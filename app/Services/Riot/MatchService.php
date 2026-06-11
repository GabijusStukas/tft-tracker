<?php

namespace App\Services\Riot;

use App\Enums\DataDragonIconType;
use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotMatch;
use App\Repositories\Riot\RiotMatchParticipantRepository;
use App\Repositories\Riot\RiotMatchParticipantUnitRepository;
use App\Repositories\Riot\RiotMatchRepository;
use Carbon\Carbon;

class MatchService
{
    /**
     * @param RiotMatchRepository $riotMatchRepository
     * @param RiotMatchParticipantRepository $riotMatchParticipantRepository
     * @param RiotMatchParticipantUnitRepository $riotMatchParticipantUnitRepository
     * @param DataDragonService $dataDragonService
     */
    public function __construct(
        private RiotMatchRepository $riotMatchRepository,
        private RiotMatchParticipantRepository $riotMatchParticipantRepository,
        private RiotMatchParticipantUnitRepository $riotMatchParticipantUnitRepository,
        private DataDragonService $dataDragonService,
    ) {
    }

    /**
     * @param array $matchData
     * @return RiotMatch
     */
    public function parseMatchData(array $matchData): RiotMatch
    {
        preg_match('/<Releases\/([^>]+)>/', $matchData['info']['game_version'], $matches);
        $gameVersion = $this->dataDragonService->getVersion($matches[1]);
        $cdnData = $this->dataDragonService->getCdnData(DataDragonIconType::TFT_QUEUES, $gameVersion);
        $queueName = $cdnData['data'][$matchData['info']['queueId']]['name'] ?? 'Unknown Queue';

        $match = $this->riotMatchRepository->createOrUpdate([
            'match_id'         => $matchData['metadata']['match_id'],
            'game_version'     => $gameVersion,
            'queue_name'       => $queueName,
            'season'           => $matchData['info']['tft_set_number'],
            'raw_data'         => json_encode($matchData),
            'match_created_at' => Carbon::createFromTimestampMs($matchData['info']['gameCreation'])->utc(),
        ]);

        $participants = [];
        foreach ($matchData['info']['participants'] as $participant) {
            $participantModel = $this->riotMatchParticipantRepository->createOrUpdate([
                'match_id' => $match->id,
                'puuid'    => $participant['puuid'],
                'game_name' => $participant['riotIdGameName'],
                'tag_line' => $participant['riotIdTagLine'],
                'level' => $participant['level'],
                'gold_left' => $participant['gold_left'],
                'placement' => $participant['placement'],
                'last_round' => $participant['last_round'],
            ]);
            $units = [];
            foreach ($participant['units'] as $unit) {
                $cdnData = $this->dataDragonService->getEntityCdnData(
                    $unit['character_id'],
                    DataDragonIconType::TFT_CHAMPION,
                    $gameVersion
                );
                $items = [];
                foreach ($unit['itemNames'] as $itemName) {
                    $itemCdnData = $this->dataDragonService->getEntityCdnData(
                        $itemName,
                        DataDragonIconType::TFT_ITEM,
                        $gameVersion
                    );
                    $items['items'][] = [
                        'name' => $itemCdnData['name'],
                        'icon' => $this->dataDragonService->getCdnImageUrl(
                            $itemName,
                            DataDragonIconType::TFT_ITEM,
                            $gameVersion
                        ),
                    ];
                }}
                $units[] = $this->riotMatchParticipantUnitRepository->createOrUpdate([
                    'participant_id' => $participantModel->id,
                    'character_id' => $unit['character_id'],
                    'name' => $cdnData['name'],
                    'icon' => $this->dataDragonService->getCdnImageUrl(
                        $unit['character_id'],
                        DataDragonIconType::TFT_CHAMPION,
                        $gameVersion
                    ),
                    'items' => json_encode($items),
                ]);
            }
            $participants[] = $participantModel;
        $match->participants()->saveMany($participants);

        dd($match);
        return $match;
    }
}

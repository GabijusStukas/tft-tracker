<?php

namespace App\Services\Riot;

use App\Enums\DataDragonIconType;
use App\Models\Riot\RiotMatch;
use App\Repositories\Riot\RiotRepositoryFactory;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Throwable;

class MatchService
{
    /**
     * @param RiotRepositoryFactory $repositoryFactory
     * @param DataDragonService $dataDragonService
     */
    public function __construct(
        private RiotRepositoryFactory $repositoryFactory,
        private DataDragonService $dataDragonService,
    ) {
    }

    /**
     * @param array $matchData
     * @return RiotMatch
     * @throws GuzzleException|Throwable
     */
    public function parseMatchData(array $matchData): RiotMatch
    {
        return DB::transaction(function () use ($matchData) {
            $gameVersion = $this->resolveGameVersion($matchData['info']['game_version']);
            $queueName = $this->resolveQueueName($matchData['info']['queueId'], $gameVersion);

            $match = $this->repositoryFactory->match()->createOrUpdate($this->buildMatchPayload($matchData, $gameVersion, $queueName));

            foreach ($matchData['info']['participants'] as $participantData) {
                $this->syncParticipant($match, $participantData, $gameVersion);
            }

            return $match;
        });
    }

    /**
     * @param string $gameVersionRaw
     * @return string
     */
    private function resolveGameVersion(string $gameVersionRaw): string
    {
        preg_match('/<Releases\/([^>]+)>/', $gameVersionRaw, $matches);

        return $this->dataDragonService->getVersion($matches[1] ?? null);
    }

    /**
     * @param int $queueId
     * @param string $gameVersion
     * @return string
     */
    private function resolveQueueName(int $queueId, string $gameVersion): string
    {
        $queueData = $this->dataDragonService->getCdnData(DataDragonIconType::TFT_QUEUES, $gameVersion);

        return $queueData['data'][$queueId]['name'] ?? 'Unknown Queue';
    }

    /**
     * @param array $matchData
     * @param string $gameVersion
     * @param string $queueName
     * @return array
     */
    private function buildMatchPayload(array $matchData, string $gameVersion, string $queueName): array
    {
        return [
            'match_id' => $matchData['metadata']['match_id'],
            'game_version' => $gameVersion,
            'queue_name' => $queueName,
            'season' => $matchData['info']['tft_set_number'],
            'raw_data' => json_encode($matchData),
            'match_created_at' => Carbon::createFromTimestampMs($matchData['info']['gameCreation'])->utc(),
        ];
    }

    /**
     * @param RiotMatch $match
     * @param array $participantData
     * @param string $gameVersion
     * @return void
     * @throws GuzzleException
     */
    private function syncParticipant(RiotMatch $match, array $participantData, string $gameVersion): void
    {
        $participant = $this->repositoryFactory->matchParticipant()->createOrUpdate([
            'match_id' => $match->id,
            'puuid' => $participantData['puuid'],
            'game_name' => $participantData['riotIdGameName'],
            'tag_line' => $participantData['riotIdTagline'],
            'level' => $participantData['level'],
            'gold_left' => $participantData['gold_left'],
            'placement' => $participantData['placement'],
            'last_round' => $participantData['last_round'],
        ]);

        $participant->units()->delete();
        $participant->traits()->delete();

        foreach ($this->buildUnitsPayload($participantData['units'] ?? [], $gameVersion) as $unitPayload) {
            $this->repositoryFactory->matchParticipantUnit()->create([
                'participant_id' => $participant->id,
                ...$unitPayload,
            ]);
        }

        foreach ($this->buildTraitsPayload($participantData['traits'] ?? [], $gameVersion) as $traitPayload) {
            $this->repositoryFactory->matchParticipantTrait()->create([
                'participant_id' => $participant->id,
                ...$traitPayload,
            ]);
        }
    }

    /**
     * @param array $unitsData
     * @param string $gameVersion
     * @return array
     * @throws GuzzleException
     */
    private function buildUnitsPayload(array $unitsData, string $gameVersion): array
    {
        $units = [];

        foreach ($unitsData as $unitData) {
            $unitMetadata = $this->resolveEntityMetadata(
                $unitData['character_id'],
                DataDragonIconType::TFT_CHAMPION,
                $gameVersion
            );

            $units[] = [
                'character_id' => $unitData['character_id'],
                'name' => $unitMetadata['name'],
                'tier' => $unitData['tier'],
                'rarity' => $unitData['rarity'],
                'icon' => $unitMetadata['icon'],
                'items' => $this->buildUnitItemsPayload($unitData['itemNames'] ?? [], $gameVersion),
            ];
        }

        return $units;
    }

    /**
     * @param array $itemNames
     * @param string $gameVersion
     * @return array
     * @throws GuzzleException
     */
    private function buildUnitItemsPayload(array $itemNames, string $gameVersion): array
    {
        $items = [];

        foreach ($itemNames as $itemName) {
            $items[] = $this->resolveEntityMetadata($itemName, DataDragonIconType::TFT_ITEM, $gameVersion);
        }

        return $items;
    }

    /**
     * @param array $traitsData
     * @param string $gameVersion
     * @return array
     * @throws GuzzleException
     */
    private function buildTraitsPayload(array $traitsData, string $gameVersion): array
    {
        $traits = [];

        foreach ($traitsData as $traitData) {
            $traitMetadata = $this->resolveEntityMetadata($traitData['name'], DataDragonIconType::TFT_TRAIT, $gameVersion);

            $traits[] = [
                'trait_id' => $traitData['name'],
                'name' => $traitMetadata['name'] ?? $traitData['name'],
                'style' => $traitData['style'],
                'num_units' => $traitData['num_units'],
                'tier_total' => $traitData['tier_total'],
                'tier_current' => $traitData['tier_current'],
                'icon' => $traitMetadata['icon'],
            ];
        }

        return $traits;
    }

    /**
     * @param string $entityId
     * @param DataDragonIconType $iconType
     * @param string $gameVersion
     * @return array{name: string|null, icon: string|null}
     * @throws GuzzleException
     */
    private function resolveEntityMetadata(string $entityId, DataDragonIconType $iconType, string $gameVersion): array
    {
        $entityData = $this->dataDragonService->getEntityCdnData($entityId, $iconType, $gameVersion);

        return [
            'name' => $entityData['name'] ?? null,
            'icon' => $this->dataDragonService->getCdnImageUrl($entityId, $iconType, $gameVersion),
        ];
    }
}

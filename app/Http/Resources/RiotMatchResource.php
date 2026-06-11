<?php

namespace App\Http\Resources;

use App\Enums\DataDragonIconType;
use App\Services\Riot\DataDragonService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class RiotMatchResource extends JsonResource
{
    /**
     * @var DataDragonService|\Illuminate\Foundation\Application|mixed|object
     */
    protected DataDragonService $dataDragonService;

    /**
     * @param $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->dataDragonService = app(DataDragonService::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->raw_data;
        $participants = $data['info']['participants'] ?? [];
        $data['info']['participants'] = $this->appendDataDragon($participants);

        return [
            'puuid'            => $this->account->puuid,
            'match_id'         => $this->match_id,
            'raw_data'         => $data,
            'match_created_at' => $this->match_created_at?->utc()->toISOString(),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $participants
     * @return array<int, array<string, mixed>>
     */
    protected function appendDataDragon(array $participants): array
    {
        return array_map(function (array $participant): array {
            $participant['units'] = array_map(function (array $unit): array {
                $data = $this->withDataDragon($unit['character_id'], DataDragonIconType::TFT_CHAMPION);

                foreach ($unit['itemNames'] as $itemName) {
                    $data['items'][] = $this->withDataDragon($itemName, DataDragonIconType::TFT_ITEM);
                }

                return array_merge($unit, $data);
            }, $participant['units']);

            $participant['traits'] = array_map(function (array $trait): array {
                $data = $this->withDataDragon($trait['name'], DataDragonIconType::TFT_TRAIT);
                return array_merge($trait, $data);
            }, $participant['traits']);

            return $participant;
        }, $participants);
    }

    /**
     * @param string $entityId
     * @param DataDragonIconType $type
     * @return array<string, mixed>
     */
    protected function withDataDragon(string $entityId, DataDragonIconType $type): array
    {
        try {
            $cdnData = $this->dataDragonService->getEntityCdnData($entityId, $type, $this->game_version);

            $unit['name'] = $cdnData['name'];
            $unit['icon'] = $this->dataDragonService->getCdnImageUrl($entityId, $type, $this->game_version);
        } catch (Throwable) {
            $unit['icon'] = null;
        }

        return $unit;
    }

    /**
     * @param string $queueId
     * @return string
     */
    protected function getQueueName(string $queueId): string
    {
        $queueData = $this->dataDragonService->getCdnData(DataDragonIconType::TFT_QUEUES, $this->game_version);

        return $queueData['data'][$queueId]['name'] ?? 'Unknown Queue';
    }
}

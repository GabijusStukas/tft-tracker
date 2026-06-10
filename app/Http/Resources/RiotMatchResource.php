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
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->raw_data;
        $participants = $data['info']['participants'] ?? [];
        $data['info']['participants'] = $this->withUnitIcons($participants);

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
    protected function withUnitIcons(array $participants): array
    {
        $dataDragonService = app(DataDragonService::class);

        return array_map(function (array $participant) use ($dataDragonService): array {
            $participant['units'] = array_map(function (array $unit) use ($dataDragonService): array {
                $data = $this->withDataDragon($unit['character_id'], $dataDragonService, DataDragonIconType::TFT_CHAMPION);
                return array_merge($unit, $data);
            }, $participant['units']);

            $participant['traits'] = array_map(function (array $trait) use ($dataDragonService): array {
                $data = $this->withDataDragon($trait['name'], $dataDragonService, DataDragonIconType::TFT_TRAIT);
                return array_merge($trait, $data);
            }, $participant['traits']);

            return $participant;
        }, $participants);
    }

    /**
     * @param string $entityId
     * @param DataDragonService $dataDragonService
     * @param DataDragonIconType $type
     * @return array<string, mixed>
     */
    protected function withDataDragon(string $entityId, DataDragonService $dataDragonService, DataDragonIconType $type): array
    {
        try {
            $cdnData = $dataDragonService->getEntityCdnData($entityId, $type);

            $unit['name'] = $cdnData['name'];
            $unit['icon'] = $dataDragonService->getCdnImageUrl($entityId, $type);
        } catch (Throwable) {
            $unit['icon'] = null;
        }

        return $unit;
    }
}

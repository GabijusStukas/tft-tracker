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
    private function withUnitIcons(array $participants): array
    {
        $dataDragonService = app(DataDragonService::class);

        return array_map(function (array $participant) use ($dataDragonService): array {
            $units = $participant['units'] ?? [];

            $participant['units'] = array_map(
                fn (array $unit): array => $this->withIcon($unit, $dataDragonService),
                is_array($units) ? $units : []
            );

            return $participant;
        }, $participants);
    }

    /**
     * @param array<string, mixed> $unit
     * @param DataDragonService $dataDragonService
     * @return array<string, mixed>
     */
    private function withIcon(array $unit, DataDragonService $dataDragonService): array
    {
        $characterId = $unit['character_id'] ?? null;


        if (! is_string($characterId) || $characterId === '') {
            $unit['icon'] = null;
            return $unit;
        }

        try {
            $cdnData = $dataDragonService->getEntityCdnData(
                $characterId,
                DataDragonIconType::TFT_CHAMPION
            );

            $unit['name'] = $cdnData['name'];
            $unit['icon'] = $dataDragonService->getCdnImageUrl($characterId, DataDragonIconType::TFT_CHAMPION);
        } catch (Throwable) {
            $unit['icon'] = null;
        }

        return $unit;
    }
}

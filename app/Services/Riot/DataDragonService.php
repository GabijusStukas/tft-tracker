<?php

namespace App\Services\Riot;

use App\Enums\DataDragonIconType;
use App\Services\Riot\API\DataDragonClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DataDragonService
{
	public function __construct(private DataDragonClient $dataDragonClient)
	{
	}

    /**
     * @param string|null $version
     * @return string
     */
    public function getVersion(?string $version = null): string
    {
        $versions = Cache::remember('riot.datadragon.versions', now()->addHour(), function () {
            return $this->dataDragonClient->request('GET', 'api/versions.json');
        });

        if ( is_null($version) ) {
            return $versions[0];
        }

        if (array_key_exists($version, $versions)) {
            return $version;
        }

        /** @var string $result */
        $result = collect($versions)
            ->first(fn (string $item) => $item === $version || str_starts_with($item, $version));

        return $result;
    }

    /**
     * @param DataDragonIconType $iconType
     * @param string $version
     * @return array
     */
    public function getCdnData(DataDragonIconType $iconType, string $version): array
    {
        return Cache::remember(
            'riot.datadragon.cdn_data.' . $version . '.' . $iconType->value,
            now()->addHours(12),
            function () use ($version, $iconType) {
                return $this->dataDragonClient->request(
                    'GET',
                    "cdn/$version/data/en_US/$iconType->value.json"
                );
            }
        );
    }

    /**
     * @param int $profileIconId
     * @param string|null $version
     * @return string
     * @throws GuzzleException
     */
    public function getSummonerIconUrl(int $profileIconId, ?string $version = null): string
    {
        $resolvedVersion = $version ?? $this->getVersion();
        $filename = sprintf('%d.png', $profileIconId);
        $relativePath = sprintf('riot/%s/images/%s', $resolvedVersion, $filename);
        $disk = Storage::disk('public');

        if (! $disk->exists($relativePath)) {
            $response = $this->dataDragonClient->requestImage('GET', sprintf('cdn/%s/img/profileicon/%s', $resolvedVersion, $filename), [
                'headers' => [
                    'Accept' => 'image/png',
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Unable to download Data Dragon profile icon.');
            }

            $disk->put($relativePath, $response->getBody()->getContents());
        }

        return $disk->url($relativePath);
    }

    /**
     * @param string $iconId
     * @param DataDragonIconType $iconType
     * @param string $version
     * @return string|null
     * @throws GuzzleException
     */
    public function getCdnImageUrl(string $iconId, DataDragonIconType $iconType, string $version): ?string
    {
        $relativePath = $this->buildCdnImageRelativePath($version, $iconType, $iconId);
        $disk = Storage::disk('public');

        if (! $disk->exists($relativePath)) {
            try {
                $championIcon = $this->resolveCdnImageFilename($iconType, $iconId, $version);
            } catch (RuntimeException $e) {
                Log::warning('Unable to resolve Data Dragon icon filename.', [
                    'icon_id' => $iconId,
                    'icon_type' => $iconType->value,
                    'version' => $version,
                    'exception' => $e->getMessage(),
                ]);
                return null;
            }
            $response = $this->dataDragonClient->requestImage(
                'GET',
                sprintf('cdn/%s/img/%s/%s', $version, $iconType->value, $championIcon),
                [
                    'headers' => [
                        'Accept' => 'image/png',
                    ]
                ]
            );

            if ($response->getStatusCode() !== 200) {
                Log::warning('Unable to download Data Dragon profile icon.', [
                    'icon_id' => $iconId,
                    'icon_type' => $iconType->value,
                    'version' => $version,
                ]);
                return null;
            }

            $disk->put($relativePath, $response->getBody()->getContents());
        }

        return $disk->url($relativePath);
    }

    /**
     * @param string $version
     * @param DataDragonIconType $iconType
     * @param string $normalizedIconId
     * @return string
     */
    private function buildCdnImageRelativePath(string $version, DataDragonIconType $iconType, string $normalizedIconId): string
    {
        $filename = sprintf('%s.png', $normalizedIconId);

        return sprintf('riot/%s/images/%s/%s', $version, $iconType->value, $filename);
    }

    /**
     * @param DataDragonIconType $iconType
     * @param string $iconId
     * @param string $version
     * @return string
     */
    private function resolveCdnImageFilename(DataDragonIconType $iconType, string $iconId, string $version): string
    {
        $cdnEntityData = $this->getEntityCdnData($iconId, $iconType, $version);

        if (empty($cdnEntityData)) {
            throw new RuntimeException(sprintf('Unable to resolve icon metadata for %s.', $iconId));
        }

        return $cdnEntityData['image']['full'];
    }

    /**
     * @param string $entityId
     * @param DataDragonIconType $type
     * @param string $version
     * @return array|null
     */
    public function getEntityCdnData(string $entityId, DataDragonIconType $type, string $version): ?array
    {
        $cdnData = $this->getCdnData($type, $version);

        $championData = $cdnData['data'];

        return array_find($championData, fn (array $champion) => strtolower($champion['id']) === strtolower($entityId));
    }
}

<?php

namespace App\Services\Riot;

use App\Enums\DataDragonIconType;
use App\Services\Riot\API\DataDragonClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DataDragonService
{
	public function __construct(private DataDragonClient $dataDragonClient)
	{
	}

	/**
	 * @return string
	 */
    public function getLatestVersion(): string
    {
        return Cache::remember('riot.datadragon.latest_version', now()->addHour(), function () {
            $versions = $this->dataDragonClient->request('GET', 'api/versions.json');

            if (empty($versions[0])) {
                throw new RuntimeException('Invalid Data Dragon versions response.');
            }

            return (string) $versions[0];
        });
    }

    /**
     * @param string $version
     * @param DataDragonIconType $iconType
     * @return array
     */
    public function getCdnData(string $version, DataDragonIconType $iconType): array
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
        $resolvedVersion = $version ?? $this->getLatestVersion();
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
     * @return string
     * @throws GuzzleException
     */
    public function getCdnImageUrl(string $iconId, DataDragonIconType $iconType): string
    {
        $resolvedVersion = $this->getLatestVersion();
        $relativePath = $this->buildCdnImageRelativePath($resolvedVersion, $iconType, $iconId);
        $disk = Storage::disk('public');

        if (! $disk->exists($relativePath)) {
            $championIcon = $this->resolveCdnImageFilename($iconType, $iconId, $resolvedVersion);
            $response = $this->dataDragonClient->requestImage(
                'GET',
                sprintf('cdn/%s/img/%s/%s', $resolvedVersion, $iconType->value, $championIcon),
                [
                    'headers' => [
                        'Accept' => 'image/png',
                    ]
                ]
            );

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException('Unable to download Data Dragon profile icon.');
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
     * @param string|null $version
     * @return array|null
     */
    public function getEntityCdnData(string $entityId, DataDragonIconType $type, string $version = null): ?array
    {
        if (! $version ) {
            $version = $this->getLatestVersion();
        }
        $cdnData = $this->getCdnData($version, $type);

        $championData = $cdnData['data'];

        return array_find($championData, fn (array $champion) => strtolower($champion['id']) === strtolower($entityId));
    }
}

<?php

namespace App\Services\Riot;

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
        return Cache::remember('riot.datadragon.latest_version', now()->addHours(12), function () {
            $versions = $this->dataDragonClient->request('GET', 'api/versions.json');

            if (empty($versions[0])) {
                throw new RuntimeException('Invalid Data Dragon versions response.');
            }

            return (string) $versions[0];
        });
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
}

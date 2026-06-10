<?php

namespace App\Services\Riot;

use App\DTO\RiotAccountSearchDTO;
use App\Http\Exceptions\RiotApiException;
use App\Models\RiotAccount;
use App\Models\RiotSummoner;
use App\Repositories\RiotMatchRepository;
use App\Repositories\RiotRegionRepository;
use App\Repositories\RiotAccountRepository;
use App\Repositories\RiotSummonerRepository;
use App\Services\Riot\API\AccountService;
use App\Services\Riot\API\RiotServiceFactory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RiotService
{
    /**
     * @param RiotServiceFactory $serviceFactory
     * @param RiotAccountRepository $riotAccountRepository
     * @param RiotRegionRepository $riotRegionRepository
     * @param RiotMatchRepository $riotMatchRepository
     */
    public function __construct(
        private RiotServiceFactory     $serviceFactory,
        private RiotAccountRepository  $riotAccountRepository,
        private RiotRegionRepository   $riotRegionRepository,
        private RiotMatchRepository    $riotMatchRepository,
        private RiotSummonerRepository $riotSummonerRepository
    ) {
    }

    /**
     * Get summoner information by summoner name
     *
     * @param RiotAccountSearchDTO $DTO
     * @return RiotAccount
     * @throws RiotApiException
     */
    public function getSummonerByName(RiotAccountSearchDTO $DTO): RiotAccount
    {
        $account = $this->riotAccountRepository->getAccount($DTO);

        if ($account && $DTO->shouldRefresh() === false) {
            return $account;
        }

        $accountService = $this->serviceFactory->account();
        $accountData    = $accountService->getAccountByName($DTO->getUsername(), $DTO->getTagLine());
        $accountData    = array_merge($accountData, $accountService->getRegionByGame($accountData['puuid']));

        return $this->riotAccountRepository->createOrUpdateAccount(
            $accountData
        );
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return Collection
     * @throws RiotApiException
     */
    public function getSummonerMatches(RiotAccountSearchDTO $DTO): Collection
    {
        $account = $this->getSummonerByName($DTO);

        if ( $account->matches->isNotEmpty() && $DTO->shouldRefresh() === false ) {
            return $this->riotMatchRepository->getMatchesByAccountId($account->id);
        }

        $tftService = $this->serviceFactory->tft($this->riotRegionRepository->getClusterByRegion($account->region));

        $matches = $tftService->getMatchesByPuuid($account->puuid);

        $result = [];
        foreach ($matches as $match) {
            $matchData = $tftService->getMatch($match);
            $result[] = [
                'account_id'       => $account->id,
                'match_id'         => $match,
                'raw_data'         => json_encode($matchData),
                'match_created_at' => Carbon::createFromTimestampMs($matchData['info']['gameCreation'])->utc(),
            ];
        }

        return $this->riotMatchRepository->upsert($result);
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return RiotSummoner
     * @throws RiotApiException
     */
    public function getSummonerDetails(RiotAccountSearchDTO $DTO): RiotSummoner
    {
        $account = $this->getSummonerByName($DTO);

        if ( $account->summoner && $DTO->shouldRefresh() === false ) {
            return $account->summoner;
        }

        $summonerData = $this->serviceFactory->summoner($account->region)->getSummonerDetails($account->puuid);

        return $this->riotSummonerRepository->createOrUpdate($account, $summonerData);
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return RiotSummoner
     * @throws RiotApiException
     */
    public function getAccountLeague(RiotAccountSearchDTO $DTO): RiotSummoner
    {
        $account = $this->getSummonerByName($DTO);

        if ( $DTO->shouldRefresh() === false ) {
            return $this->riotSummonerRepository->getByAccountId($account->id);
        }

        $summonerData = $this->serviceFactory->summoner($account->region)->getSummonerDetails($account->puuid);

        return $this->riotSummonerRepository->createOrUpdate($account, $summonerData);
    }
}

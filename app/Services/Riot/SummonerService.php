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
use Illuminate\Support\Collection;

class SummonerService
{
    /**
     * @var AccountService
     */
    private AccountService $accountService;

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

        if ($account) {
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

        $matches = $this->serviceFactory
            ->tft($this->riotRegionRepository->getClusterByRegion($account->region))
            ->getMatchesByPuuid($account->puuid);

        return $this->riotMatchRepository->upsert($matches, $account);
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return RiotSummoner
     * @throws RiotApiException
     */
    public function getSummonerDetails(RiotAccountSearchDTO $DTO): RiotSummoner
    {
        $account = $this->getSummonerByName($DTO);

        $summonerData = $this->serviceFactory->summoner($account->region)->getSummonerDetails($account->puuid);

        return $this->riotSummonerRepository->createOrUpdate($account, $summonerData);
    }
}

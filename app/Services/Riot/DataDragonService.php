<?php

namespace App\Services\Riot;

use App\DTO\RiotAccountSearchDTO;
use App\Http\Exceptions\RiotApiException;
use App\Models\RiotAccount;
use App\Repositories\RiotMatchRepository;
use App\Repositories\RiotRegionRepository;
use App\Repositories\RiotAccountRepository;
use App\Services\Riot\API\AccountService;
use App\Services\Riot\API\RiotServiceFactory;

class DataDragonService
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
        private RiotServiceFactory    $serviceFactory,
        private RiotAccountRepository $riotAccountRepository,
        private RiotRegionRepository  $riotRegionRepository,
        private RiotMatchRepository   $riotMatchRepository
    ) {
        $this->accountService = $serviceFactory->account();
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

        $accountData = $this->accountService->getAccountByName($DTO->getUsername(), $DTO->getTagLine());
        $accountData = array_merge($accountData, $this->accountService->getRegionByGame($accountData['puuid']));

        return $this->riotAccountRepository->createOrUpdateAccount(
            $accountData
        );
    }
}

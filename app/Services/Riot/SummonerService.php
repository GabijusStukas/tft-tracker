<?php

namespace App\Services\Riot;

use App\DTO\SummonerSearchDTO;
use App\Http\Exceptions\RiotApiException;
use App\Models\Summoner;
use App\Repositories\SummonerRepository;
use App\Services\Riot\API\AccountService;
use App\Services\Riot\API\RiotServiceFactory;

class SummonerService
{
    /**
     * @var AccountService
     */
    private AccountService $accountService;

    /**
     * @param RiotServiceFactory $serviceFactory
     * @param SummonerRepository $repository
     */
    public function __construct(RiotServiceFactory $serviceFactory, private SummonerRepository $repository)
    {
        $this->accountService = $serviceFactory->account();
    }

    /**
     * Get summoner information by summoner name
     *
     * @param SummonerSearchDTO $DTO
     * @return Summoner
     * @throws RiotApiException
     */
    public function getSummonerByName(SummonerSearchDTO $DTO): Summoner
    {
        $summoner = $this->repository->getSummoner($DTO);

        if ($summoner) {
            return $summoner;
        }

        $summonerData = $this->accountService->getSummonerByName($DTO->getUsername(), $DTO->getTagLine());
        $summonerData = array_merge($summonerData, $this->accountService->getRegionByGame($summonerData['puuid']));

        return $this->repository->createOrUpdateSummoner(
            $summonerData
        );
    }
}

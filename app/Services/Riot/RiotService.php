<?php

namespace App\Services\Riot;

use App\DTO\RiotAccountSearchDTO;
use App\Http\Exceptions\RiotApiException;
use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotLeague;
use App\Models\Riot\RiotSummoner;
use App\Repositories\Riot\RiotAccountRepository;
use App\Repositories\Riot\RiotLeagueRepository;
use App\Repositories\Riot\RiotMatchRepository;
use App\Repositories\Riot\RiotRegionRepository;
use App\Repositories\Riot\RiotSummonerRepository;
use App\Services\Riot\API\RiotServiceFactory;
use Illuminate\Support\Collection;

class RiotService
{
    /**
     * @param RiotServiceFactory $serviceFactory
     * @param RiotAccountRepository $riotAccountRepository
     * @param RiotRegionRepository $riotRegionRepository
     * @param RiotMatchRepository $riotMatchRepository
     * @param RiotSummonerRepository $riotSummonerRepository
     * @param RiotLeagueRepository $riotLeagueRepository
     * @param DataDragonService $dataDragonService
     * @param MatchService $matchService
     */
    public function __construct(
        private RiotServiceFactory     $serviceFactory,
        private RiotAccountRepository  $riotAccountRepository,
        private RiotRegionRepository   $riotRegionRepository,
        private RiotMatchRepository    $riotMatchRepository,
        private RiotSummonerRepository $riotSummonerRepository,
        private RiotLeagueRepository   $riotLeagueRepository,
        private DataDragonService      $dataDragonService,
        private MatchService           $matchService,
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

        if ( $account->participants->isNotEmpty() && $DTO->shouldRefresh() === false ) {
            return $this->riotMatchRepository->getMatchesByAccountId($account->id);
        }

        $tftService = $this->serviceFactory->tft($this->riotRegionRepository->getClusterByRegion($account->region));

        $matches = $tftService->getMatchesByPuuid($account->puuid);

        $result = [];
        foreach ($matches as $match) {
            $matchData = $tftService->getMatch($match);
            $result[] = $this->matchService->parseMatchData($matchData);
        }

        return $result;
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
     * @return Collection<int, RiotLeague>
     * @throws RiotApiException
     */
    public function getAccountLeague(RiotAccountSearchDTO $DTO): Collection
    {
        $account = $this->getSummonerByName($DTO);

        if ( $account->leagues->isNotEmpty() && $DTO->shouldRefresh() === false ) {
            return $this->riotLeagueRepository->getByAccountId($account->id);
        }

        $leagueData = $this->serviceFactory->tftLeague($account->region)->getLeagues($account->puuid);

        $result = collect();
        foreach ($leagueData as $league) {
            $result[] = $this->riotLeagueRepository->createOrUpdate([
                'account_id'       => $account->id,
                'queue_type'       => $league['queueType'],
                'tier'             => $league['tier'],
                'rank'             => $league['rank'],
                'league_points'    => $league['leaguePoints'],
                'wins'             => $league['wins'],
                'losses'           => $league['losses'],
            ]);
        }

        return $result;
    }
}

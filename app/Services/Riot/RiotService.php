<?php

namespace App\Services\Riot;

use App\DTO\RiotAccountSearchDTO;
use App\Http\Exceptions\RiotApiException;
use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotLeague;
use App\Models\Riot\RiotMatch;
use App\Models\Riot\RiotSummoner;
use App\Repositories\Riot\RiotRepositoryFactory;
use App\Services\Riot\API\RiotServiceFactory;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Throwable;

class RiotService
{
    /**
     * @param RiotServiceFactory $serviceFactory
     * @param RiotRepositoryFactory $repositoryFactory
     * @param MatchService $matchService
     */
    public function __construct(
        private RiotServiceFactory $serviceFactory,
        private RiotRepositoryFactory $repositoryFactory,
        private MatchService $matchService,
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
        $account = $this->repositoryFactory->account()->getAccount($DTO);

        if ($account && $DTO->shouldRefresh() === false) {
            return $account;
        }

        $accountService = $this->serviceFactory->account();
        $accountData    = $accountService->getAccountByName($DTO->getUsername(), $DTO->getTagLine());
        $accountData    = array_merge($accountData, $accountService->getRegionByGame($accountData['puuid']));

        return $this->repositoryFactory->account()->createOrUpdateAccount(
            $accountData
        );
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return Collection
     * @throws RiotApiException
     * @throws GuzzleException
     * @throws Throwable
     */
    public function getSummonerMatches(RiotAccountSearchDTO $DTO): Collection
    {
        $originalRefresh = $DTO->shouldRefresh();
        $DTO->setRefresh(false);
        $account = $this->getSummonerByName($DTO);
        $DTO->setRefresh($originalRefresh);

        if ( $account->participants->isNotEmpty() && $DTO->shouldRefresh() === false ) {
            return $this->repositoryFactory->match()->getMatchesByPuuid($account->puuid);
        }

        $tftService = $this->serviceFactory->tft($this->repositoryFactory->region()->getClusterByRegion($account->region));

        $matches = $tftService->getMatchesByPuuid($account->puuid);
        $missingMatches = $this->repositoryFactory->match()->getMissingMatchIds($matches);

        foreach ($missingMatches as $match) {
            $matchData = $tftService->getMatch($match);
            $matchData['info']['participants'] = array_filter($matchData['info']['participants'], function ($participant) use ($account) {
                return $participant['puuid'] === $account->puuid;
            });
            $this->matchService->parseMatchData($matchData);
        }

        return $this->repositoryFactory->match()->getMatchesByPuuid($account->puuid);
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

        return $this->repositoryFactory->summoner()->createOrUpdate($account, $summonerData);
    }

    /**
     * @param RiotAccountSearchDTO $DTO
     * @return Collection<int, RiotLeague>
     * @throws RiotApiException
     */
    public function getAccountLeague(RiotAccountSearchDTO $DTO): Collection
    {
        $originalRefresh = $DTO->shouldRefresh();
        $DTO->setRefresh(false);
        $account = $this->getSummonerByName($DTO);
        $DTO->setRefresh($originalRefresh);

        if ( $account->leagues->isNotEmpty() && $DTO->shouldRefresh() === false ) {
            return $this->repositoryFactory->league()->getByAccountId($account->id);
        }

        $leagueData = $this->serviceFactory->tftLeague($account->region)->getLeagues($account->puuid);

        $result = collect();
        foreach ($leagueData as $league) {
            $result[] = $this->repositoryFactory->league()->createOrUpdate([
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

    /**
     * @param string $matchId
     * @return RiotMatch|null
     * @throws GuzzleException
     * @throws RiotApiException
     * @throws Throwable
     */
    public function getMatchDetails(string $matchId): ?RiotMatch
    {
        $match = $this->repositoryFactory->match()->getMatchById($matchId);

        if ($match->participants->count() !== RiotMatch::MAX_PARTICIPANTS) {
            $matchData = $this->serviceFactory->tft()->getMatch($matchId);
            $match = $this->matchService->parseMatchData($matchData);
        }

        return $match;
    }
}

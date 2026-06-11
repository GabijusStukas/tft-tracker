<?php

namespace App\Repositories\Riot;

class RiotRepositoryFactory
{
    /**
     * @param RiotAccountRepository $riotAccountRepository
     * @param RiotRegionRepository $riotRegionRepository
     * @param RiotMatchRepository $riotMatchRepository
     * @param RiotSummonerRepository $riotSummonerRepository
     * @param RiotLeagueRepository $riotLeagueRepository
     * @param RiotMatchParticipantRepository $riotMatchParticipantRepository
     * @param RiotMatchParticipantTraitRepository $riotMatchParticipantTraitRepository
     * @param RiotMatchParticipantUnitRepository $riotMatchParticipantUnitRepository
     */
    public function __construct(
        private RiotAccountRepository $riotAccountRepository,
        private RiotRegionRepository $riotRegionRepository,
        private RiotMatchRepository $riotMatchRepository,
        private RiotSummonerRepository $riotSummonerRepository,
        private RiotLeagueRepository $riotLeagueRepository,
        private RiotMatchParticipantRepository $riotMatchParticipantRepository,
        private RiotMatchParticipantTraitRepository $riotMatchParticipantTraitRepository,
        private RiotMatchParticipantUnitRepository $riotMatchParticipantUnitRepository,
    ) {
    }

    /**
     * @return RiotAccountRepository
     */
    public function account(): RiotAccountRepository
    {
        return $this->riotAccountRepository;
    }

    /**
     * @return RiotRegionRepository
     */
    public function region(): RiotRegionRepository
    {
        return $this->riotRegionRepository;
    }

    /**
     * @return RiotMatchRepository
     */
    public function match(): RiotMatchRepository
    {
        return $this->riotMatchRepository;
    }

    /**
     * @return RiotSummonerRepository
     */
    public function summoner(): RiotSummonerRepository
    {
        return $this->riotSummonerRepository;
    }

    /**
     * @return RiotLeagueRepository
     */
    public function league(): RiotLeagueRepository
    {
        return $this->riotLeagueRepository;
    }

    /**
     * @return RiotMatchParticipantRepository
     */
    public function matchParticipant(): RiotMatchParticipantRepository
    {
        return $this->riotMatchParticipantRepository;
    }

    /**
     * @return RiotMatchParticipantTraitRepository
     */
    public function matchParticipantTrait(): RiotMatchParticipantTraitRepository
    {
        return $this->riotMatchParticipantTraitRepository;
    }

    /**
     * @return RiotMatchParticipantUnitRepository
     */
    public function matchParticipantUnit(): RiotMatchParticipantUnitRepository
    {
        return $this->riotMatchParticipantUnitRepository;
    }
}


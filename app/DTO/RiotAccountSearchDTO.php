<?php

namespace App\DTO;

class RiotAccountSearchDTO
{
    /**
     * @param string $game
     * @param string $region
     * @param string $username
     * @param string|null $tagLine
     * @param bool $refresh
     */
    public function __construct(
        private string $game,
        private string $region,
        private string $username,
        private ?string $tagLine = null,
        private bool $refresh = false,
    ) {
    }

    /**
     * @return string
     */
    public function getGame(): string
    {
        return $this->game;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getTagLine(): ?string
    {
        return $this->tagLine;
    }

    /**
     * @return bool
     */
    public function shouldRefresh(): bool
    {
        return $this->refresh;
    }

    /**
     * @param bool $refresh
     * @return RiotAccountSearchDTO
     */
    public function setRefresh(bool $refresh): self
    {
        $this->refresh = $refresh;
        return $this;
    }
}

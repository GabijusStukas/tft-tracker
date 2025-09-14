<?php

namespace App\DTO;

class RiotAccountSearchDTO
{
    /**
     * @param string $username
     * @param string|null $tagLine
     */
    public function __construct(private string $username, private ?string $tagLine = null)
    {
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
}

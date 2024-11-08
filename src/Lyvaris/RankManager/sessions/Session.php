<?php

namespace Lyvaris\RankManager\sessions;

class Session {
    private string $playerName;
    private ?string $staffRank;
    private ?string $mediaRank;
    private ?string $vipRank;
    private array $temporaryRanks;

    public function __construct(string $playerName) {
        $this->playerName = $playerName;
        $this->staffRank = null;
        $this->mediaRank = null;
        $this->vipRank = null;
        $this->temporaryRanks = [];
    }

    public function getPlayerName(): string {
        return $this->playerName;
    }

    public function getStaffRank(): ?string {
        return $this->staffRank;
    }

    public function setStaffRank(?string $rank): void {
        $this->staffRank = $rank;
    }

    public function getMediaRank(): ?string {
        return $this->mediaRank;
    }

    public function setMediaRank(?string $rank): void {
        $this->mediaRank = $rank;
    }

    public function getVipRank(): ?string {
        return $this->vipRank;
    }

    public function setVipRank(?string $rank): void {
        $this->vipRank = $rank;
    }

    public function getTemporaryRanks(): array {
        return $this->temporaryRanks;
    }

    public function setTemporaryRank(string $rankName, int $expiryTimestamp): void {
        $this->temporaryRanks[$rankName] = $expiryTimestamp;
    }

    public function removeTemporaryRank(string $rankName): void {
        unset($this->temporaryRanks[$rankName]);
    }

    public function checkTemporaryRanks(): void {
        $currentTime = time();
        foreach ($this->temporaryRanks as $rankName => $expiryTimestamp) {
            if ($currentTime >= $expiryTimestamp) {
                unset($this->temporaryRanks[$rankName]);
            }
        }
    }
}

<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use pocketmine\player\Player;

class RankAssignedEvent extends Event implements Cancellable
{
    private Player $player;
    private string $rankName;
    private ?int $expiryTime;
    private bool $isCancelled = false;

    public function __construct(Player $player, string $rankName, ?int $expiryTime = null)
    {
        $this->player = $player;
        $this->rankName = $rankName;
        $this->expiryTime = $expiryTime;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getRankName(): string
    {
        return $this->rankName;
    }

    public function getExpiryTime(): ?int
    {
        return $this->expiryTime;
    }

    public function isTemporary(): bool
    {
        return $this->expiryTime !== null;
    }

    public function getFormattedExpiryTime(): string
    {
        if ($this->expiryTime === null) {
            return "Permanent";
        }

        $remainingTime = $this->expiryTime - time();
        $minutes = intdiv($remainingTime, 60);
        $seconds = $remainingTime % 60;

        return "$minutes minutes and $seconds seconds remaining";
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function setCancelled(bool $cancelled = true): void
    {
        $this->isCancelled = $cancelled;
    }
}

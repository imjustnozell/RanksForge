<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\player\Player;

class TemporaryRankExpireEvent extends Event  implements Cancellable
{
    private Player $player;
    private string $rankName;
    private bool $isCancelled = false;

    public function __construct(Player $player, string $rankName)
    {
        $this->player = $player;
        $this->rankName = $rankName;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getRankName(): string
    {
        return $this->rankName;
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

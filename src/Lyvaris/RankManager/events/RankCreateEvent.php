<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use Lyvaris\RankManager\utils\Rank;
use pocketmine\player\Player;

class RankCreateEvent extends Event implements Cancellable
{
    public const EVENT_NAME = "RankCreateEvent";

    private Rank $rank;

    private Player $creator;

    private bool $isCancelled = false;

    public function __construct(Rank $rank, Player $creator)
    {
        $this->rank = $rank;
        $this->creator = $creator;
    }

    public function getRank(): Rank
    {
        return $this->rank;
    }

    public function getCreator(): Player
    {
        return $this->creator;
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

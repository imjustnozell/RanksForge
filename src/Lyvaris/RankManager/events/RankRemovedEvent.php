<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use Lyvaris\RankManager\utils\Rank;

class RankRemoveEvent extends Event implements Cancellable
{
    private Rank $rank;
    private bool $isCancelled = false;

    public function __construct(Rank $rank)
    {
        $this->rank = $rank;
    }

    public function getRank(): Rank
    {
        return $this->rank;
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

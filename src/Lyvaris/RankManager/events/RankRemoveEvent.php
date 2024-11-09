<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use Lyvaris\RankManager\utils\Rank;
use pocketmine\player\Player;

class RankRemoveEvent extends Event implements Cancellable
{
    private Rank $rank;
    private bool $isCancelled = false;
    private Player $remover;

    public function __construct(Rank $rank, Player $remover)
    {
        $this->rank = $rank;
        $this->remover = $remover;
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

    public function getRemover(): Player
    {
        return $this->remover;
    }
}

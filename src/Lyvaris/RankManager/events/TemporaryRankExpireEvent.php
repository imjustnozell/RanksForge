<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Event;
use pocketmine\player\Player;

class TemporaryRankExpireEvent extends Event
{
    private Player $player;
    private string $rankName;

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
}

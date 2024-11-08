<?php

namespace Lyvaris\RankManager\placeholders;

use Nozell\PlaceholderAPI\placeholders\PlayerPlaceholder;
use pocketmine\player\Player;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\sessions\SessionManager;

class VipRankPlaceholder extends PlayerPlaceholder
{

    public function getIdentifier(): string
    {
        return "vip_rank";
    }

    protected function processPlayer(Player $player): string
    {
        $session = SessionManager::getInstance()->getSession($player);
        return $session ? ($session->getVipRank() ?? "") : "";
    }
}

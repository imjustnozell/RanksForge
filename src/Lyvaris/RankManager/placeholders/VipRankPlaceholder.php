<?php

namespace Lyvaris\RankManager\placeholders;

use Nozell\PlaceholderAPI\placeholders\PlayerPlaceholder;
use pocketmine\player\Player;
use Lyvaris\RankManager\sessions\SessionManager;
use Lyvaris\RankManager\utils\RankFactory;

class VipRankPlaceholder extends PlayerPlaceholder
{

    public function getIdentifier(): string
    {
        return "vip_rank";
    }

    protected function processPlayer(Player $player): string
    {
        $session = SessionManager::getInstance()->getSession($player);
        $rank = $session->getVipRank();
        $rank = RankFactory::getInstance()->getRank($rank);
        return $session ? ($rank->getPrefix() ?? "") : "";
    }
}

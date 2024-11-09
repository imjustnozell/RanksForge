<?php

namespace Lyvaris\RankManager\placeholders;

use Nozell\PlaceholderAPI\placeholders\PlayerPlaceholder;
use pocketmine\player\Player;
use Lyvaris\RankManager\sessions\SessionManager;
use Lyvaris\RankManager\utils\RankFactory;

class MediaRankPlaceholder extends PlayerPlaceholder
{

    public function getIdentifier(): string
    {
        return "media_rank";
    }

    protected function processPlayer(Player $player): string
    {
        $session = SessionManager::getInstance()->getSession($player);
        $rank = $session->getMediaRank();
        if ($rank === null) return false;
        $rank = RankFactory::getInstance()->getRank($rank);
        return $session ? ($rank->getPrefix() ?? "") : "";
    }
}

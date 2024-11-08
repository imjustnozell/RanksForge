<?php

namespace Lyvaris\RankManager\listeners;

use Lyvaris\RankManager\events\TemporaryRankExpireEvent;
use pocketmine\event\Listener;
use Lyvaris\RankManager\sessions\SessionManager;
use Lyvaris\RankManager\Main;

class TemporaryListener implements Listener
{

    public function onTemporaryRankExpire(TemporaryRankExpireEvent $event): void
    {
        $player = $event->getPlayer();
        $rankName = $event->getRankName();

        $session = SessionManager::getInstance()->getSession($player);
        if ($session === null) {
            $player->sendMessage("§cNo se pudo encontrar tu sesión.");
            return;
        }

        $session->removeTemporaryRank($rankName);
        $session->save();


        Main::getInstance()->getLogger()->info("El rango temporal '$rankName' del jugador {$player->getName()} ha expirado.");

        $player->sendMessage("§cTu rango temporal '$rankName' ha expirado.");
    }
}

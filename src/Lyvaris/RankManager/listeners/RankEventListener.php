<?php

namespace Lyvaris\RankManager\listeners;

use Lyvaris\RankManager\events\RankAssignedEvent;
use Lyvaris\RankManager\events\RankEditEvent;
use Lyvaris\RankManager\events\RankRemoveEvent;
use pocketmine\event\Listener;
use Lyvaris\RankManager\utils\RankFactory;
use Lyvaris\RankManager\Main;

class RankEventListener implements Listener
{

    public function onRankAssigned(RankAssignedEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $player = $event->getPlayer();
        $rankName = $event->getRankName();
        $expiryTime = $event->getExpiryTime();

        $rank = RankFactory::getInstance()->getRank($rankName);
        if ($rank === null) {
            $player->sendMessage("§cEl rango '$rankName' no existe.");
            return;
        }


        Main::getInstance()->getLogger()->info("El jugador {$player->getName()} ha recibido el rango '$rankName'.");

        $player->sendMessage("§aHas recibido el rango '$rankName'.");
    }

    public function onRankEdit(RankEditEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $rank = $event->getRank();
        $newPrefix = $event->getNewPrefix();
        $newPermissions = $event->getNewPermissions();


        Main::getInstance()->getLogger()->info("El rango '{$rank->getName()}' ha sido editado.");
    }

    public function onRankRemove(RankRemoveEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $rank = $event->getRank();
        $rankName = $rank->getName();


        Main::getInstance()->getLogger()->info("El rango '$rankName' ha sido eliminado.");
    }
}

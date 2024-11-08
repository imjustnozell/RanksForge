<?php

namespace Lyvaris\RankManager\listeners;

use Lyvaris\RankManager\events\RankAssignedEvent;
use Lyvaris\RankManager\events\RankCreateEvent;
use Lyvaris\RankManager\events\RankEditEvent;
use Lyvaris\RankManager\events\RankRemoveEvent;
use Lyvaris\RankManager\utils\RankFactory;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\sessions\SessionManager;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class RankEventListener implements Listener
{
    public function onRankCreate(RankCreateEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $rank = $event->getRank();
        $creator = $event->getCreator();

        Main::getInstance()->getLogger()->info("§aRango creado: '{$rank->getName()}' por {$creator->getName()}.");

        $creator->sendMessage("§aEl rango '{$rank->getName()}' ha sido creado exitosamente.");

        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player->hasPermission("rankmanager.notify")) {
                $player->sendMessage("§eNuevo rango creado: '{$rank->getName()}' por {$creator->getName()}.");
            }
        }
    }

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

        $session = SessionManager::getInstance()->getSession($player);
        if ($session === null) {
            $player->sendMessage("§cNo se pudo obtener tu sesión.");
            return;
        }

        $session->assignRank($rank, $expiryTime);

        $prefix = $session->getPrefix();
        if ($prefix !== "") {
        }

        Main::getInstance()->getLogger()->info("El jugador {$player->getName()} ha recibido el rango '$rankName'.");

        $player->sendMessage("§aHas recibido el rango '$rankName'." . ($expiryTime !== null ? " (Expira el " . date("Y-m-d H:i:s", $expiryTime) . ")" : ""));
    }

    public function onRankEdit(RankEditEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $rank = $event->getRank();
        $newPrefix = $event->getNewPrefix();
        $newPermissions = $event->getNewPermissions();
        $editor = $event->getEditor();

        Main::getInstance()->getLogger()->info("§aRango '{$rank->getName()}' ha sido editado por {$editor->getName()}.");

        $editor->sendMessage("§aEl rango '{$rank->getName()}' ha sido editado exitosamente.");

        foreach (SessionManager::getInstance()->getAllSessions() as $session) {
            if ($session->hasRank($rank->getName())) {
                $player = Main::getInstance()->getServer()->getPlayerExact($session->getPlayerName());
                if ($player !== null) {
                    $prefix = $session->getPrefix();
                    if ($prefix !== "") {
                    }
                    $player->sendMessage("§aTu rango '{$rank->getName()}' ha sido actualizado.");
                }
            }
        }

        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player->hasPermission("rankmanager.notify")) {
                $player->sendMessage("§eEl rango '{$rank->getName()}' ha sido editado por {$editor->getName()}.");
            }
        }
    }

    public function onRankRemove(RankRemoveEvent $event): void
    {
        if ($event->isCancelled()) {
            return;
        }

        $rank = $event->getRank();
        $rankName = $rank->getName();
        $remover = $event->getRemover();

        foreach (SessionManager::getInstance()->getAllSessions() as $session) {
            if ($session->hasRank($rankName)) {
                $player = Main::getInstance()->getServer()->getPlayerExact($session->getPlayerName());
                if ($player !== null) {
                    $session->removeRank($rankName);
                    $prefix = $session->getPrefix();
                    if ($prefix !== "") {
                    }
                    $player->sendMessage("§cEl rango '$rankName' ha sido removido.");
                }
            }
        }

        Main::getInstance()->getLogger()->info("§cRango '$rankName' ha sido eliminado por {$remover->getName()}.");

        $remover->sendMessage("§cEl rango '$rankName' ha sido eliminado exitosamente.");

        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player->hasPermission("rankmanager.notify")) {
                $player->sendMessage("§eEl rango '$rankName' ha sido eliminado por {$remover->getName()}.");
            }
        }
    }
}

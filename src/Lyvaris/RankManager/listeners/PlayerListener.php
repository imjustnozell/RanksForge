<?php

namespace Lyvaris\RankManager\listeners;

use Lyvaris\RankManager\sessions\SessionManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class PlayerListener implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $sessionManager = SessionManager::getInstance();
        $sessionManager->loadSession($player);

        $player->sendMessage("§aBienvenido al servidor, {$player->getName()}!");
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $sessionManager = SessionManager::getInstance();
        $sessionManager->saveSession($player);

        $player->sendMessage("§c¡Hasta luego, {$player->getName()}!");
    }
}

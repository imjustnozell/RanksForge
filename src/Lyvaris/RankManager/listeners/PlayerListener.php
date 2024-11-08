<?php

namespace Lyvaris\RankManager\listeners;

use Lyvaris\RankManager\sessions\SessionManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerListener implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        SessionManager::getInstance()->createSession($player);
        $player->sendMessage("§aTu sesión ha sido cargada.");
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        SessionManager::getInstance()->removeSession($player);
        $player->sendMessage("§aTu sesión ha sido guardada.");
    }
}

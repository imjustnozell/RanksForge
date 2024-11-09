<?php

namespace Lyvaris\RankManager\tasks;

use pocketmine\scheduler\Task;
use Lyvaris\RankManager\sessions\SessionManager;

class TemporaryCheckTask extends Task
{

    public function onRun(): void
    {
        foreach (SessionManager::getInstance()->getAllSessions() as $player) {
            SessionManager::getInstance()->getSession($player)->checkTemporaryRanks();
        }
    }
}

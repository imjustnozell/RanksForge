<?php

namespace Lyvaris\RankManager\sessions;

use Lyvaris\RankManager\Main;
use Nozell\Database\DatabaseFactory;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class SessionManager
{
    use SingletonTrait;

    private array $sessions = [];

    public function getSession(Player $player): ?Session
    {
        return $this->sessions[$player->getName()] ?? null;
    }

    public function createSession(Player $player): Session
    {
        $playerName = $player->getName();

        $databasePath = Main::getInstance()->getDataFolder() . "players/" . strtolower($playerName) . ".db";
        $database = DatabaseFactory::create($databasePath, "sqlite");

        $session = new Session($playerName, $database);
        $this->sessions[$playerName] = $session;
        return $session;
    }

    public function removeSession(Player $player): void
    {
        unset($this->sessions[$player->getName()]);
    }

    public function checkAllTemporaryRanks(): void
    {
        foreach ($this->sessions as $session) {
            $session->checkTemporaryRanks();
        }
    }
}

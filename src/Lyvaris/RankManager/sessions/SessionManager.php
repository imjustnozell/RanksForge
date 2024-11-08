<?php

namespace Lyvaris\RankManager\sessions;

use Lyvaris\RankManager\Main;
use Nozell\Database\DatabaseFactory;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use RuntimeException;

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

        if (isset($this->sessions[$playerName])) {
            return $this->sessions[$playerName];
        }

        $databaseType = "sqlite";
        $databaseFolder = Main::getInstance()->getDataFolder() . "playerdata/";
        $databasePath = $databaseFolder . "_ranksdata.db";

        @mkdir($databaseFolder, 0755, true);

        $database = DatabaseFactory::create($databasePath, $databaseType, true);

        $session = new Session($playerName, $database);
        $this->sessions[$playerName] = $session;
        return $session;
    }

    public function removeSession(Player $player): void
    {
        $playerName = $player->getName();
        if (isset($this->sessions[$playerName])) {
            $this->sessions[$playerName]->save();
            unset($this->sessions[$playerName]);
        }
    }

    public function checkAllTemporaryRanks(): void
    {
        foreach ($this->sessions as $session) {
            $session->checkTemporaryRanks();
        }
    }
}

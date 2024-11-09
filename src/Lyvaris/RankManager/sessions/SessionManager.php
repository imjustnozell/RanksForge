<?php

namespace Lyvaris\RankManager\sessions;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use Nozell\Database\DatabaseFactory;
use Lyvaris\RankManager\Main;

class SessionManager
{
    use SingletonTrait;

    private array $sessions = [];
    private $database;

    protected function __construct() {
        $dataFolder = Main::getInstance()->getDataFolder() . "playerdata/";
        if (!is_dir($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->database = DatabaseFactory::create(
            $dataFolder . "_rankdata.json",
            "json"
        );
    }

    public function getSession(Player $player): ?Session {
        $playerName = $player->getName();
        if (isset($this->sessions[$playerName])) {
            return $this->sessions[$playerName];
        }

        $session = new Session($playerName, $this->database);
        $this->sessions[$playerName] = $session;

        return $session;
    }

    public function loadSession(Player $player): void {
        $session = $this->getSession($player);
        if ($session === null) {
            return;
        }
    }

    public function saveSession(Player $player): void {
        $playerName = $player->getName();
        if (!isset($this->sessions[$playerName])) {
            return;
        }

        $this->sessions[$playerName]->save();
        unset($this->sessions[$playerName]);
    }

    public function getAllSessions(): array {
        return array_values($this->sessions);
    }
}
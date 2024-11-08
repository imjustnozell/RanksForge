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
        $this->database = DatabaseFactory::create(
            Main::getInstance()->getDataFolder() . "sessions.db",
            "sqlite"
        );
    }

    public function getSession(Player $player): ?Session {
        $playerName = strtolower($player->getName());
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

        $prefix = $session->getPrefix();
        if ($prefix !== "") {
        }

    }

    public function saveSession(Player $player): void {
        $playerName = strtolower($player->getName());
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

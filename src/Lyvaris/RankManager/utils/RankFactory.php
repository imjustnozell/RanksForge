<?php

namespace Lyvaris\RankManager\utils;

use Nozell\Database\DatabaseFactory;
use Lyvaris\RankManager\Main;
use pocketmine\utils\SingletonTrait;

class RankFactory {
    use SingletonTrait;

    private array $ranks = [];
    private $database;

    public function __construct() {
        $this->database = DatabaseFactory::create(
            Main::getInstance()->getDataFolder() . "ranks.db",
            "sqlite"
        );
        $this->loadRanks();
    }

    public function loadRanks(): void {
        $sections = $this->database->getAllSections();
        foreach ($sections as $rankName) {
            $data = $this->database->get($rankName, "data");
            if ($data !== null) {
                $this->ranks[$rankName] = new Rank(
                    $rankName,
                    $data["prefix"] ?? "",
                    $data["type"] ?? "",
                    $data["color"] ?? "",
                    $data["joinMessage"] ?? "",
                    $data["permissions"] ?? [],
                    $data["badge"] ?? ""
                );
            }
        }
    }

    public function saveRanks(): void {
        foreach ($this->ranks as $rankName => $rank) {
            $this->database->set($rankName, "data", [
                "prefix" => $rank->getPrefix(),
                "type" => $rank->getType(),
                "color" => $rank->getColor(),
                "joinMessage" => $rank->getJoinMessage(),
                "permissions" => $rank->getPermissions(),
                "badge" => $rank->getBadge()
            ]);
        }
    }

    public function getAllRankNames(): array {
        return array_keys($this->ranks);
    }

    public function getRank(string $rankName): ?Rank {
        return $this->ranks[$rankName] ?? null;
    }

    public function createRank(
        string $name,
        string $prefix,
        string $type,
        string $color,
        string $joinMessage,
        array $permissions,
        string $badge
    ): bool {
        if (isset($this->ranks[$name])) {
            return false;
        }

        $this->ranks[$name] = new Rank($name, $prefix, $type, $color, $joinMessage, $permissions, $badge);
        $this->saveRanks();
        return true;
    }

    public function editRank(
        string $name,
        string $prefix,
        string $type,
        string $color,
        string $joinMessage,
        array $permissions,
        string $badge
    ): bool {
        if (!isset($this->ranks[$name])) {
            return false;
        }

        $rank = $this->ranks[$name];
        $rank->setPrefix($prefix);
        $rank->setType($type);
        $rank->setColor($color);
        $rank->setJoinMessage($joinMessage);
        $rank->setPermissions($permissions);
        $rank->setBadge($badge);

        $this->saveRanks();
        return true;
    }

    public function removeRank(string $name): bool {
        if (!isset($this->ranks[$name])) {
            return false;
        }

        unset($this->ranks[$name]);
        $this->database->delete($name, "data");
        return true;
    }

    public function getOwningPlugin(): Main {
        return Main::getInstance();
    }
}

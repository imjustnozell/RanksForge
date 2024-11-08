<?php

namespace Lyvaris\RankManager\utils;

use Nozell\Database\DatabaseFactory;
use Lyvaris\RankManager\Main;
use pocketmine\utils\SingletonTrait;
use Lyvaris\RankManager\events\RankAssignedEvent;
use Lyvaris\RankManager\events\RankEditEvent;
use Lyvaris\RankManager\events\RankRemoveEvent;
use pocketmine\player\Player;
use RuntimeException;

class RankFactory
{
    use SingletonTrait;

    private array $ranks = [];
    private $database;

    private function __construct()
    {
        $this->database = DatabaseFactory::create(
            Main::getInstance()->getDataFolder() . "ranks.db",
            "sqlite"
        );
        $this->loadRanks();
    }

    public function loadRanks(): void
    {
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

    public function saveRanks(): void
    {
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

    public function getAllRankNames(): array
    {
        return array_keys($this->ranks);
    }

    public function getRank(string $rankName): ?Rank
    {
        return $this->ranks[$rankName] ?? null;
    }

    public function createRank(
        string $name,
        string $prefix,
        string $type,
        string $color,
        string $joinMessage,
        array $permissions,
        string $badge,
        Player $creator
    ): bool {
        if (isset($this->ranks[$name])) {
            return false;
        }

        $rank = new Rank($name, $prefix, $type, $color, $joinMessage, $permissions, $badge);
        $this->ranks[$name] = $rank;
        $this->saveRanks();

        $event = new RankAssignedEvent($creator, $name, null);
        $event->call();

        if (!$event->isCancelled()) {
            $creator->sendMessage("§aHas creado y asignado el rango '$name'.");
        } else {
            unset($this->ranks[$name]);
            $this->saveRanks();
            $creator->sendMessage("§cLa creación del rango '$name' ha sido cancelada.");
            return false;
        }

        return true;
    }

    public function editRank(
        string $name,
        string $prefix,
        string $type,
        string $color,
        string $joinMessage,
        array $permissions,
        string $badge,
        Player $editor
    ): bool {
        if (!isset($this->ranks[$name])) {
            return false;
        }

        $rank = $this->ranks[$name];
        $oldData = [
            "prefix" => $rank->getPrefix(),
            "type" => $rank->getType(),
            "color" => $rank->getColor(),
            "joinMessage" => $rank->getJoinMessage(),
            "permissions" => $rank->getPermissions(),
            "badge" => $rank->getBadge()
        ];

        $rank->setPrefix($prefix);
        $rank->setType($type);
        $rank->setColor($color);
        $rank->setJoinMessage($joinMessage);
        $rank->setPermissions($permissions);
        $rank->setBadge($badge);

        $this->saveRanks();

        $event = new RankEditEvent(
            $rank,
            $oldData["prefix"],
            $prefix,
            $oldData["permissions"],
            $permissions
        );
        $event->call();

        if (!$event->isCancelled()) {
            $editor->sendMessage("§aHas editado el rango '$name'.");
        } else {
            $rank->setPrefix($oldData["prefix"]);
            $rank->setType($oldData["type"]);
            $rank->setColor($oldData["color"]);
            $rank->setJoinMessage($oldData["joinMessage"]);
            $rank->setPermissions($oldData["permissions"]);
            $rank->setBadge($oldData["badge"]);
            $this->saveRanks();
            $editor->sendMessage("§cLa edición del rango '$name' ha sido cancelada.");
            return false;
        }

        return true;
    }

    public function removeRank(string $name, Player $remover): bool
    {
        if (!isset($this->ranks[$name])) {
            return false;
        }

        $rank = $this->ranks[$name];

        $event = new RankRemoveEvent($rank);
        $event->call();

        if ($event->isCancelled()) {
            $remover->sendMessage("§cLa eliminación del rango '$name' ha sido cancelada.");
            return false;
        }

        unset($this->ranks[$name]);
        $this->database->delete($name, "data");
        $this->saveRanks();

        $remover->sendMessage("§cHas eliminado el rango '$name'.");

        return true;
    }

    public function getOwningPlugin(): Main
    {
        return Main::getInstance();
    }
}

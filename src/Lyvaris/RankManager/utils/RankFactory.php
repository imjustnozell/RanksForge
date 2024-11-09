<?php

namespace Lyvaris\RankManager\utils;

use Lyvaris\RankManager\events\RankAssignedEvent;
use Nozell\Database\DatabaseFactory;
use Lyvaris\RankManager\Main;
use pocketmine\utils\SingletonTrait;
use Lyvaris\RankManager\events\RankCreateEvent;
use Lyvaris\RankManager\events\RankEditEvent;
use Lyvaris\RankManager\events\RankRemoveEvent;
use Lyvaris\RankManager\sessions\SessionManager;
use pocketmine\player\Player;

class RankFactory
{
    use SingletonTrait;

    private $database;

    private function __construct()
    {
        $dataFolder = Main::getInstance()->getDataFolder();
        if (!is_dir($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->database = DatabaseFactory::create(
            $dataFolder . "ranks.json",
            "json"
        );
    }

    public function loadRanks(): void
    {
        $sections = $this->database->getAllSections();
        foreach ($sections as $rankName) {
            $data = $this->database->get($rankName, "data");
            if ($data !== null) {
                new Rank(
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

    public function getRank(string $rankName): ?Rank
    {
        $data = $this->database->get($rankName, "data");
        if ($data === null) {
            return null;
        }

        return new Rank(
            $rankName,
            $data["prefix"] ?? "",
            $data["type"] ?? "",
            $data["color"] ?? "",
            $data["joinMessage"] ?? "",
            $data["permissions"] ?? [],
            $data["badge"] ?? ""
        );
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
        if ($this->database->get($name, "data") !== null) {
            return false;
        }

        $validTypes = ['staff', 'media', 'vip'];
        if (!in_array(strtolower($type), $validTypes)) {
            $creator->sendMessage("§cTipo de rango inválido. Los tipos válidos son: staff, media, vip.");
            return false;
        }

        $rank = new Rank($name, $prefix, strtolower($type), $color, $joinMessage, $permissions, $badge);
        $this->database->set($name, "data", [
            "prefix" => $rank->getPrefix(),
            "type" => $rank->getType(),
            "color" => $rank->getColor(),
            "joinMessage" => $rank->getJoinMessage(),
            "permissions" => $rank->getPermissions(),
            "badge" => $rank->getBadge()
        ]);

        $event = new RankCreateEvent($rank, $creator);
        $event->call();

        if ($event->isCancelled()) {
            $this->database->delete($name, "data");
            $creator->sendMessage("§cLa creación del rango '$name' ha sido cancelada.");
            return false;
        }

        $creator->sendMessage("§aHas creado el rango '$name'.");
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
        $data = $this->database->get($name, "data");
        if ($data === null) {
            return false;
        }

        $rank = new Rank($name, $prefix, strtolower($type), $color, $joinMessage, $permissions, $badge);

        $this->database->set($name, "data", [
            "prefix" => $prefix,
            "type" => $type,
            "color" => $color,
            "joinMessage" => $joinMessage,
            "permissions" => $permissions,
            "badge" => $badge
        ]);

        $event = new RankEditEvent($rank, $data["prefix"], $prefix, $data["permissions"], $permissions);
        $event->call();

        if ($event->isCancelled()) {
            $this->database->set($name, "data", $data);
            $editor->sendMessage("§cLa edición del rango '$name' ha sido cancelada.");
            return false;
        }

        $editor->sendMessage("§aHas editado el rango '$name'.");
        return true;
    }

    public function removeRank(string $name, Player $remover): bool
    {
        if ($this->database->get($name, "data") === null) {
            return false;
        }

        $rank = new Rank($name, "", "", "", "", [], "");

        $event = new RankRemoveEvent($rank);
        $event->call();

        if ($event->isCancelled()) {
            $remover->sendMessage("§cLa eliminación del rango '$name' ha sido cancelada.");
            return false;
        }

        $this->database->delete($name, "data");
        $remover->sendMessage("§cHas eliminado el rango '$name'.");
        return true;
    }

    public function getOwningPlugin(): Main
    {
        return Main::getInstance();
    }

    public function getAllRankNames(): array

    {

        $sections = $this->database->getAllSections();

        $rankNames = [];

        foreach ($sections as $rankName) {

            $data = $this->database->get($rankName, "data");

            if ($data !== null) {

                $rankNames[] = $rankName;
            }
        }

        return $rankNames;
    }
}

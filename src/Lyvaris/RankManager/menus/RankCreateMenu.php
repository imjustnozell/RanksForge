<?php

namespace Lyvaris\RankManager\menus;

use Vecnavium\FormsUI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\utils\RankFactory;

class RankCreateMenu extends CustomForm
{


    public function __construct(Player $player)
    {
        parent::__construct([$this, "onSubmit"]);

        $this->setTitle("Create a New Rank");
        $this->addInput("Rank Name", "Enter the name of the rank");
        $this->addInput("Prefix", "Enter the prefix for the rank");
        $this->addInput("Type (Staff, Media, VIP)", "Enter the type of the rank");
        $this->addInput("Color", "Enter the color code (e.g., #FFFFFF)");
        $this->addInput("Join Message", "Enter the message when the player joins");
        $this->addInput("Permissions (comma-separated)", "e.g., permission.node1,permission.node2");
        $this->addInput("Badge", "Enter a badge symbol");

        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        if ($data === null) {
            return;
        }

        $rankName = trim($data[0] ?? "");
        $prefix = trim($data[1] ?? "");
        $type = trim($data[2] ?? "");
        $color = trim($data[3] ?? "#FFFFFF");
        $joinMessage = trim($data[4] ?? "");
        $permissions = array_map('trim', explode(",", $data[5] ?? ""));
        $badge = trim($data[6] ?? "");

        if (empty($rankName) || empty($prefix) || empty($type)) {
            $player->sendMessage(TextFormat::RED . "Rank name, prefix, and type cannot be empty.");
            return;
        }

        $rankFactory = RankFactory::getInstance();
        if ($rankFactory->createRank($rankName, $prefix, $type, $color, $joinMessage, $permissions, $badge, $player)) {
            $player->sendMessage(TextFormat::GREEN . "Rank '$rankName' created successfully!");
        } else {
            $player->sendMessage(TextFormat::RED . "Failed to create rank. It may already exist.");
        }
    }
}

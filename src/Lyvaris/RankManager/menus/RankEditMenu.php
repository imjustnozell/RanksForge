<?php

namespace Lyvaris\RankManager\menus;

use Vecnavium\FormsUI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\utils\RankFactory;

class RankEditMenu extends CustomForm
{
    private string $rankName;

    public function __construct(Player $player, string $rankName)
    {

        $this->rankName = $rankName;
        parent::__construct([$this, "onSubmit"]);
        $this->setTitle("Edit Rank: $rankName");

        $rankFactory = RankFactory::getInstance();
        $rank = $rankFactory->getRank($rankName);

        if ($rank === null) {
            return;
        }

        $this->addInput("Prefix", "Enter the prefix for the rank", $rank->getPrefix());
        $this->addInput("Type (Staff, Media, VIP)", "Enter the type of the rank", $rank->getType());
        $this->addInput("Color", "Enter the color code (e.g., #FFFFFF)", $rank->getColor());
        $this->addInput("Join Message", "Enter the message when the player joins", $rank->getJoinMessage());
        $this->addInput("Permissions (comma-separated)", "e.g., permission.node1,permission.node2", implode(",", $rank->getPermissions()));
        $this->addInput("Badge", "Enter a badge symbol", $rank->getBadge());
        $player->sendForm($this);
    }


    public function onSubmit(Player $player, ?array $data): void
    {
        if ($data === null) {
            return;
        }

        $prefix = trim($data[0] ?? "");
        $type = trim($data[1] ?? "");
        $color = trim($data[2] ?? "#FFFFFF");
        $joinMessage = trim($data[3] ?? "");
        $permissions = array_map('trim', explode(",", $data[4] ?? ""));
        $badge = trim($data[5] ?? "");

        if (empty($this->rankName) || empty($prefix) || empty($type)) {
            $player->sendMessage(TextFormat::RED . "Rank name, prefix, and type cannot be empty.");
            return;
        }

        $rankFactory = RankFactory::getInstance();
        if ($rankFactory->editRank($this->rankName, $prefix, $type, $color, $joinMessage, $permissions, $badge, $player)) {
            $player->sendMessage(TextFormat::GREEN . "Rank '{$this->rankName}' edited successfully!");
        } else {
            $player->sendMessage(TextFormat::RED . "Failed to edit rank. Please try again.");
        }
    }
}

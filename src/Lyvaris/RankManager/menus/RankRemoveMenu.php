<?php

namespace Lyvaris\RankManager\menus;

use Vecnavium\FormsUI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\utils\RankFactory;

class RankRemoveMenu extends CustomForm
{
    private RankFactory $rankFactory;

    public function __construct(Player $player)
    {
        $this->rankFactory = new RankFactory();
        parent::__construct([$this, "onSubmit"]);

        $this->setTitle("Remove a Rank");

        $ranks = $this->rankFactory->getAllRankNames();
        $this->addDropdown("Select a Rank to Remove", $ranks);
        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        if ($data === null) {
            return;
        }

        $selectedIndex = $data[0];
        $ranks = $this->rankFactory->getAllRankNames();

        if (!isset($ranks[$selectedIndex])) {
            $player->sendMessage(TextFormat::RED . "Invalid rank selected.");
            return;
        }

        $rankName = $ranks[$selectedIndex];

        if ($this->rankFactory->removeRank($rankName, $player)) {
            $player->sendMessage(TextFormat::GREEN . "Rank '$rankName' has been successfully removed.");
        } else {
            $player->sendMessage(TextFormat::RED . "Failed to remove rank '$rankName'.");
        }
    }
}

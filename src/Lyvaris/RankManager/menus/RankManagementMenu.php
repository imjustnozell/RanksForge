<?php

namespace Lyvaris\RankManager\menus;

use Vecnavium\FormsUI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\menus\RankCreateMenu;
use Lyvaris\RankManager\menus\RankEditMenu;
use Lyvaris\RankManager\menus\RankRemoveMenu;
use Lyvaris\RankManager\utils\RankFactory;

class RankManagementMenu extends CustomForm
{

    public function __construct(Player $player)
    {
        parent::__construct([$this, "onSubmit"]);
        $this->setTitle("Rank Management");

        $this->addDropdown("Select an Action", ["Create Rank", "Edit Rank", "Remove Rank"]);
        $player->sendForm($this);
    }

    public function onSubmit(Player $player, ?array $data): void
    {
        if ($data === null) {
            return;
        }

        $action = $data[0];

        switch ($action) {
            case 0:
                $createMenu = new RankCreateMenu($player);
                break;

            case 1:
                $rankFactory = RankFactory::getInstance();
                $rankNames = $rankFactory->getAllRankNames();

                if (empty($rankNames)) {
                    $player->sendMessage(TextFormat::RED . "There are no ranks to edit.");
                    return;
                }

                $this->openRankSelectionMenu($player, $rankNames);
                break;

            case 2:
                $removeMenu = new RankRemoveMenu($player);
                break;

            default:
                $player->sendMessage(TextFormat::RED . "Invalid action selected.");
                break;
        }
    }

    private function openRankSelectionMenu(Player $player, array $rankNames): void
    {
        $form = new CustomForm(function (Player $player, ?array $data) use ($rankNames) {
            if ($data === null) {
                return;
            }

            $selectedRankName = $rankNames[$data[0]] ?? null;

            if ($selectedRankName === null) {
                $player->sendMessage(TextFormat::RED . "Invalid rank selected.");
                return;
            }

            $editMenu = new RankEditMenu($player, $selectedRankName);
        });

        $form->setTitle("Select Rank to Edit");
        $form->addDropdown("Choose a Rank", $rankNames);
        $player->sendForm($form);
    }
}

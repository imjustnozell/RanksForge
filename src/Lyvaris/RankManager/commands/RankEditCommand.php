<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\menus\RankEditMenu;

class RankEditCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "rankedit",
            "Open the menu to edit an existing rank",
            "/rankedit <rank>"
        );
        $this->setPermission("rankmanager.command.rankedit");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("rankmanager.command.rankedit")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage: /rankedit <rank>");
            return;
        }

        $rankName = $args[0];

        $menu = new RankEditMenu($sender, $rankName);
    }
}

<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\menus\RankManagementMenu;

class RankManagementCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "rankmanage",
            "Open the rank management menu",
            "/rankmanage"
        );
        $this->setPermission("rankmanager.command.rankmanage");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("rankmanager.command.rankmanage")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        $menu = new RankManagementMenu($sender);
    }
}

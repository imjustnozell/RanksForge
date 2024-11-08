<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\Main;
use Lyvaris\RankManager\menus\RankRemoveMenu;

class RankRemoveCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "rankremove",
            "Open the menu to remove a rank",
            "/rankremove"
        );
        $this->setPermission("rankmanager.command.rankremove");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("rankmanager.command.rankremove")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        $menu = new RankRemoveMenu($sender);
    }
}

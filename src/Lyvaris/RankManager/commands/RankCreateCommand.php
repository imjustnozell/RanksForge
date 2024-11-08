<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\menus\RankCreateMenu;

class RankCreateCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "rankcreate",
            "Open the menu to create a new rank",
            "/rankcreate"
        );
        $this->setPermission("rankmanager.command.rankcreate");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("rankmanager.command.rankcreate")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        $menu = new RankCreateMenu($sender);
    }
}

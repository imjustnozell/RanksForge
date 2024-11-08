<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\sessions\SessionManager;
use pocketmine\Server;

class GetRankCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "getrank",
            "Get the current ranks of a player",
            "/getrank <player>"
        );
        $this->setPermission("rankmanager.command.getrank");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender->hasPermission("rankmanager.command.getrank")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::RED . "Usage: /getrank <player>");
            return;
        }

        $playerName = $args[0];
        $targetPlayer = Server::getInstance()->getPlayerByPrefix($playerName);

        if ($targetPlayer === null) {
            $sender->sendMessage(TextFormat::RED . "Player '$playerName' not found.");
            return;
        }

        $session = SessionManager::getInstance()->getSession($targetPlayer);
        if ($session === null) {
            $sender->sendMessage(TextFormat::RED . "Failed to load session data for '$playerName'.");
            return;
        }

        $staffRank = $session->getStaffRank() ?? "None";
        $mediaRank = $session->getMediaRank() ?? "None";
        $vipRank = $session->getVipRank() ?? "None";

        $sender->sendMessage(TextFormat::YELLOW . "Ranks for " . $targetPlayer->getName() . ":");
        $sender->sendMessage(TextFormat::AQUA . "Staff Rank: " . TextFormat::WHITE . $staffRank);
        $sender->sendMessage(TextFormat::AQUA . "Media Rank: " . TextFormat::WHITE . $mediaRank);
        $sender->sendMessage(TextFormat::AQUA . "VIP Rank: " . TextFormat::WHITE . $vipRank);
    }
}

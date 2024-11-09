<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\sessions\SessionManager;
use Lyvaris\RankManager\utils\RankFactory;
use pocketmine\Server;

class SetTemporaryRankCommand extends Command
{

    public function __construct()
    {
        parent::__construct(
            "settemprank",
            "Set a temporary rank for a player",
            "/settemprank <player> <rank> <duration in minutes>"
        );
        $this->setPermission("rankmanager.command.settemprank");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender->hasPermission("rankmanager.command.settemprank")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::RED . "Usage: /settemprank <player> <rank> <duration in minutes>");
            return;
        }

        $playerName = $args[0];
        $rankName = $args[1];
        $duration = (int) $args[2];

        if ($duration <= 0) {
            $sender->sendMessage(TextFormat::RED . "Duration must be a positive integer.");
            return;
        }

        $targetPlayer = Server::getInstance()->getPlayerByPrefix($playerName);
        if ($targetPlayer === null) {
            $sender->sendMessage(TextFormat::RED . "Player '$playerName' not found.");
            return;
        }

        $sessionManager = SessionManager::getInstance();
        $session = $sessionManager->getSession($targetPlayer);

        if ($session === null) {
            $sender->sendMessage(TextFormat::RED . "Failed to load session data for '$playerName'.");
            return;
        }
        $rank = RankFactory::getInstance()->getRank($rankName);

        $expiryTime = time() + ($duration * 60);
        $session->assignRank($rank, $expiryTime);

        $sender->sendMessage(TextFormat::GREEN . "Temporary rank {$rank->getName()} set for player '$playerName' for $duration minutes.");
        $targetPlayer->sendMessage(TextFormat::YELLOW . "You have been given the temporary rank '$rankName' for $duration minutes.");
    }
}

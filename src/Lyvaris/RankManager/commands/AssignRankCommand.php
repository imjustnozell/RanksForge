<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\sessions\SessionManager;
use Lyvaris\RankManager\utils\RankFactory;
use pocketmine\Server;

class AssignRankCommand extends Command
{


    public function __construct()
    {
        parent::__construct("assignrank", "Assign one or more ranks to a player", "/assignrank <player> <rank1,rank2,...>", ["ar"]);
        $this->setPermission("rankmanager.command.checktemprank");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender->hasPermission("rankmanager.assign")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . "Usage: /assignrank <player> <rank1,rank2,...>");
            return;
        }

        $playerName = array_shift($args);
        $ranks = explode(",", implode(" ", $args));

        $player = Server::getInstance()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Player $playerName not found.");
            return;
        }

        $sessionManager = SessionManager::getInstance();
        $session = $sessionManager->getSession($player);

        if ($session === null) {
            $session = $sessionManager->loadSession($player);
        }

        $rankFactory = RankFactory::getInstance();
        $assignedRanks = [];

        foreach ($ranks as $rankName) {
            $rankName = trim($rankName);
            $rank = $rankFactory->getRank($rankName);

            if ($rank === null) {
                $sender->sendMessage(TextFormat::YELLOW . "Rank $rankName does not exist.");
                continue;
            }

            switch (strtolower($rank->getType())) {
                case "staff":
                    $session->setStaffRank($rankName);
                    break;
                case "media":
                    $session->setMediaRank($rankName);
                    break;
                case "vip":
                    $session->setVipRank($rankName);
                    break;
                default:
                    $sender->sendMessage(TextFormat::YELLOW . "Rank $rankName has an unknown type.");
                    continue;
            }

            $assignedRanks[] = $rankName;
        }

        if (empty($assignedRanks)) {
            $sender->sendMessage(TextFormat::RED . "No valid ranks were assigned to $playerName.");
        } else {
            $sender->sendMessage(TextFormat::GREEN . "Ranks " . implode(", ", $assignedRanks) . " assigned to $playerName.");
        }
    }
}

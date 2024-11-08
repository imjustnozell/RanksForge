<?php

namespace Lyvaris\RankManager\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Lyvaris\RankManager\sessions\SessionManager;

class CheckTempRankCommand extends Command
{
    public function __construct()
    {
        parent::__construct(
            "checktemprank",
            "Check how much time is left on your temporary ranks",
            "/checktemprank"
        );
        $this->setPermission("rankmanager.command.checktemprank");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("rankmanager.command.checktemprank")) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return;
        }

        $session = SessionManager::getInstance()->getSession($sender);
        if ($session === null) {
            $sender->sendMessage(TextFormat::RED . "Failed to load your session data.");
            return;
        }

        $currentTime = time();
        $temporaryRanks = $session->getTemporaryRanks();

        if (empty($temporaryRanks)) {
            $sender->sendMessage(TextFormat::GREEN . "You have no active temporary ranks.");
            return;
        }

        $sender->sendMessage(TextFormat::YELLOW . "Your temporary ranks and remaining times:");
        foreach ($temporaryRanks as $rankName => $expiryTime) {
            $remainingTime = $expiryTime - $currentTime;
            if ($remainingTime > 0) {
                $minutes = intdiv($remainingTime, 60);
                $seconds = $remainingTime % 60;
                $sender->sendMessage(TextFormat::AQUA . "- $rankName: $minutes minutes and $seconds seconds remaining");
            }
        }
    }
}

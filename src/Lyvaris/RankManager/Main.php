<?php

namespace Lyvaris\RankManager;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Nozell\PlaceholderAPI\PlaceholderAPI;
use Lyvaris\RankManager\placeholders\StaffRankPlaceholder;
use Lyvaris\RankManager\placeholders\MediaRankPlaceholder;
use Lyvaris\RankManager\placeholders\VipRankPlaceholder;
use Lyvaris\RankManager\commands\CheckTemporaryRankCommand;
use Lyvaris\RankManager\commands\SetTemporaryRankCommand;
use Lyvaris\RankManager\commands\RankManagementCommand;
use Lyvaris\RankManager\commands\RankCreateCommand;
use Lyvaris\RankManager\commands\RankEditCommand;
use Lyvaris\RankManager\commands\RankRemoveCommand;
use Lyvaris\RankManager\commands\GetRankCommand;
use Lyvaris\RankManager\commands\AssignRankCommand;
use Lyvaris\RankManager\commands\CheckTempRankCommand;
use Lyvaris\RankManager\listeners\PlayerListener;
use Lyvaris\RankManager\listeners\RankEventListener;
use Lyvaris\RankManager\listeners\TemporaryListener;
use Lyvaris\RankManager\Manager\LangManager;
use Lyvaris\RankManager\tasks\TemporaryCheckTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;
    private LangManager $langManager;

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();

        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new RankEventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new TemporaryCheckTask(), 20 * 60);

        $this->registerCommands();
        $this->registerPlaceholders();

        $this->getLogger()->info(TextFormat::GREEN . "RankManager plugin enabled!");
    }

    public function onDisable(): void
    {
        $this->getLogger()->info(TextFormat::RED . "RankManager plugin disabled!");
    }

    private function registerCommands(): void
    {
        Server::getInstance()->getCommandMap()->registerAll("rankmanager", [
            new CheckTempRankCommand(),
            new SetTemporaryRankCommand(),
            new RankManagementCommand(),
            new RankCreateCommand(),
            new RankEditCommand(),
            new RankRemoveCommand(),
            new GetRankCommand(),
            new AssignRankCommand()
        ]);
    }

    private function registerPlaceholders(): void
    {
        $placeholderAPI = Server::getInstance()->getPluginManager()->getPlugin("PlaceholderAPI");
        if ($placeholderAPI !== null && $placeholderAPI->isEnabled()) {
            PlaceholderAPI::getRegistry()->registerPlaceholder(new StaffRankPlaceholder());
            PlaceholderAPI::getRegistry()->registerPlaceholder(new MediaRankPlaceholder());
            PlaceholderAPI::getRegistry()->registerPlaceholder(new VipRankPlaceholder());
            $this->getLogger()->info(TextFormat::AQUA . "Placeholders registered successfully in PlaceholderAPI.");
        } else {
            $this->getLogger()->warning(TextFormat::RED . "PlaceholderAPI is not enabled.");
        }
    }
}

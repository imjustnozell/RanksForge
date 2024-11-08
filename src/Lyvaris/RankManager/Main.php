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
use Lyvaris\RankManager\Manager\LangManager;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;
    private LangManager $langManager;

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();

        LangManager::setInstance(new LangManager());
        LangManager::getInstance()->loadLangs();

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
        $this->getServer()->getCommandMap()->registerAll("rankmanager", [
            new CheckTemporaryRankCommand(),
            new SetTemporaryRankCommand(),
            new RankManagementCommand(),
            new RankCreateCommand(),
            new RankEditCommand(),
            new RankRemoveCommand(),
            new GetRankCommand()
        ]);
    }

    private function registerPlaceholders(): void
    {
        $placeholderAPI = $this->getServer()->getPluginManager()->getPlugin("PlaceholderAPI");
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

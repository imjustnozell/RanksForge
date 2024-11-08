<?php

namespace Lyvaris\RankManager\Manager;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Lyvaris\RankManager\Main;

class LangManager
{
    use SingletonTrait;

    private array $messages = [];

    public function __construct()
    {
        $this->loadLangs();
    }

    public function loadLangs(): void
    {
        $path = $this->resolvePath();

        if (!$this->fileExists($path)) {
            $this->createLangFile($path);
        }

        $this->messages = $this->parseConfig($path);
    }

    private function resolvePath(): string
    {
        $langPaths = [
            "es" => "spanish.json",
            "en" => "english.json",
            "fr" => "french.json",
            "de" => "german.json",
            "zh" => "chinese.json",
            "ja" => "japanese.json"
        ];

        $langID = Main::getInstance()
            ->getConfig()
            ->get("language", "en");

        return Main::getInstance()->getDataFolder() .
            "lang/" .
            ($langPaths[$langID] ?? "english.json");
    }

    private function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    private function parseConfig(string $path): array
    {
        $jsonContent = file_get_contents($path);
        return json_decode($jsonContent, true) ?? [];
    }

    private function createLangFile(string $path): void
    {
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT));
        Main::getInstance()
            ->getLogger()
            ->info("Created a new empty translation file at: " . $path);
    }

    public function getMessage(string $identifier, array $tags = [], array $subs = []): string
    {
        if (count($tags) !== count($subs)) {
            return "Error: Mismatched tags and substitutes.";
        }

        $msgFormat = $this->messages[$identifier] ?? "Translation key '{$identifier}' not found.";
        return str_replace($tags, $subs, $msgFormat);
    }
}

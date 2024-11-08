<?php

namespace Lyvaris\RankManager\sessions;

use RuntimeException;
use Lyvaris\RankManager\utils\Rank;
use pocketmine\player\Player;
use Lyvaris\RankManager\events\TemporaryRankExpireEvent;
use Lyvaris\RankManager\Main;

class Session
{
    private string $playerName;
    private ?string $staffRank;
    private ?string $mediaRank;
    private ?string $vipRank;
    private array $temporaryRanks;
    private $database;

    public function __construct(string $playerName, $database)
    {
        $this->playerName = strtolower($playerName);
        $this->database = $database;
        $this->staffRank = null;
        $this->mediaRank = null;
        $this->vipRank = null;
        $this->temporaryRanks = [];
        $this->load();
    }


    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getStaffRank(): ?string
    {
        return $this->staffRank;
    }

    public function setStaffRank(?string $rank): void
    {
        $this->staffRank = $rank;
    }

    public function getMediaRank(): ?string
    {
        return $this->mediaRank;
    }

    public function setMediaRank(?string $rank): void
    {
        $this->mediaRank = $rank;
    }

    public function getVipRank(): ?string
    {
        return $this->vipRank;
    }

    public function setVipRank(?string $rank): void
    {
        $this->vipRank = $rank;
    }

    public function getTemporaryRanks(): array
    {
        return $this->temporaryRanks;
    }

    public function setTemporaryRank(string $rankName, int $expiryTimestamp): void
    {
        $this->temporaryRanks[$rankName] = $expiryTimestamp;
    }

    public function removeTemporaryRank(string $rankName): void
    {
        unset($this->temporaryRanks[$rankName]);
    }

    public function assignRank(Rank $rank, ?int $expiryTime = null): void
    {
        switch ($rank->getType()) {
            case 'staff':
                $this->staffRank = $rank->getName();
                break;
            case 'media':
                $this->mediaRank = $rank->getName();
                break;
            case 'vip':
                $this->vipRank = $rank->getName();
                break;
        }

        if ($expiryTime !== null) {
            $this->temporaryRanks[$rank->getName()] = $expiryTime;
        }

        $this->save();
    }

    public function checkTemporaryRanks(): void
    {
        $currentTime = time();
        $expiredRanks = [];

        foreach ($this->temporaryRanks as $rankName => $expiryTimestamp) {
            if ($currentTime >= $expiryTimestamp) {
                $expiredRanks[] = $rankName;
                unset($this->temporaryRanks[$rankName]);

                $player = Main::getInstance()->getServer()->getPlayerExact($this->playerName);
                if ($player !== null) {
                    $event = new TemporaryRankExpireEvent($player, $rankName);
                    $event->call();

                    if (!$event->isCancelled()) {

                        $player->sendMessage("§cTu rango temporal '$rankName' ha expirado.");
                    }
                }
            }
        }

        if (!empty($expiredRanks)) {
            $this->save();
        }
    }

    private function load(): void
    {
        $data = $this->database->get("ranksdata", $this->playerName);

        if ($data !== null) {
            $this->staffRank = $data['staffRank'] ?? null;
            $this->mediaRank = $data['mediaRank'] ?? null;
            $this->vipRank = $data['vipRank'] ?? null;
            $this->temporaryRanks = $data['temporaryRanks'] ?? [];
        } else {
            $this->staffRank = null;
            $this->mediaRank = null;
            $this->vipRank = null;
            $this->temporaryRanks = [];
            $this->save();
        }
    }

    public function save(): void
    {
        $data = [
            'staffRank' => $this->staffRank,
            'mediaRank' => $this->mediaRank,
            'vipRank' => $this->vipRank,
            'temporaryRanks' => $this->temporaryRanks
        ];

        $this->database->set("ranksdata", $this->playerName, $data);
    }
}

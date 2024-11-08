<?php

namespace Lyvaris\RankManager\events;

use pocketmine\event\Event;
use pocketmine\event\Cancellable;
use Lyvaris\RankManager\utils\Rank;
use pocketmine\player\Player;

class RankEditEvent extends Event implements Cancellable
{
    private Rank $rank;
    private string $oldPrefix;
    private string $newPrefix;
    private array $oldPermissions;
    private array $newPermissions;
    private bool $isCancelled = false;
    private Player $editor;

    public function __construct(
        Rank $rank,
        string $oldPrefix,
        string $newPrefix,
        array $oldPermissions,
        array $newPermissions
    ) {
        $this->rank = $rank;
        $this->oldPrefix = $oldPrefix;
        $this->newPrefix = $newPrefix;
        $this->oldPermissions = $oldPermissions;
        $this->newPermissions = $newPermissions;
    }

    public function getRank(): Rank
    {
        return $this->rank;
    }

    public function getOldPrefix(): string
    {
        return $this->oldPrefix;
    }

    public function getNewPrefix(): string
    {
        return $this->newPrefix;
    }

    public function getOldPermissions(): array
    {
        return $this->oldPermissions;
    }

    public function getNewPermissions(): array
    {
        return $this->newPermissions;
    }

    public function isCancelled(): bool
    {
        return $this->isCancelled;
    }

    public function setCancelled(bool $cancelled = true): void
    {
        $this->isCancelled = $cancelled;
    }

    public function getEditor(): Player
    {
        return $this->editor;
    }
}

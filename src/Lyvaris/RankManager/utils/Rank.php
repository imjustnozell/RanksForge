<?php

namespace Lyvaris\RankManager\utils;

class Rank {
    private string $name;
    private string $prefix;
    private string $type;
    private string $color;
    private string $joinMessage;
    private array $permissions;
    private string $badge;

    public function __construct(
        string $name,
        string $prefix,
        string $type,
        string $color,
        string $joinMessage,
        array $permissions,
        string $badge
    ) {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->type = $type;
        $this->color = $color;
        $this->joinMessage = $joinMessage;
        $this->permissions = $permissions;
        $this->badge = $badge;
    }


    public function getName(): string {
        return $this->name;
    }

    public function getPrefix(): string {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void {
        $this->prefix = $prefix;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function setColor(string $color): void {
        $this->color = $color;
    }

    public function getJoinMessage(): string {
        return $this->joinMessage;
    }

    public function setJoinMessage(string $joinMessage): void {
        $this->joinMessage = $joinMessage;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void {
        $this->permissions = $permissions;
    }

    public function addPermission(string $permission): void {
        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
        }
    }

    public function removePermission(string $permission): void {
        $this->permissions = array_filter(
            $this->permissions,
            fn($perm) => $perm !== $permission
        );
    }

    public function getBadge(): string {
        return $this->badge;
    }

    public function setBadge(string $badge): void {
        $this->badge = $badge;
    }
}

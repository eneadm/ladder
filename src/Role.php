<?php

namespace Ladder;

use JsonSerializable;

class Role implements JsonSerializable
{
    public string $key;

    public string $name;

    public array $permissions;

    public string $description;

    public function __construct(string $key, string $name, array $permissions)
    {
        $this->key = $key;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'name' => __($this->name),
            'description' => __($this->description),
            'permissions' => $this->permissions,
        ];
    }
}

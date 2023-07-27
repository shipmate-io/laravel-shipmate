<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Shipmate\Shipmate\JobQueue\Job as PhpJob;

class Job extends PhpJob
{
    private ?string $name;

    private ?int $attempts;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(?int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getId(): string
    {
        return $this->payload['uuid'];
    }

    public function getConnection(): ?string
    {
        $command = unserialize($this->payload['data']['command']);

        return $command->connection ?? null;
    }

    public function getQueue(): ?string
    {
        $command = unserialize($this->payload['data']['command']);

        return $command->queue ?? null;
    }

    public function toJson(): string
    {
        return json_encode($this->payload);
    }
}

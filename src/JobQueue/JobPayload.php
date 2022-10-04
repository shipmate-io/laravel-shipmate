<?php

namespace Shipmate\LaravelShipmate\JobQueue;

class JobPayload
{
    public function __construct(
        private array $job,
    ) {
    }

    public function getId(): string
    {
        return $this->job['uuid'];
    }

    public function getConnectionName(): ?string
    {
        $command = unserialize($this->job['data']['command']);

        return $command->connection ?? null;
    }

    public function getQueueName(): ?string
    {
        $command = unserialize($this->job['data']['command']);

        return $command->queue ?? null;
    }

    public function toJson(): string
    {
        return json_encode($this->job);
    }
}

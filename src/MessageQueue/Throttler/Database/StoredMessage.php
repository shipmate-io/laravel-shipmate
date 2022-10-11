<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Throttler\Database;

use Illuminate\Database\Eloquent\Model;
use Shipmate\LaravelShipmate\MessageQueue\MessagePayload;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueueConfig;

class StoredMessage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getTable(): string
    {
        return MessageQueueConfig::new()->getDatabaseMessageThrottlerTableName();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->attempts;
    }

    public function getPayload(): MessagePayload
    {
        return MessagePayload::deserialize(serialization: $this->payload, decode: false);
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }
}

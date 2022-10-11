<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Throttler\Database;

use Shipmate\LaravelShipmate\MessageQueue\Message;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueueConfig;
use Shipmate\LaravelShipmate\MessageQueue\Throttler\MessageThrottler;

class DatabaseMessageThrottler implements MessageThrottler
{
    public static function new(): static
    {
        return app(static::class);
    }

    public function shouldThrottle(Message $message): bool
    {
        /** @var StoredMessage $storedMessage */
        $storedMessage = StoredMessage::query()->firstOrCreate(
            [
                'id' => $message->id,
            ],
            [
                'type' => $message->type,
                'payload' => $message->payload->serialize(encode: false),
                'attempts' => 1,
                'state' => 'handled',
            ]
        );

        $maximumAttempts = MessageQueueConfig::new()->getDatabaseMessageThrottlerMaximumAttempts();
        $shouldThrottle = $storedMessage->getAttempts() <= $maximumAttempts;

        if ($shouldThrottle) {
            $storedMessage->update([
                'state' => 'skipped',
            ]);
        } else {
            $storedMessage->update([
                'attempts' => $storedMessage->getAttempts() + 1,
            ]);
        }

        return $shouldThrottle;
    }
}

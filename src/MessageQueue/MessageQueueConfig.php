<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Shipmate\LaravelShipmate\ShipmateException;

class MessageQueueConfig
{
    public function __construct(
        private array $config
    ) {
    }

    public static function new(): static
    {
        return app(static::class);
    }

    /*
     * The topic that is used to publish messages.
     */
    public function getDefaultTopic(): string
    {
        $topic = $this->config['topic'] ?? null;

        if (! $topic) {
            throw new ShipmateException(
                'No value specified for the `topic` parameter in the `config/message-queue.php` file.'
            );
        }

        return $topic;
    }

    /*
     * The subscription for which to receive messages.
     */
    public function getDefaultSubscription(): string
    {
        $subscription = $this->config['subscription'] ?? null;

        if (! $subscription) {
            throw new ShipmateException(
                'No value specified for the `subscription` parameter in the `config/message-queue.php` file.'
            );
        }

        return $subscription;
    }

    /*
     * The subscription for which to receive messages.
     */
    public function getPathToMessageHandlersFile(): string
    {
        return $this->config['message_handlers'] ?? base_path('routes/messages.php');
    }

    /*
     * The database table in which to store the received messages.
     */
    public function getMessageThrottler(): ?string
    {
        return $this->config['message_throttler'];
    }

    /*
     * The database table in which to store the received messages.
     */
    public function getDatabaseMessageThrottlerTableName(): string
    {
        return $this->config['database_message_throttler']['table_name'] ?? 'messages';
    }

    /*
     * The database table in which to store the received messages.
     */
    public function getDatabaseMessageThrottlerMaximumAttempts(): int
    {
        return $this->config['database_message_throttler']['maximum_attempts'] ?? 5;
    }
}

<?php

namespace Shipmate\Shipmate\MessageQueue;

use Shipmate\Shipmate\ShipmateException;

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
                'No value specified for the `topic` parameter in the `message_queue` section of the `config/shipmate.php` file.'
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
                'No value specified for the `subscription` parameter in the `message_queue` section of the `config/shipmate.php` file.'
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
}

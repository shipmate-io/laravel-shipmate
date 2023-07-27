<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

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
    public function getMessageQueueName(string $queue): string
    {
        $queues = $this->config['queues'] ?? [];

        if (! array_key_exists($queue, $queues)) {
            throw new ShipmateException(
                "Unknown message queue `{$queue}`."
            );
        }

        return $queues[$queue];
    }

    /*
     * The subscription for which to receive messages.
     */
    public function getPathToMessageHandlersFile(): string
    {
        return $this->config['message_handlers'] ?? base_path('routes/messages.php');
    }

    /*
     * Whether to register the routes required to handle the messages from the message queues.
     */
    public function registerRoutes(): bool
    {
        return $this->config['register_routes'] ?? true;
    }
}

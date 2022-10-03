<?php

namespace Shipmate\Shipmate\MessageQueue;

use Exception;
use Illuminate\Support\Collection;
use Throwable;

class MessageHandlers
{
    private Collection $messageHandlers;

    public function __construct()
    {
        $pathToMessageHandlersFile = MessageQueueConfig::new()->getPathToMessageHandlersFile();

        try {
            $messageHandlers = require $pathToMessageHandlersFile;
        } catch (Throwable) {
            throw new Exception("Could not read the message handlers from '{$pathToMessageHandlersFile}'.");
        }

        if (! is_array($messageHandlers)) {
            throw new Exception('The message handlers file should return an array.');
        }

        $this->messageHandlers = new Collection($messageHandlers);
    }

    public static function new(): self
    {
        return app(self::class);
    }

    public function getMessageTypes(): array
    {
        return $this->messageHandlers->keys()->all();
    }

    public function findHandlerForMessage(Message $message): ?MessageHandler
    {
        $messageHandler = $this->messageHandlers->get($message->type);

        if ($messageHandler === null) {
            return null;
        }

        return new MessageHandler($messageHandler);
    }
}

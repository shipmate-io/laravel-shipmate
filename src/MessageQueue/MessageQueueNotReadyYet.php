<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Shipmate\LaravelShipmate\ShipmateException;

class MessageQueueNotReadyYet extends ShipmateException
{
    public function __construct(string $message)
    {
        parent::__construct("The message queue is not ready yet. Reason: {$message}", 500);
    }
}

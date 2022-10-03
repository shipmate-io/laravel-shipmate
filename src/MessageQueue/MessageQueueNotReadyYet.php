<?php

namespace Shipmate\Shipmate\MessageQueue;

use Shipmate\Shipmate\ShipmateException;

class MessageQueueNotReadyYet extends ShipmateException
{
    public function __construct(string $message)
    {
        parent::__construct("The message queue is not ready yet. Reason: {$message}", 500);
    }
}

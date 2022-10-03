<?php

namespace Shipmate\Shipmate\MessageQueue;

use Shipmate\Shipmate\ShipmateException;

class ClientNotReadyYet extends ShipmateException
{
    public function __construct(string $message)
    {
        parent::__construct("Client not ready yet. Reason: {$message}", 500);
    }
}

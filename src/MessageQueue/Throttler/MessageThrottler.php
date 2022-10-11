<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Throttler;

use Shipmate\LaravelShipmate\MessageQueue\Message;

interface MessageThrottler
{
    public function shouldThrottle(Message $message): bool;
}

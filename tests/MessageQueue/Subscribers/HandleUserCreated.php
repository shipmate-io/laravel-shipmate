<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers;

use Shipmate\LaravelShipmate\MessageQueue\Message;
use Shipmate\LaravelShipmate\Tests\Log;

class HandleUserCreated
{
    public function __invoke(Message $message): void
    {
        $firstName = $message->payload->get('first_name');

        Log::write("Welcome, {$firstName}!");
    }
}

<?php

namespace Shipmate\Shipmate\Tests\MessageQueue\Subscribers;

use Shipmate\Shipmate\MessageQueue\Message;
use Shipmate\Shipmate\Tests\Log;

class HandleUserCreated
{
    public function __invoke(Message $message): void
    {
        $firstName = $message->payload->get('first_name');

        Log::write("Welcome, {$firstName}!");
    }
}

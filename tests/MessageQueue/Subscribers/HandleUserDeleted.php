<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers;

use Shipmate\LaravelShipmate\MessageQueue\Message;
use Shipmate\LaravelShipmate\Tests\Log;

class HandleUserDeleted
{
    public function handle(Message $message): void
    {
        $email = $message->payload->get('email');

        Log::write("Goodbye email sent to {$email}");
    }
}

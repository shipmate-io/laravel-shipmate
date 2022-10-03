<?php

namespace Shipmate\Shipmate\Tests\MessageQueue\Subscribers;

use Shipmate\Shipmate\MessageQueue\Message;
use Shipmate\Shipmate\Tests\Log;

class HandleUserDeleted
{
    public function handle(Message $message): void
    {
        $email = $message->payload->get('email');

        Log::write("Goodbye email sent to {$email}");
    }
}

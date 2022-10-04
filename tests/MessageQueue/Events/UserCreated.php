<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue\Events;

use Shipmate\LaravelShipmate\MessageQueue\ShouldPublish;

class UserCreated implements ShouldPublish
{
    public function publishAs(): string
    {
        return 'user.created';
    }

    public function publishWith(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];
    }
}

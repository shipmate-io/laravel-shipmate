<?php

namespace Shipmate\Shipmate\Tests\MessageQueue\Events;

use Shipmate\Shipmate\MessageQueue\ShouldPublish;

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

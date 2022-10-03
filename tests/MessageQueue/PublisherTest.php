<?php

namespace Shipmate\Shipmate\Tests\MessageQueue;

use Shipmate\Shipmate\MessageQueue\MessageQueue;
use Shipmate\Shipmate\Tests\MessageQueue\Events\UserCreated;
use Shipmate\Shipmate\Tests\MessageQueue\Events\UserDeleted;
use Shipmate\Shipmate\Tests\TestCase;

class PublisherTest extends TestCase
{
    /** @test */
    public function it_does_not_publish_a_message_that_does_not_implement_the_should_publish_interface(): void
    {
        $this
            ->makePartialMock(MessageQueue::class)
            ->shouldNotReceive('publishMessage');

        event(new UserDeleted);
    }

    /** @test */
    public function it_publishes_a_message_that_implements_the_should_publish_interface(): void
    {
        $this
            ->makePartialMock(MessageQueue::class)
            ->shouldReceive('publishMessage')
            ->once();

        event(new UserCreated);
    }
}

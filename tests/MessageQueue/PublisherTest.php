<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue;

use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Events\UserCreated;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Events\UserDeleted;
use Shipmate\LaravelShipmate\Tests\TestCase;

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

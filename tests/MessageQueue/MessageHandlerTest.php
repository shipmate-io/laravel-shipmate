<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue;

use Illuminate\Support\Str;
use Shipmate\LaravelShipmate\MessageQueue\Message;
use Shipmate\LaravelShipmate\MessageQueue\MessageHandler;
use Shipmate\LaravelShipmate\MessageQueue\MessagePayload;
use Shipmate\LaravelShipmate\Tests\Log;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;
use Shipmate\LaravelShipmate\Tests\TestCase;

class MessageHandlerTest extends TestCase
{
    /** @test */
    public function it_can_handle_different_types_of_message_handlers(): void
    {
        $user = MessagePayload::new([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        $createdMessage = new Message(Str::uuid()->toString(), 'user.created', $user);
        $deletedMessage = new Message(Str::uuid()->toString(), 'user.deleted', $user);

        MessageHandler::new(HandleUserCreated::class)->handle($createdMessage);
        MessageHandler::new(HandleUserDeleted::class)->handle($deletedMessage);
        MessageHandler::new([HandleUserDeleted::class, 'handle'])->handle($deletedMessage);

        $this->assertEquals([
            'Welcome, John!',
            'Goodbye email sent to john.doe@example.com',
            'Goodbye email sent to john.doe@example.com',
        ], Log::read());
    }
}

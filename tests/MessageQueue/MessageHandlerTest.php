<?php

namespace Shipmate\Shipmate\Tests\MessageQueue;

use Illuminate\Support\Str;
use Shipmate\Shipmate\MessageQueue\Message;
use Shipmate\Shipmate\MessageQueue\MessageHandler;
use Shipmate\Shipmate\MessageQueue\MessagePayload;
use Shipmate\Shipmate\Tests\Log;
use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;
use Shipmate\Shipmate\Tests\TestCase;

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

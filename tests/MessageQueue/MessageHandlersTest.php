<?php

namespace Shipmate\Shipmate\Tests\MessageQueue;

use Illuminate\Support\Str;
use Shipmate\Shipmate\MessageQueue\Message;
use Shipmate\Shipmate\MessageQueue\MessageHandler;
use Shipmate\Shipmate\MessageQueue\MessageHandlers;
use Shipmate\Shipmate\MessageQueue\MessagePayload;
use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;
use Shipmate\Shipmate\Tests\TestCase;

class MessageHandlersTest extends TestCase
{
    /** @test */
    public function it_can_correctly_load_the_message_handlers_file(): void
    {
        $messageHandlers = MessageHandlers::new();

        $this->assertEquals(['user.created', 'user.deleted'], $messageHandlers->getMessageTypes());

        $userCreatedMessage = new Message(Str::uuid()->toString(), 'user.created', MessagePayload::new());
        $messageHandler = $messageHandlers->findHandlerForMessage($userCreatedMessage);
        $this->assertInstanceOf(MessageHandler::class, $messageHandler);
        $this->assertEquals(HandleUserCreated::class, $messageHandler->getClassName());
        $this->assertEquals('__invoke', $messageHandler->getMethodName());

        $userDeletedMessage = new Message(Str::uuid()->toString(), 'user.deleted', MessagePayload::new());
        $messageHandler = $messageHandlers->findHandlerForMessage($userDeletedMessage);
        $this->assertInstanceOf(MessageHandler::class, $messageHandler);
        $this->assertEquals(HandleUserDeleted::class, $messageHandler->getClassName());
        $this->assertEquals('handle', $messageHandler->getMethodName());

        $userUpdatedMessage = new Message(Str::uuid()->toString(), 'user.updated', MessagePayload::new());
        $messageHandler = $messageHandlers->findHandlerForMessage($userUpdatedMessage);
        $this->assertNull($messageHandler);
    }
}

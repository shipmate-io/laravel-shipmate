<?php

namespace Shipmate\LaravelShipmate\Tests\MessageQueue;

use Illuminate\Support\Str;
use Shipmate\LaravelShipmate\MessageQueue\Message;
use Shipmate\LaravelShipmate\MessageQueue\MessageHandler;
use Shipmate\LaravelShipmate\MessageQueue\MessageHandlers;
use Shipmate\LaravelShipmate\MessageQueue\MessagePayload;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;
use Shipmate\LaravelShipmate\Tests\TestCase;

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

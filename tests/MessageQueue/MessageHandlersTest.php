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
    public function it_can_get_the_messages_types_from_the_message_handlers_file(): void
    {
        $messageHandlers = MessageHandlers::new();

        $this->assertEquals(['user.created', 'user.deleted'], $messageHandlers->getMessageTypes());
    }

    /** @test */
    public function it_can_call_the_correct_handler_from_the_message_handlers_file(): void
    {
        $userCreatedMessage = new Message(Str::uuid()->toString(), 'user.created', MessagePayload::new());
        $userUpdatedMessage = new Message(Str::uuid()->toString(), 'user.updated', MessagePayload::new());
        $userDeletedMessage = new Message(Str::uuid()->toString(), 'user.deleted', MessagePayload::new());

        $userCreatedMessageHandler = MessageHandlers::new()->findHandlerForMessage($userCreatedMessage);
        $userUpdatedMessageHandler = MessageHandlers::new()->findHandlerForMessage($userUpdatedMessage);
        $userDeletedMessageHandler = MessageHandlers::new()->findHandlerForMessage($userDeletedMessage);

        $this->assertInstanceOf(MessageHandler::class, $userCreatedMessageHandler);
        $this->assertNull($userUpdatedMessageHandler);
        $this->assertInstanceOf(MessageHandler::class, $userDeletedMessageHandler);

        $this->makePartialMock(HandleUserCreated::class)->shouldReceive('__invoke')->once();
        $this->makePartialMock(HandleUserDeleted::class)->shouldReceive('handle')->once();

        $userCreatedMessageHandler->handle($userCreatedMessage);
        $userDeletedMessageHandler->handle($userDeletedMessage);
    }
}

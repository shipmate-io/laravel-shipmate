<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Shipmate\Shipmate\MessageQueue\MessageQueue;

class MessageQueueController
{
    public function handleMessage(Request $request): Response
    {
        $requestPayload = (string) $request->getContent();

        $message = MessageQueue::parseMessage($requestPayload);

        $messageHandler = MessageHandlers::new()->findHandlerForMessage($message);

        try {
            $messageHandler?->handle($message);
        } catch (Exception $e) {
            Log::error($e);

            return new Response(null, 500);
        }

        return new Response;
    }

    public function handleFailedMessage(Request $request): Response
    {
        $requestPayload = (string) $request->getContent();

        $message = MessageQueue::parseMessage($requestPayload);

        $jsonPayload = json_encode($message->payload);

        Log::error("Failed to handle message with type '{$message->type}' and payload '{$jsonPayload}'.");

        return new Response;
    }
}

<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RequestHandler
{
    public function __invoke(Request $request): Response
    {
        // TODO: OpenId::new()->validateToken($request->bearerToken());

        try {
            $message = $this->parseMessage($request);
        } catch (Exception) {
            return new Response('Invalid message format.', 422);
        }

        $messageHandler = MessageHandlers::new()->findHandlerForMessage($message);

        try {
            $messageHandler?->handle($message);
        } catch (Exception $e) {
            Log::error($e);

            return new Response(null, 500);
        }

        return new Response;
    }

    private function parseMessage(Request $request): Message
    {
        $rawMessage = $request->get('message');

        try {
            $message = new Message(
                id: $rawMessage['messageId'],
                type: $rawMessage['attributes']['type'],
                payload: MessagePayload::deserialize(base64_decode($rawMessage['data'])),
            );
        } catch (Exception $exception) {
            $jsonEncodedMessage = json_encode($rawMessage ?? '');
            Log::error("Invalid message received: `{$jsonEncodedMessage}`");
            Log::debug($exception);
            throw $exception;
        }

        return $message;
    }
}

<?php

namespace Shipmate\Shipmate\MessageQueue;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RequestHandler
{
    public function __invoke(Request $request): Response
    {
        try {
            // TODO: OpenId::new()->validateToken($request->bearerToken());

            $rawMessage = $request->get('message');

            $message = new Message(
                id: $rawMessage['messageId'],
                type: $rawMessage['attributes']['type'],
                payload: MessagePayload::deserialize(base64_decode($rawMessage['data'])),
            );

            $messageHandler = MessageHandlers::new()->findHandlerForMessage($message);

            $messageHandler?->handle($message);
        } catch (Exception $e) {
            Log::error($e);

            return new Response(null, 500);
        }

        return new Response;
    }
}

<?php

namespace Shipmate\Shipmate\MessageQueue;

class Message
{
    public function __construct(
        public string $id,
        public string $type,
        public MessagePayload $payload,
    ) {
    }
}

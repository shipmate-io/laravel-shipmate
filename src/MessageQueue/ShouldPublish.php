<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

interface ShouldPublish
{
    public function publishOn(): string;

    public function publishAs(): string;

    public function publishWith(): mixed;
}

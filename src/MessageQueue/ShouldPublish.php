<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

interface ShouldPublish
{
    public function publishAs(): string;

    public function publishWith(): array;
}

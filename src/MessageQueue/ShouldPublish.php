<?php

namespace Shipmate\Shipmate\MessageQueue;

interface ShouldPublish
{
    public function publishAs(): string;

    public function publishWith(): array;
}

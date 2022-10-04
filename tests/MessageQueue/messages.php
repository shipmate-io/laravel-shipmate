<?php

use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\LaravelShipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;

return [

    'user.created' => HandleUserCreated::class,

    'user.deleted' => [HandleUserDeleted::class, 'handle'],

];

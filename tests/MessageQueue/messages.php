<?php

use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserCreated;
use Shipmate\Shipmate\Tests\MessageQueue\Subscribers\HandleUserDeleted;

return [

    'user.created' => HandleUserCreated::class,

    'user.deleted' => [HandleUserDeleted::class, 'handle'],

];

<?php

return [

    /*
     * The name of the message queue topic that is used to publish messages.
     */
    'topic' => env('MESSAGE_QUEUE_TOPIC', 'default'),

    /*
     * The name of the message queue subscription that is used to receive messages.
     */
    'subscription' => env('MESSAGE_QUEUE_SUBSCRIPTION'),

    /*
     * The file within your code base that defines your message handlers.
     */
    'message_handlers' => base_path('routes/messages.php'),

    /*
     * The file within your code base that defines your message handlers.
     */
    'message_throttler' => \Shipmate\LaravelShipmate\MessageQueue\Throttler\Database\DatabaseMessageThrottler::class,

    /*
     * The configuration of the default database message throttler.
     */
    'database_message_throttler' => [

        /*
         * The database table used to store the received messages.
         */
        'table_name' => 'messages',

        /*
         * The maximum number of attempts that a message is handled before it is discarded.
         */
        'maximum_attempts' => 5,

    ],

];

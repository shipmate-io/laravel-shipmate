<?php

return [

    /*
     * The message queues that are available to your service.
     */
    'message_queues' => [
        'default' => env('SHIPMATE_MESSAGE_QUEUE_NAME'),
    ],

    /*
     * The file within your code base that defines your message handlers.
     */
    'message_handlers' => base_path('routes/messages.php'),

    /*
     * Whether to register the routes required to handle the messages from the message queues.
     */
    'register_routes' => true,

];

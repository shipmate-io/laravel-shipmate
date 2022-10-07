<?php

return [

    /*
     * The name of the message queue topic that is used to publish messages.
     */
    'topic' => env('MESSAGE_QUEUE_TOPIC'),

    /*
     * The name of the message queue subscription that is used to receive messages.
     */
    'subscription' => env('MESSAGE_QUEUE_SUBSCRIPTION'),

    /*
     * The file within your code base that defines your message handlers.
     */
    'message_handlers' => base_path('routes/messages.php'),

];

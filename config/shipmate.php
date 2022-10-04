<?php

return [

    /*
     * The credentials used to authenticate with Shipmate.
     */
    'credentials' => [
        'email' => env('SHIPMATE_EMAIL'),
        'key' => env('SHIPMATE_KEY'),
        'project_id' => env('SHIPMATE_PROJECT_ID'),
        'region_id' => env('SHIPMATE_REGION_ID'),
    ],

    'message_queue' => [

        /*
         * The name of the default message queue topic that is used to publish messages.
         */
        'topic' => env('MESSAGE_QUEUE_TOPIC'),

        /*
         * The name of the default message queue subscription that is used to receive messages.
         */
        'subscription' => env('MESSAGE_QUEUE_SUBSCRIPTION'),

        /*
         * The file within your code base that defines your message handlers.
         */
        'message_handlers' => base_path('routes/messages.php'),

    ],

];

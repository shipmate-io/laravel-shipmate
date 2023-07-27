# Interact with Shipmate from your Laravel code

## Installation

You can install the package via composer:

```bash
composer require shipmate-io/laravel-shipmate
```

## Job queue

Add a new queue connection to your `config/queue.php` file:

```php
'shipmate' => [
    'driver' => 'shipmate',
    'default_queue' => 'default',
    'queues' => [
        'default' => [
            'name' => env('SHIPMATE_DEFAULT_JOB_QUEUE_NAME'),
            'worker_url' => env('SHIPMATE_DEFAULT_JOB_QUEUE_WORKER_URL'),
        ],
    ],
],
```

Update the `QUEUE_CONNECTION` environment variable:

```
QUEUE_CONNECTION=shipmate
```

## Message queue

The message queue are configured in the `config/message-queue.php` file.

```php
return [

    /*
     * The message queues that are available to your service.
     */
    'queues' => [
        'default' => env('SHIPMATE_DEFAULT_MESSAGE_QUEUE_NAME'),
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
```

You can publish this file by running the following artisan command:

```bash
php artisan vendor:publish --tag="shipmate-config"
```

A message is a simple class that implements the `Shipmate\LaravelShipmate\MessageQueue\ShouldPublish` interface.

```php
use Shipmate\LaravelShipmate\MessageQueue\ShouldPublish;

class UserCreated implements ShouldPublish
{
    public function publishOn(): string
    {
        return 'default';
    }

    public function publishAs(): string
    {
        return 'user.created';
    }

    public function publishWith(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];
    }
}
```

To publish a message to the message queue, you can dispatch it using Laravel's event helper.

```php
event(new UserCreated);
```

The message queue delivers this message to the other services in your application as an HTTP request. To accept
this request, the package automatically registers the following routes in your service.

```php
Route::post('shipmate/handle-message', [Shipmate\LaravelShipmate\MessageQueue\MessageQueueController::class, 'handleMessage']);
Route::post('shipmate/handle-failed-message', [Shipmate\LaravelShipmate\MessageQueue\MessageQueueController::class, 'handleFailedMessage']);
```

Next, the package looks in the `routes/messages.php` file of your service for a handler that corresponds with the
type of the message. The contents of the file should look like this:

```php
<?php

return [

    'user.created' => HandleUserCreatedMessage::class,

    'user.deleted' => [HandleUserDeletedMessage::class, 'handle'],

];
```

The file must return an associative array in which:
- **a key** is the message type that the application wants to receive
- **a value** is the class within your application that handles the incoming message of this type

A message handler can be defined in two ways:

1. By referencing a class

    ```php
    'user.created' => HandleUserCreatedMessage::class,
    ```

   In this case, the package looks for a public method in the class that accepts a `Shipmate\Shipmate\MessageQueue\Message`
   as argument. This method can be called anything, as shown here:

    ```php
    use Shipmate\Shipmate\MessageQueue\Message;
   
    class HandleUserCreatedMessage
    {
        public function __invoke(Message $message): void
        {
            $firstName = $message->payload['first_name'];
   
            //
        }
    }
    
    class HandleUserCreatedMessage
    {
        public function handle(Message $message): void
        {
            $firstName = $message->payload['first_name'];
   
            //
        }
    }
    
    class HandleUserCreatedMessage
    {
        public function execute(Message $message): void
        {
            $firstName = $message->payload['first_name'];
   
            //
        }
    }
    ```

2. By referencing a class and method

    ```php
    'user.created' => [HandleUserCreatedMessage::class, 'handle'],
    ```

If no handler is registered for a particular type of message, the message is discarded.

## Storage bucket

Add a new disk to your `config/filesystems.php` file:

```php
'shipmate' => [
    'driver' => 'shipmate',
    'bucket' => env('STORAGE_BUCKET_NAME'),
    'visibility' => 'public', // public or private
],
```

Store and retrieve files from your storage bucket:

```php
$disk = Storage::disk('shipmate');

$disk->put('avatars/1', $fileContents);
$exists = $disk->exists('file.jpg');
$time = $disk->lastModified('file1.jpg');
$disk->copy('old/file1.jpg', 'new/file1.jpg');
$disk->move('old/file1.jpg', 'new/file1.jpg');
$url = $disk->url('folder/my_file.txt');
$url = $disk->temporaryUrl('folder/my_file.txt', now()->addMinutes(30));
$disk->setVisibility('folder/my_file.txt', 'public');
```

See [https://laravel.com/docs/master/filesystem](https://laravel.com/docs/master/filesystem) for full list of available functionality.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

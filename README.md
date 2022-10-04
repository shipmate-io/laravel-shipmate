# Fully managed microservice hosting for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/shipmate-io/laravel-shipmate.svg?style=flat-square)](https://packagist.org/packages/shipmate-io/laravel-shipmate)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/shipmate-io/laravel-shipmate/run-tests?label=tests)](https://github.com/shipmate-io/laravel-shipmate/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/shipmate-io/laravel-shipmate/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/shipmate-io/laravel-shipmate/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/shipmate-io/laravel-shipmate.svg?style=flat-square)](https://packagist.org/packages/shipmate-io/laravel-shipmate)

Shipmate is a fully managed hosting platform that eliminates the complexity of shipping microservice apps. This package contains everything you need to make your Laravel microservice run smoothly on Shipmate. To learn more about Shipmate and how to use this package, please consult the official documentation.

## Installation

You can install the package via composer:

```bash
composer require shipmate-io/laravel-shipmate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-shipmate-config"
```

This is the contents of the published config file:

```php
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
```

## Job queues

Add a new queue connection to your `config/queue.php` file:

```php
'shipmate' => [
    'driver' => 'shipmate',
    'queue' => env('QUEUE_NAME'),
    'worker_url' => env('QUEUE_WORKER_URL'),
],
```

Update the `QUEUE_CONNECTION` environment variable:

```
QUEUE_CONNECTION=shipmate
```

## Message handlers

A message is a simple class that implements the `Shipmate\LaravelShipmate\MessageQueue\ShouldPublish` interface.

```php
use Shipmate\LaravelShipmate\MessageQueue\ShouldPublish;

class UserCreated implements ShouldPublish
{
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

The message queue delivers this message to the other microservices in your application as an HTTP request. To accept
this request, the package automatically registers the following route in your microservice.

```php
Route::post('shipmate/handle-message', Shipmate\LaravelShipmate\MessageQueue\RequestHandler::class);
```

Next, the package looks in the `routes/messages.php` file of your microservice for a handler that corresponds with the
type of the message. The contents of the file should look like this:

```php
<?php

return [

    'user.created' => HandleUserCreatedMessage::class,

    'user.deleted' => [HandleUserDeletedMessage::class, 'handle'],

];
```

The file returns an associative array in which:
- **a key** is the message type that the application wants to receive
- **a value** is the class within your application that handles the incoming message of this type

A message handler can be defined in two ways:

1. By referencing a class

    ```php
    'user.created' => HandleUserCreatedMessage::class,
    ```

   In this case, the package looks for a public method in the class that accepts a `Shipmate\LaravelShipmate\MessageQueue\Message`
   as argument. This method can be called anything, as shown here:

    ```php
    use Shipmate\LaravelShipmate\MessageQueue\Message;
   
    class HandleUserCreatedMessage
    {
        public function __invoke(Message $message): void
        {
            $firstName = $message->payload->get('first_name');
   
            //
        }
    }
    
    class HandleUserCreatedMessage
    {
        public function handle(Message $message): void
        {
            $firstName = $message->payload->get('first_name');
   
            //
        }
    }
    
    class HandleUserCreatedMessage
    {
        public function execute(Message $message): void
        {
            $firstName = $message->payload->get('first_name');
   
            //
        }
    }
    ```

2. By referencing a class and method

    ```php
    'user.created' => [HandleUserCreatedMessage::class, 'handle'],
    ```

If no handler is registered for a particular type of message, the message is discarded.

## Storage buckets

Add a new disk to your `config/filesystems.php` file:

```php
'shipmate' => [
    'driver' => 'shipmate',
    'bucket' => env('STORAGE_BUCKET_NAME'),
    'path_prefix' => env('STORAGE_BUCKET_PATH_PREFIX', ''), // e.g. /path/in/bucket
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

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Shipmate](https://github.com/shipmate-io)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

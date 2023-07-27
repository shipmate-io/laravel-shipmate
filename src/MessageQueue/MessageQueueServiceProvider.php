<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Shipmate\Shipmate\MessageQueue\Message;
use Shipmate\Shipmate\MessageQueue\MessageQueue;
use Spatie\LaravelPackageTools\Package;

class MessageQueueServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->hasConfigFile('message-queue')
            ->hasRoutes('messages');
    }

    public function boot(): void
    {
        $this->app->singleton(
            abstract: MessageQueueConfig::class,
            concrete: fn (Container $app) => new MessageQueueConfig($app['config']->get('message-queue'))
        );

        $config = MessageQueueConfig::new();

        $this->registerEventListener($config);
        $this->registerController($config);
    }

    private function registerEventListener(MessageQueueConfig $config): void
    {
        Event::listen('*', function (string $eventName, array $data) use ($config): void {
            $event = $data[0];

            if (! $event instanceof ShouldPublish) {
                return;
            }

            $message = new Message(
                type: $event->publishAs(),
                payload: $event->publishWith()
            );

            $messageQueue = new MessageQueue(
                name: $config->getMessageQueueName($event->publishOn()),
            );

            $messageQueue->publishMessage($message);
        });
    }

    private function registerController(MessageQueueConfig $config): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        if ($config->registerRoutes()) {
            $router->post('shipmate/handle-message', [MessageQueueController::class, 'handleMessage']);
            $router->post('shipmate/handle-failed-message', [MessageQueueController::class, 'handleFailedMessage']);
        }
    }
}

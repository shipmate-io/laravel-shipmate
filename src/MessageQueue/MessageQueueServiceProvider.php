<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Shipmate\LaravelShipmate\MessageQueue\Commands\ConnectMessageQueue;
use Shipmate\LaravelShipmate\MessageQueue\Commands\CreateMessageQueueSubscription;
use Shipmate\LaravelShipmate\MessageQueue\Commands\CreateMessageQueueTopic;
use Shipmate\LaravelShipmate\MessageQueue\Commands\DeleteMessageQueueSubscription;
use Shipmate\LaravelShipmate\MessageQueue\Commands\DeleteMessageQueueTopic;
use Shipmate\LaravelShipmate\MessageQueue\Commands\ListMessageQueueSubscriptions;
use Shipmate\LaravelShipmate\MessageQueue\Commands\ListMessageQueueTopics;
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
            ->hasRoutes('messages')
            ->hasConfigFile('message-queue')
            ->hasMigration('create_messages_table')
            ->hasCommand(ConnectMessageQueue::class)
            ->hasCommand(CreateMessageQueueSubscription::class)
            ->hasCommand(CreateMessageQueueTopic::class)
            ->hasCommand(DeleteMessageQueueSubscription::class)
            ->hasCommand(DeleteMessageQueueTopic::class)
            ->hasCommand(ListMessageQueueSubscriptions::class)
            ->hasCommand(ListMessageQueueTopics::class);
    }

    public function boot(): void
    {
        $this->app->singleton(
            abstract: MessageQueueConfig::class,
            concrete: fn (Container $app) => new MessageQueueConfig($app['config']->get('message-queue'))
        );

        $this->app->singleton(
            abstract: MessageQueue::class,
            concrete: fn (Container $app) => new MessageQueue
        );

        $this->registerEventListener();
        $this->registerRequestHandler();
    }

    private function registerEventListener(): void
    {
        Event::listen('*', function (string $eventName, array $data): void {
            $event = $data[0];

            if (! $event instanceof ShouldPublish) {
                return;
            }

            MessageQueue::new()->publishMessage($event);
        });
    }

    private function registerRequestHandler(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        $router->post('shipmate/handle-message', RequestHandler::class);
    }
}

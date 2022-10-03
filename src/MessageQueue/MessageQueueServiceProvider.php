<?php

namespace Shipmate\Shipmate\MessageQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class MessageQueueServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function boot(): void
    {
        $this->app->singleton(
            abstract: MessageQueueConfig::class,
            concrete: fn (Container $app) => new MessageQueueConfig($app['config']->get('shipmate.message_queue'))
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

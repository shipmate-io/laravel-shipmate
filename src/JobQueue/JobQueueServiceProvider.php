<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\QueueManager;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class JobQueueServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function boot(): void
    {
        $this->registerQueueConnector();
        $this->registerController();
    }

    private function registerQueueConnector(): void
    {
        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        $queue->addConnector('shipmate', fn () => new JobQueueConnector);
    }

    private function registerController(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        $router->post('shipmate/handle-job', [JobQueueController::class, 'handleJob']);
    }
}

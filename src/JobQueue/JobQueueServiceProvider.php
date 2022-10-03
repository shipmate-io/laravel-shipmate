<?php

namespace Shipmate\Shipmate\JobQueue;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Queue\QueueManager;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Shipmate\Shipmate\JobQueue\RequestHandler\RequestHandler;

class JobQueueServiceProvider extends ServiceProvider
{
    public static function new(Application $app): static
    {
        return new static($app);
    }

    public function boot(): void
    {
        $this->registerQueueConnector();
        $this->registerRequestHandler();
    }

    private function registerQueueConnector(): void
    {
        /** @var QueueManager $queue */
        $queue = app('queue');

        $queue->addConnector('shipmate', fn () => new JobQueueConnector);
    }

    private function registerRequestHandler(): void
    {
        /** @var Router $router */
        $router = app('router');

        $router->post('shipmate/handle-job', RequestHandler::class);
    }
}

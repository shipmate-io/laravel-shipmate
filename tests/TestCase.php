<?php

namespace Shipmate\LaravelShipmate\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;
use Shipmate\LaravelShipmate\ShipmateServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Shipmate\\Shipmate\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ShipmateServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        config()->set('queue.connections.shipmate.driver', 'shipmate');
        config()->set('queue.connections.shipmate.topic', 'application');
        config()->set('queue.connections.shipmate.subscription', 'identity');

        config()->set('shipmate.project_id', 'abc123');
        config()->set('shipmate.region_id', 'europe-west1');
        config()->set('shipmate.email', 'john.doe@example.com');
        config()->set('shipmate.key', 'eyJzZWNyZXQiOnRydWV9');

        config()->set('shipmate.message_queue.queue_connection', 'shipmate');
        config()->set('shipmate.message_queue.message_handlers', __DIR__.'/MessageQueue/messages.php');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-shipmate_table.php.stub';
        $migration->up();
        */
    }

    /**
     * Set up Pub/Sub env, creating the topic and subscription.
     */
    protected function setUpPubSub(): void
    {
        putenv('PUBSUB_EMULATOR_HOST=0.0.0.0:8085');

        $messageQueue = MessageQueue::new();
        $messageQueue->createTopic('application');
        $messageQueue->createSubscription('identity', 'application', 'TODO');
    }

    /**
     * Delete Pub/Sub env, deleting the topic and subscription.
     */
    protected function deletePubSub(): void
    {
        $messageQueue = MessageQueue::new();
        $messageQueue->deleteSubscription('identity', 'application');
        $messageQueue->deleteTopic('application');
    }

    protected function makePartialMock(string $class, array $arguments = []): Mockery\MockInterface
    {
        $mock = Mockery::mock($class, $arguments)->makePartial();
        $this->app->bind($class, fn () => $mock);

        return $mock;
    }
}

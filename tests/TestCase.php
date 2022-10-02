<?php

namespace Shipmate\Shipmate\Tests;

use Hyperlab\LaravelGoogleCloudTasks\Api\GoogleCloudTasks;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Shipmate\Shipmate\ShipmateServiceProvider;

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

        $config->set('queue.connections.pubsub.driver', 'gcp_pubsub');
        $config->set('queue.connections.pubsub.subscription', 'pull_test');
        $config->set('queue.connections.pubsub.topic', 'pubsub_package_tests');

        $config->set('pubsub.queue_connection', 'pubsub');
        $config->set('pubsub.message_handlers', __DIR__.'/messages.php');

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

        GoogleCloudTasks::new()
            ->createTopic()
            ->createSubscription();
    }

    /**
     * Delete Pub/Sub env, deleting the topic and subscription.
     */
    protected function deletePubSub(): void
    {
        GoogleCloudTasks::new()
            ->deleteSubscription()
            ->deleteTopic();
    }

    protected function makePartialMock(string $class, array $arguments = []): Mockery\MockInterface
    {
        $mock = Mockery::mock($class, $arguments)->makePartial();
        $this->app->bind($class, fn () => $mock);

        return $mock;
    }
}

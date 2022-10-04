<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Google\Cloud\PubSub\Subscription;
use Illuminate\Console\Command;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;

class ListMessageQueueSubscriptions extends Command
{
    public $signature = '
        message-queue:list-subscriptions
        {topic : The name of the topic}
    ';

    public $description = 'List all subscription of a topic.';

    public function handle(): int
    {
        $topicName = $this->argument('topic');

        $this->output->info('Listing subscriptions...');

        $subscriptions = MessageQueue::new()->listSubscriptions($topicName);

        $this->table(
            headers: ['Name'],
            rows: array_map(fn (Subscription $subscription) => [$subscription->name()], $subscriptions),
        );

        return self::SUCCESS;
    }
}

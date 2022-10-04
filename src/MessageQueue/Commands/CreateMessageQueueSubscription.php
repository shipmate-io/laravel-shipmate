<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Illuminate\Console\Command;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;

class CreateMessageQueueSubscription extends Command
{
    public $signature = '
        message-queue:create-subscription
        {name : The name of the subscription}
        {topic : The name of the topic}
        {endpoint : The url to push the messages to}
    ';

    public $description = 'Create a subscription.';

    public function handle(): int
    {
        $name = $this->argument('name');
        $topicName = $this->argument('topic');
        $endpoint = $this->argument('endpoint');

        $this->output->info('Creating subscription...');
        MessageQueue::new()->createSubscription($name, $topicName, $endpoint);
        $this->output->success('Subscription created');

        return self::SUCCESS;
    }
}

<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Illuminate\Console\Command;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;

class DeleteMessageQueueSubscription extends Command
{
    public $signature = '
        message-queue:delete-subscription
        {name : The name of the subscription}
        {topic : The name of the topic}
    ';

    public $description = 'Delete a subscription.';

    public function handle(): int
    {
        $name = $this->argument('name');
        $topicName = $this->argument('topic');

        $this->output->info('Deleting topic...');
        MessageQueue::new()->deleteSubscription($name, $topicName);
        $this->output->success('Topic deleted');

        return self::SUCCESS;
    }
}

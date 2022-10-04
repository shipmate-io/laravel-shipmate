<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Illuminate\Console\Command;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;

class CreateMessageQueueTopic extends Command
{
    public $signature = '
        message-queue:create-topic
        {name : The name of the topic}
    ';

    public $description = 'Create a topic.';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->output->info('Creating topic...');
        MessageQueue::new()->createTopic($name);
        $this->output->success('Topic created');

        return self::SUCCESS;
    }
}

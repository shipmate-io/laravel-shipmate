<?php

namespace Shipmate\Shipmate\MessageQueue\Commands;

use Illuminate\Console\Command;
use Shipmate\Shipmate\MessageQueue\MessageQueue;

class DeleteMessageQueueTopic extends Command
{
    public $signature = '
        message-queue:delete-topic
        {name : The name of the topic}
    ';

    public $description = 'Delete a topic.';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->output->info('Deleting topic...');
        MessageQueue::new()->deleteTopic($name);
        $this->output->success('Topic deleted');

        return self::SUCCESS;
    }
}

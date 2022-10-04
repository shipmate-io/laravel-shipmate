<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Google\Cloud\PubSub\Topic;
use Illuminate\Console\Command;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;

class ListMessageQueueTopics extends Command
{
    public $signature = '
        message-queue:list-topics
    ';

    public $description = 'List all topics.';

    public function handle(): int
    {
        $this->output->info('Listing topics...');

        $topics = MessageQueue::new()->listTopics();

        $this->table(
            headers: ['Name'],
            rows: array_map(fn (Topic $topic) => [$topic->name()], $topics),
        );

        return self::SUCCESS;
    }
}

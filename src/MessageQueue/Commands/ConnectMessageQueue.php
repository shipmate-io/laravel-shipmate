<?php

namespace Shipmate\LaravelShipmate\MessageQueue\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueue;
use Shipmate\LaravelShipmate\MessageQueue\MessageQueueNotReadyYet;

class ConnectMessageQueue extends Command
{
    public $signature = '
        message-queue:connect
        {--timeout=60 : Time in seconds that connecting should be attempted}
    ';

    public $description = 'Wait for the connection with the message queue to be established.';

    public function handle(): int
    {
        $messageQueue = MessageQueue::new();

        $this->output->info('Waiting for a successful connection with the message queue...');

        $connected = false;
        $timeout = $this->getTimeout();

        $this->output->progressStart($timeout);

        do {
            try {
                $messageQueue->connect();
                $connected = true;
            } catch (MessageQueueNotReadyYet $e) {
                if ($timeout <= 0) {
                    throw $e;
                }
                $timeout--;
                sleep(1);
                $this->output->progressAdvance();
            }
        } while (! $connected);

        $this->output->progressFinish();

        $this->output->success('Successfully established a connection with the message queue.');

        return self::SUCCESS;
    }

    private function getTimeout(): int
    {
        $timeout = $this->option('timeout') ?? 60;

        if (! is_numeric($timeout)) {
            throw new InvalidArgumentException('Must pass an integer to option: timeout');
        }

        return (int) $timeout;
    }
}

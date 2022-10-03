<?php

namespace Shipmate\Shipmate\JobQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job as LaravelJob;
use Shipmate\Shipmate\JobQueue\Google\GoogleClient;

class Job extends LaravelJob implements JobContract
{
    public function __construct(
        protected GoogleClient $googleClient,
        protected JobPayload $jobPayload,
        protected string $jobName,
        protected int $attempts,
        protected int $maxTries,
        protected ?int $retryUntil,
        protected $connectionName,
        protected $queue,
    ) {
        $this->container = Container::getInstance();
    }

    public function getJobId(): string
    {
        return $this->jobPayload->getId();
    }

    public function uuid(): string
    {
        return $this->jobPayload->getId();
    }

    public function getRawBody(): string
    {
        return $this->jobPayload->toJson();
    }

    public function attempts(): ?int
    {
        return $this->attempts;
    }

    public function maxTries(): ?int
    {
        return $this->maxTries;
    }

    public function retryUntil(): ?int
    {
        return $this->retryUntil;
    }

    public function delete(): void
    {
        parent::delete();

        $this->googleClient->deleteJob(
            queueName: $this->queue,
            jobName: $this->jobName,
        );
    }

    public function fire(): void
    {
        $this->attempts++;

        parent::fire();
    }
}

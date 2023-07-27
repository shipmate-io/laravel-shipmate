<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as IlluminateJobContract;
use Illuminate\Queue\Jobs\Job as IlluminateJob;

class QueueJob extends IlluminateJob implements IlluminateJobContract
{
    protected ?int $attempts;

    public function __construct(
        protected JobQueue $jobQueue,
        protected Job $job,
        protected int $maxTries,
        protected ?int $retryUntil,
        protected $connectionName,
        protected $queue,
    ) {
        $this->container = Container::getInstance();
        $this->attempts = $this->job->getAttempts();
    }

    public function getJobId(): string
    {
        return $this->job->getId();
    }

    public function uuid(): string
    {
        return $this->job->getId();
    }

    public function getRawBody(): string
    {
        return $this->job->toJson();
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

        $this->jobQueue->deleteJob(
            jobName: $this->job->getName(),
        );
    }

    public function fire(): void
    {
        $this->attempts++;

        parent::fire();
    }
}

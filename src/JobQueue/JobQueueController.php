<?php

namespace Shipmate\LaravelShipmate\JobQueue;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JobQueueController
{
    public function handleJob(Request $request): Response
    {
        $requestPayload = (string) $request->getContent();

        $job = JobQueue::parseJob($requestPayload);
        $job->setName($request->header('X-Cloudtasks-Taskname'));
        $job->setAttempts((int) $request->header('X-CloudTasks-TaskExecutionCount'));

        JobHandler::new()->handle(
            job: $job,
            bearerToken: $request->bearerToken(),
        );

        return new Response;
    }
}

<?php

namespace Shipmate\Shipmate\JobQueue;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Shipmate\Shipmate\ShipmateException;
use Shipmate\Shipmate\Support\OpenId;

class RequestHandler
{
    public function __invoke(Request $request): Response
    {
        $jobPayload = $this->parseJobPayload($request);

        OpenId::new()->validateToken(
            token: $this->parseBearerToken($request),
            audience: $this->getAudience($jobPayload),
        );

        JobHandler::new()->handle(
            jobPayload: $jobPayload,
            jobName: $this->parseJobName($request),
            jobExecutionCount: $this->parseJobExecutionCount($request)
        );

        return new Response;
    }

    private function parseJobPayload(Request $request): JobPayload
    {
        $input = (string) $request->getContent();

        if (! $input) {
            throw new ShipmateException('Could not read incoming job', 422);
        }

        $jobPayload = json_decode($input, true);

        if (! is_array($jobPayload)) {
            throw new ShipmateException('Could not decode incoming job', 422);
        }

        return new JobPayload($jobPayload);
    }

    private function parseBearerToken(Request $request): string
    {
        $token = $request->bearerToken();

        if (! $token) {
            throw new ShipmateException('Missing [Authorization] header');
        }

        return $token;
    }

    private function getAudience(JobPayload $jobPayload): string
    {
        $connectionName = $jobPayload->getConnectionName() ?? config('queue.default');

        $config = JobQueueConfig::readFromConnection($connectionName);

        return $config->getWorkerUrl();
    }

    private function parseJobName(Request $request): string
    {
        $jobName = $request->header('X-Cloudtasks-Taskname');

        if (! is_string($jobName)) {
            throw new ShipmateException('Expected job name to be a string.', 422);
        }

        return $jobName;
    }

    private function parseJobExecutionCount(Request $request): int
    {
        return (int) $request->header('X-CloudTasks-TaskExecutionCount');
    }
}

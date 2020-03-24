<?php

namespace Imtigger\LaravelJobStatus;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;

class JobStatusUpdater
{
    public function update($job, array $data)
    {
        if ($this->isEvent($job)) {
            $this->updateEvent($job, $data);
        }

        $this->updateJob($job, $data);
    }

    /**
     * @param JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event
     */
    private function updateEvent($event, array $data)
    {
        $job = $this->parseJob($event);
        $jobStatus = $this->getJobStatus($job);

        if (!$jobStatus) {
            return;
        }

        try {
            $data['attempts'] = $job->attempts();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        $jobStatus->update($data);
    }

    private function updateJob($job, array $data)
    {
        if ($jobStatus = $this->getJobStatus($job)) {
            $jobStatus->update($data);
        }
    }

    /**
     * @param  JobProcessing|JobProcessed|JobFailed|JobExceptionOccurred $event
     * @return mixed|null
     */
    private function parseJob($event)
    {
        try {
            $payload = $event->job->payload();

            return unserialize($payload['data']['command']);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    private function getJobStatusId($job)
    {
        if ($job instanceof TrackableJob || method_exists($job, 'getJobStatusId')) {
            return $job->getJobStatusId();
        }

        return null;
    }

    private function getJobStatus($job)
    {
        if ($id = $this->getJobStatusId($job)) {
            /** @var JobStatus $entityClass */
            $entityClass = app(config('job-status.model'));

            return $entityClass::whereKey($id)->first();
        }

        return null;
    }

    private function isEvent($job)
    {
        return $job instanceof JobProcessing
            || $job instanceof JobProcessed
            || $job instanceof JobFailed
            || $job instanceof JobExceptionOccurred;
    }
}

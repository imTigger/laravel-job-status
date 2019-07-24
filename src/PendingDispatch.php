<?php

namespace Imtigger\LaravelJobStatus;

use Illuminate\Contracts\Bus\Dispatcher;

class PendingDispatch extends \Illuminate\Foundation\Bus\PendingDispatch
{
    public function __destruct()
    {
        $this->updateJobStatus(app(Dispatcher::class)->dispatch($this->job));
    }

    private function updateJobStatus($jobId)
    {
        if ($jobStatus = $this->getJobStatus()) {
            $jobStatus->update([
                'status' => $jobStatus::STATUS_EXECUTING,
                'job_id' => $jobId,
                'queue' => $this->job->queue ?: 'default',
            ]);
        }
    }

    public function getJobStatusId()
    {
        if (method_exists($this->job, 'getJobStatusId')) {
            return $this->job->getJobStatusId();
        }
    }

    /**
     * @return JobStatus|null
     */
    public function getJobStatus()
    {
        /** @var \Imtigger\LaravelJobStatus\JobStatus $entityClass */
        $entityClass = app(config('job-status.model'));

        return $entityClass::query()->find($this->getJobStatusId());
    }
}

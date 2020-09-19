<?php

namespace Imtigger\LaravelJobStatus\EventManagers;

use Carbon\Carbon;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class DefaultEventManager extends EventManager
{
    public function before(JobProcessing $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => $this->getEntity()::STATUS_EXECUTING,
            'job_id' => $event->job->getJobId(),
            'queue' => $event->job->getQueue(),
            'started_at' => Carbon::now(),
        ]);
    }

    public function after(JobProcessed $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => $this->getEntity()::STATUS_FINISHED,
            'finished_at' => Carbon::now(),
        ]);
    }

    public function failing(JobFailed $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => $this->getEntity()::STATUS_FAILED,
            'finished_at' => Carbon::now(),
        ]);
    }

    public function exceptionOccurred(JobExceptionOccurred $event): void
    {
        $this->getUpdater()->update($event, [
            'status' => ($event->job->attempts() === $event->job->maxTries()) ? $this->getEntity()::STATUS_FAILED : $this->getEntity()::STATUS_RETRYING,
            'finished_at' => Carbon::now(),
            'output' => ['message' => $event->exception->getMessage()],
        ]);
    }
}

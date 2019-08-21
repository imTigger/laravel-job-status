<?php

namespace Imtigger\LaravelJobStatus\EventManagers;

use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Imtigger\LaravelJobStatus\JobStatus;
use Imtigger\LaravelJobStatus\JobStatusUpdater;

abstract class EventManager
{
    abstract public function before(JobProcessing $event): void;

    abstract public function after(JobProcessed $event): void;

    abstract public function failing(JobFailed $event): void;

    abstract public function exceptionOccurred(JobExceptionOccurred $event): void;

    private $updater;

    private $entity;

    public function __construct(JobStatusUpdater $updater)
    {
        $this->updater = $updater;
        $this->entity = app(config('job-status.model'));
    }

    /**
     * @return JobStatusUpdater
     */
    protected function getUpdater()
    {
        return $this->updater;
    }

    /**
     * @return JobStatus
     */
    protected function getEntity()
    {
        return $this->entity;
    }
}

<?php

namespace Imtigger\LaravelJobStatus;

trait Trackable
{
    /** @var int $statusId */
    protected $statusId;

    protected function setProgressMax($value)
    {
        $this->update(['progress_max' => $value]);
    }

    protected function setProgressNow($value)
    {
        $this->update(['progress_now' => $value]);
    }

    protected function setInput($value)
    {
        $this->update(['input' => $value]);
    }

    protected function setOutput($value)
    {
        $this->update(['output' => $value]);
    }

    protected function update($data) {
        $task = JobStatus::find($this->statusId);

        if ($task != null) {
            return $task->update($data);
        }
    }

    protected function prepareStatus() {
        $status = JobStatus::create([
            'type' => static::class
        ]);
        $this->statusId = $status->id;
    }

    public function getJobStatusId()
    {
        if ($this->statusId == null) {
            throw new \Exception("Failed to get jobStatusId, have you called \$this->prepareStatus() in __construct() of Job?");
        }

        return $this->statusId;
    }
}

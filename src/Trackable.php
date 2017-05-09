<?php

namespace Imtigger\LaravelJobStatus;

trait Trackable
{
    /** @var int $statusId */
    protected $statusId;

    protected function setProgressMax(int $value)
    {
        $this->update(['progress_max' => $value]);
    }

    protected function setProgressNow(int $value, int $updateEvery = 1)
    {
        if ($value % $updateEvery == 0) {
            $this->update(['progress_now' => $value]);
        }
    }

    protected function setInput(array $value)
    {
        $this->update(['input' => $value]);
    }

    protected function setOutput(array $value)
    {
        $this->update(['output' => $value]);
    }

    protected function update(array $data) {
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

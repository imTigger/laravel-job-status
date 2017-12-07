<?php

namespace Imtigger\LaravelJobStatus;

trait Trackable
{
    /** @var int $statusId */
    protected $statusId;
    protected $progressNow = 0;

    protected function setProgressMax($value)
    {
        $this->update(['progress_max' => $value]);
    }

    protected function setProgressNow($value, $every = 1)
    {
        if ($value % $every == 0) {
            $this->update(['progress_now' => $value]);
        }
        $this->progressNow = $value;
    }
    
    protected function incrementProgress($offset = 1, $every = 1)
    {
        $value = $this->progressNow + $offset;
        $this->setProgressNow($value, $every);
    }

    protected function setInput(array $value)
    {
        $this->update(['input' => $value]);
    }

    protected function setOutput(array $value)
    {
        $this->update(['output' => $value]);
    }

    protected function update(array $data)
    {
        $entityClass = config('job-status.class', JobStatus::class);

        $status = $entityClass::find($this->statusId);

        if ($status != null) {
            return $status->update($data);
        }
    }

    protected function prepareStatus()
    {
        $entityClass = config('job-status.class', JobStatus::class);

        $status = $entityClass::create([
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

<?php

namespace Imtigger\LaravelJobStatus;

use Illuminate\Database\Eloquent\Model;

/**
 * Imtigger\LaravelJobStatus.
 *
 * @property int    $id
 * @property string $job_id
 * @property string $type
 * @property string $queue
 * @property int    $attempts
 * @property int    $progress_now
 * @property int    $progress_max
 * @property string $status
 * @property string $input
 * @property string $output
 * @property string $created_at
 * @property string $started_at
 * @property string $finished_at
 * @property mixed  $is_ended
 * @property mixed  $is_executing
 * @property mixed  $is_failed
 * @property mixed  $is_finished
 * @property mixed  $is_queued
 * @property mixed  $is_retrying
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereAttempts($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereCreatedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereFinishedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereId($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereInput($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereJobId($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereOutput($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereProgressMax($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereProgressNow($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereQueue($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereStartedAt($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereStatus($value)
 * @method   static \Illuminate\Database\Query\Builder|\Imtigger\LaravelJobStatus\JobStatus whereType($value)
 * @mixin \Eloquent
 */
class JobStatus extends Model
{
    const STATUS_QUEUED = 'queued';
    const STATUS_EXECUTING = 'executing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';
    const STATUS_RETRYING = 'retrying';

    public $dates = ['started_at', 'finished_at', 'created_at', 'updated_at'];
    protected $guarded = [];

    protected $casts = [
        'input' => 'array',
        'output' => 'array',
    ];

    /* Accessor */
    public function getProgressPercentageAttribute()
    {
        return $this->progress_max !== 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }

    public function getIsEndedAttribute()
    {
        return \in_array($this->status, [self::STATUS_FAILED, self::STATUS_FINISHED], true);
    }

    public function getIsFinishedAttribute()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function getIsFailedAttribute()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getIsExecutingAttribute()
    {
        return $this->status === self::STATUS_EXECUTING;
    }

    public function getIsQueuedAttribute()
    {
        return $this->status === self::STATUS_QUEUED;
    }

    public function getIsRetryingAttribute()
    {
        return $this->status === self::STATUS_RETRYING;
    }

    public static function getAllowedStatuses()
    {
        return [
            self::STATUS_QUEUED,
            self::STATUS_EXECUTING,
            self::STATUS_FINISHED,
            self::STATUS_FAILED,
            self::STATUS_RETRYING,
        ];
    }
}

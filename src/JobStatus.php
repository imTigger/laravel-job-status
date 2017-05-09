<?php

namespace Imtigger\LaravelJobStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Imtigger\LaravelJobStatus
 *
 * @property int $id
 * @property string $job_id
 * @property string $type
 * @property string $queue
 * @property int $attempts
 * @property int $progress_now
 * @property int $progress_max
 * @property string $status
 * @property string $input
 * @property string $output
 * @property string $created_at
 * @property string $started_at
 * @property string $finished_at
 * @property-read mixed $is_ended
 * @property-read mixed $is_executing
 * @property-read mixed $is_failed
 * @property-read mixed $is_finished
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereAttempts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereFinishedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereInput($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereJobId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereOutput($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereProgressMax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereProgressNow($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereQueue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereStartedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\JobStatus whereType($value)
 * @mixin \Eloquent
 */
class JobStatus extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    /* Accessor */
    public function getInputAttribute($value) {
        return json_decode($value);
    }

    public function getOutputAttribute($value) {
        return json_decode($value);
    }

    public function getProgressPercentageAttribute() {
        return $this->progress_max != 0 ? round(100 * $this->progress_now / $this->progress_max) : 0;
    }
    
    public function getIsEndedAttribute() {
        return in_array($this->status, ['failed', 'finished']);
    }

    public function getIsFinishedAttribute() {
        return in_array($this->status, ['finished']);
    }

    public function getIsFailedAttribute() {
        return in_array($this->status, ['failed']);
    }
    
    public function getIsExecutingAttribute() {
        return in_array($this->status, ['executing']);
    }

    /* Mutator */
    public function setInputAttribute($value) {
        $this->attributes['input'] = json_encode($value);
    }

    public function setOutputAttribute($value) {
        $this->attributes['output'] = json_encode($value);
    }
}

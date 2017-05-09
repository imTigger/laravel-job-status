# Laravel Job Status

Add ability to track Job progress and result after dispatched to Queue.

## Installation

### Requirements

- PHP >= 5.6.4
- Laravel >= 5.3

### Composer

This plugin can only be installed from [Composer](https://getcomposer.org/).

Run the following command:
```
$ composer require imtigger/laravel-job-status
```

### Usage

In your Job, use `Trackable` trait and call `$this->prepareStatus()` in constructor.

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Imtigger\LaravelJobStatus\Trackable;

class TrackableJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public function __construct(array $params)
    {
        $this->prepareStatus();
        $this->params = $params; // Optional
        $this->setInput($this->params); // Optional
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $max = mt_rand(5, 30);
        $this->setProgressMax($max);

        for ($i = 0; $i <= $max; $i += 1) {
            sleep(1); // Some Long Operations
            $this->setProgressNow($i);
        }

        $this->setOutput(['total' => $max, 'other' => 'parameter']);
    }
}

```

In your Job dispatcher:

```php
<?php
$job = new TrackableJob([]);
$this->dispatch($job);

$jobStatusId = $job->getJobStatusId();
```

Once you have jobStatusId, you can show job status, progress and output to user.

```php
<?php
$jobStatus = JobStatus::find($jobStatusId);

$jobStatus->job_id; // String
$jobStatus->type; // String
$jobStatus->queue; // String
$jobStatus->attempts; // Integer
$jobStatus->progress_now; // Integer
$jobStatus->progress_max; // Integer
$jobStatus->input;  // Array
$jobStatus->output; // Array
$jobStatus->created_at; // Carbon object
$jobStatus->updated_at; // Carbon Object
$jobStatus->started_at; // Carbon object
$jobStatus->finished_at; // Carbon object

// Generated fields
$jobStatus->progress_percentage; // Double, 0~100
$jobStatus->is_ended; // Boolean
$jobStatus->is_executing; // Boolean
$jobStatus->is_failed; // Boolean
$jobStatus->is_finished; // Boolean
```

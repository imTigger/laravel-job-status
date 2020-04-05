# Laravel Job Status

[![Latest Stable Version](https://poser.pugx.org/imTigger/laravel-job-status/v/stable)](https://packagist.org/packages/imTigger/laravel-job-status)
[![Total Downloads](https://poser.pugx.org/imTigger/laravel-job-status/downloads)](https://packagist.org/packages/imTigger/laravel-job-status)
[![Build Status](https://travis-ci.org/imTigger/laravel-job-status.svg?branch=master)](https://travis-ci.org/imTigger/laravel-job-status)
[![License](https://poser.pugx.org/imTigger/laravel-job-status/license)](https://packagist.org/packages/imTigger/laravel-job-status)


Laravel package to add ability to track `Job` progress, status and result dispatched to `Queue`.

- Queue name, attempts, status and created/updated/started/finished timestamp.
- Progress update, with arbitrary current/max value and percentage auto calculated
- Handles failed job with exception message
- Custom input/output
- Native Eloquent model `JobStatus`
- Support all drivers included in Laravel (null/sync/database/beanstalkd/redis/sqs)

- This package intentionally do not provide any UI for displaying Job progress.

  If you have such need, please refer to [laravel-job-status-progress-view](https://github.com/imTigger/laravel-job-status-progress-view)  
  
  or make your own implementation using `JobStatus` model

## Requirements

- PHP >= 7.1
- Laravel/Lumen >= 5.5

## Installation

[Installation for Laravel](INSTALL.md)

[Installation for Lumen](INSTALL_LUMEN.md)

### Usage

In your `Job`, use `Trackable` trait and call `$this->prepareStatus()` in constructor.

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

In your Job dispatcher, call `$job->getJobStatusId()` to get `$jobStatusId`:

```php
<?php

class YourController {
    use DispatchesJobs;

    function go() {
        $job = new TrackableJob([]);
        $this->dispatch($job);

        $jobStatusId = $job->getJobStatusId();
    }
}
```

`$jobStatusId` can be used elsewhere to retrieve job status, progress and output.

```php
<?php
$jobStatus = JobStatus::find($jobStatusId);
```
### Troubleshooting

#### Call to undefined method ...->getJobStatusId()

Laravel provide many ways to dispatch Jobs. Not all methods return your Job object, for example:

```php
<?php
YourJob::dispatch(); // Returns PendingDispatch instead of YourJob object, leaving no way to retrive `$job->getJobStatusId();`
```

If you really need to dispatch job in this way, workarounds needed: Create your own key

1. Create migration adding extra key to job_statuses table.

2. In your job, generate your own unique key and pass into `prepareStatus();`, `$this->prepareStatus(['key' => $params['key']]);`

3. Find JobStatus another way: `$jobStatus = JobStatus::whereKey($key)->firstOrFail();`

#### Status not updating until transaction commited

On version >= 1.1, dedicated database connection support is added.

Therefore JobStatus updates can be saved instantly even within your application transaction.

Read setup step 6 for instructions.


## Documentations

```php
<?php
// Job protected methods (Call from your Job)
$this->prepareStatus();                           // Must be called in constructor before any other methods
$this->setProgressMax(int $v);                    // Update the max number of progress
$this->setProgressNow(int $v);                    // Update the current number progress
$this->setProgressNow(int $v, int $every);        // Update the current number progress, write to database only when $v % $every == 0
$this->incrementProgress(int $offset)             // Increase current number progress by $offset
$this->incrementProgress(int $offset, int $every) // Increase current number progress by $offset, write to database only when $v % $every == 0
$this->setInput(array $v);                        // Store input into database
$this->setOutput(array $v);                       // Store output into database (Typically the run result)

// Job public methods (Call from your Job dispatcher)
$job->getJobStatusId();                       // Return the primary key of JobStatus (To retrieve status later)

// JobStatus object fields
var_dump($jobStatus->job_id);                 // String (Result varies with driver, see note)
var_dump($jobStatus->type);                   // String
var_dump($jobStatus->queue);                  // String
var_dump($jobStatus->status);                 // String [queued|executing|finished|retrying|failed]
var_dump($jobStatus->attempts);               // Integer
var_dump($jobStatus->progress_now);           // Integer
var_dump($jobStatus->progress_max);           // Integer
var_dump($jobStatus->input);                  // Array
var_dump($jobStatus->output);                 // Array, ['message' => $exception->getMessage()] if job failed
var_dump($jobStatus->created_at);             // Carbon object
var_dump($jobStatus->updated_at);             // Carbon object
var_dump($jobStatus->started_at);             // Carbon object
var_dump($jobStatus->finished_at);            // Carbon object

// JobStatus generated fields
var_dump($jobStatus->progress_percentage);    // Double [0-100], useful for displaying progress bar
var_dump($jobStatus->is_ended);               // Boolean, true if status == finished || status == failed
var_dump($jobStatus->is_executing);           // Boolean, true if status == executing
var_dump($jobStatus->is_failed);              // Boolean, true if status == failed
var_dump($jobStatus->is_finished);            // Boolean, true if status == finished
var_dump($jobStatus->is_queued);              // Boolean, true if status == queued
var_dump($jobStatus->is_retrying);            // Boolean, true if status == retrying
```

# Note 

`$jobStatus->job_id` result varys with driver

| Driver     | job_id
| ---------- | --------
| null       | NULL (Job not run at all!)
| sync       | empty string
| database   | integer
| beanstalkd | integer 
| redis      | string(32)
| sqs        | GUID 

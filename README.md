# Laravel Job Status

Add ability to track Job progress and result after dispatched to Queue.

## Requirements

- PHP >= 5.6.4
- Laravel >= 5.3

## Installation

This plugin can only be installed from [Composer](https://getcomposer.org/).

Run the following command:
```
$ composer require imtigger/laravel-job-status
```

Add the following to your `config/app.php`:

```php
'providers' => [
    ...
    Imtigger\LaravelJobStatus\LaravelJobStatusServiceProvider::class,
]
```

And run:

```bash
php artisan migrate
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
```

## Documentations

```php
<?php
// Job protected methods
$this->prepareStatus(); // Must be called in constructor before any other methods
$this->setProgressMax(int $v); // Update the max number of progress
$this->setProgressNow(int $v); // Update the current number progress
$this->setInput(array $v); // Store input into database
$this->setOutput(array $v); // Store output into database (Typically the run result)


// Job public methods
$job->getJobStatusId(); // Return the primary key of JobStatus (To retrieve status later)

// JobStatus fields
var_dump($jobStatus->job_id);                 // String
var_dump($jobStatus->type);                   // String
var_dump($jobStatus->queue);                  // String
var_dump($jobStatus->status);                 // String [queued|executing|finished|failed]
var_dump($jobStatus->attempts);               // Integer
var_dump($jobStatus->progress_now);           // Integer
var_dump($jobStatus->progress_max);           // Integer
var_dump($jobStatus->input);                  // Array
var_dump($jobStatus->output);                 // Array
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
```

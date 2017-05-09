<?php

namespace Imtigger\LaravelJobStatus;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Log;

class LaravelJobStatusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) {
            $this->updateJobStatus($event->job, [
                'status' => 'executing',
                'attempts' => $event->job->attempts(),
                'queue' => $event->job->getQueue(),
                'started_at' => Carbon::now()
            ]);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) {
            $this->updateJobStatus($event->job, [
                'status' => 'finished',
                'attempts' => $event->job->attempts(),
                'finished_at' => Carbon::now()
            ]);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) {
            $this->updateJobStatus($event->job, [
                'status' => 'failed',
                'attempts' => $event->job->attempts(),
                'finished_at' => Carbon::now()
            ]);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) {
            $this->updateJobStatus($event->job, [
                'status' => 'failed',
                'attempts' => $event->job->attempts(),
                'finished_at' => Carbon::now(),
                'output' => $event->exception->getMessage()
            ]);
        });
    }

    private function updateJobStatus(Job $job, array $data)
    {
        try {
            $payload = $job->payload();
            $jobStatus = unserialize($payload['data']['command']);
            $jobStatusId = $jobStatus->getJobStatusId();

            $jobStatus = JobStatus::where('id', '=', $jobStatusId);
            return $jobStatus->update($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
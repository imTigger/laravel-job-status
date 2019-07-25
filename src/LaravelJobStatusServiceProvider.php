<?php

namespace Imtigger\LaravelJobStatus;

use Carbon\Carbon;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class LaravelJobStatusServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->mergeConfigFrom(__DIR__ . '/../config/job-status.php', 'job-status');

        $this->publishes([
            __DIR__ . '../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'config');

        $this->bootListeners();
    }

    private function bootListeners()
    {
        /** @var JobStatus $entityClass */
        $entityClass = app(config('job-status.model'));

        /** @var JobStatusUpdater $updater */
        $updater = app(JobStatusUpdater::class);

        // Add Event listeners
        app(QueueManager::class)->before(function (JobProcessing $event) use ($updater, $entityClass) {
            $updater->update($event, [
                'status' => $entityClass::STATUS_EXECUTING,
                'job_id' => $event->job->getJobId(),
                'queue' => $event->job->getQueue(),
                'started_at' => Carbon::now(),
            ]);
        });
        app(QueueManager::class)->after(function (JobProcessed $event) use ($updater, $entityClass) {
            $updater->update($event, [
                'status' => $entityClass::STATUS_FINISHED,
                'finished_at' => Carbon::now(),
            ]);
        });
        app(QueueManager::class)->failing(function (JobFailed $event) use ($updater, $entityClass) {
            $updater->update($event, [
                'status' => $entityClass::STATUS_FAILED,
                'finished_at' => Carbon::now(),
            ]);
        });
        app(QueueManager::class)->exceptionOccurred(function (JobExceptionOccurred $event) use ($updater, $entityClass) {
            $updater->update($event, [
                'status' => $entityClass::STATUS_FAILED,
                'finished_at' => Carbon::now(),
                'output' => json_encode(['message' => $event->exception->getMessage()]),
            ]);
        });
    }
}

<?php

namespace Imtigger\LaravelJobStatus;

use Closure;
use Illuminate\Contracts\Container\Container;

class Dispatcher extends \Illuminate\Bus\Dispatcher
{
    private $updater;

    public function __construct(Container $container, Closure $queueResolver, JobStatusUpdater $updater)
    {
        $this->updater = $updater;

        parent::__construct($container, $queueResolver);
    }

    public function dispatch($command)
    {
        $result = parent::dispatch($command);

        $this->updater->update($command, [
            'job_id' => $result,
        ]);

        return $result;
    }
}

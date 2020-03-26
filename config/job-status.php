<?php

return [
    'model' => \Imtigger\LaravelJobStatus\JobStatus::class,
    'event_manager' => \Imtigger\LaravelJobStatus\EventManagers\DefaultEventManager::class,
    'database_connection' => null
];

<?php

namespace Imtigger\LaravelJobStatus\Tests;


use Imtigger\LaravelJobStatus\JobStatus;
use Orchestra\Testbench\TestCase;

class JobStatusTest extends TestCase
{

    public function testGetAndSetsInputAttribute()
    {
        $jobStatus = new JobStatus();
        $jobStatus->input = ['first' => 'attr', 'second' => 'attribute'];

        $this->assertEquals($jobStatus->input, ['first' => 'attr', 'second' => 'attribute']);
    }

    public function testGetAndSetsOutputAttribute()
    {
        $jobStatus = new JobStatus();
        $jobStatus->output = ['first' => 'attr', 'second' => 'attribute'];

        $this->assertEquals($jobStatus->output, ['first' => 'attr', 'second' => 'attribute']);
    }

    public function testProgressPercentageAttributeProgressMax0()
    {
        $jobStatus = new JobStatus();
        $jobStatus->progress_max = 0;

        $this->assertEquals(0, $jobStatus->progressPercentage);
    }

    public function testProgressPercentageAttributeProgressMax100()
    {
        $jobStatus = new JobStatus();
        $jobStatus->progress_max = 100;
        $jobStatus->progress_now = 49.6;

        $this->assertEquals(50, $jobStatus->progressPercentage);
    }

    public function testJobStatusIsEndedWhenFailed()
    {
        $jobStatus = new JobStatus();
        $jobStatus->status = 'failed';

        $this->assertTrue($jobStatus->isEnded);
    }

    public function testJobStatusIsEndedWhenFinished()
    {
        $jobStatus = new JobStatus();
        $jobStatus->status = 'finished';

        $this->assertTrue($jobStatus->isEnded);
    }

    public function testJobStatusIsFinished()
    {
        $jobStatus = new JobStatus();
        $jobStatus->status = 'finished';

        $this->assertTrue($jobStatus->isFinished);
    }

    public function testJobStatusIsFailed()
    {
        $jobStatus = new JobStatus();
        $jobStatus->status = 'failed';

        $this->assertTrue($jobStatus->isEnded);
    }

    public function testJobStatusIsExecuting()
    {
        $jobStatus = new JobStatus();
        $jobStatus->status = 'executing';

        $this->assertTrue($jobStatus->isExecuting);
    }
}
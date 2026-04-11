<?php

namespace Michal78\Tasks\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Michal78\Tasks\Models\Task;

class SampleEvent
{
    public function __construct(
        public Model $model,
        public array $payload,
        public Task $task,
    ) {
    }
}

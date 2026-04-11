<?php

namespace Michal78\Tasks\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Michal78\Tasks\Models\Task;

class SampleAction
{
    public static array $calls = [];

    public function __invoke(Model $model, array $payload, Task $task): void
    {
        self::$calls[] = [
            'model_id' => $model->getKey(),
            'payload' => $payload,
            'task_id' => $task->getKey(),
        ];
    }
}

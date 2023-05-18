<?php

/*
 * Config
 */
return [
    'country' => env('TASKS_COUNTRY', 'DK'),
    'task_language' => env('TASKS_LANGUAGE', 'DA'),
    'auto_schedule' => env('TASKS_AUTO_SCHEDULE', true),
    'auto_migrate' => env('TASKS_AUTO_MIGRATE', true),
];

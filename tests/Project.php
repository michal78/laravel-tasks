<?php

namespace Michal78\Tasks\Tests;

use Illuminate\Database\Eloquent\Model;
use Michal78\Tasks\Traits\HasTasks;

class Project extends Model
{
    use HasTasks;

    protected $table = 'projects';

    protected $guarded = [];
}

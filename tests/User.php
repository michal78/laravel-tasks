<?php

namespace Michal78\Tasks\Tests;

use Illuminate\Database\Eloquent\Model;
use Michal78\Tasks\Traits\HasTasks;

class User extends Model
{
    use HasTasks;

    protected $table = 'users';

    protected $guarded = [];
}

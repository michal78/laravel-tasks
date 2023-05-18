<?php

namespace Michal78\Tasks;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Michal78\Tasks\Skeleton\SkeletonClass
 */
class TasksFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tasks';
    }
}

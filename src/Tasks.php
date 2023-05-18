<?php

namespace Michal78\Tasks;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Tasks
{
    public function addHolidays() {

    }

    /**
     * @return Collection
     */
    public function getHolidays(): Collection
    {
        $holidays = Cache::get('laravel-tasks.holidays');

        if(! $holidays) {
            $holidays = file_get_contents('https://date.nager.at/api/v3/PublicHolidays/'.Carbon::now()->year.'/' . config('laravel-tasks.country'));
            Cache::put('laravel-tasks.holidays', $holidays);
        }

        return collect(json_decode($holidays));
    }
}

<?php

namespace Michal78\Tasks\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    use HasFactory;

    // Statusses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RUNNING = 'running';

    protected $fillable = [
        'name',
        'description',
        'status',
        'owner_id',
        'owner_class',
        'assignee_id',
        'assignee_class',
        'job',
        'job_data',
        'due_date',
        'priority',
        'completed_at',
        'completed_by',
    ];

    /**
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner', 'owner_class', 'owner_id');
    }

    /**
     * @return MorphTo
     */
    public function assignee(): MorphTo
    {
        return $this->morphTo('assignee', 'assignee_class', 'assignee_id');
    }

    /**
     * @param Model $model
     * @return Task
     */
    public function complete(Model $model): Task
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->completed_by = $model->id;
        $this->save();

        return $this;
    }


    /**
     * @param Model $model
     * @return $this
     */
    public function assign(Model $model): Task
    {
        $this->assignee_id = $model->id;
        $this->assignee_class = get_class($model);
        $this->save();

        return $this;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending($query): mixed
    {
        return $query->whereNull('completed_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCompleted($query): mixed
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOverdue($query): mixed
    {
        return $query->where('due_date', '<', now());
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueToday($query): mixed
    {
        return $query->where('due_date', now()->format('Y-m-d'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueTomorrow($query): mixed
    {
        return $query->where('due_date', now()->addDay()->format('Y-m-d'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueThisWeek($query): mixed
    {
        return $query->whereBetween('due_date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueNextWeek($query): mixed
    {
        return $query->whereBetween('due_date', [now()->addWeek()->startOfWeek()->format('Y-m-d'), now()->addWeek()->endOfWeek()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueThisMonth($query): mixed
    {
        return $query->whereBetween('due_date', [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueNextMonth($query): mixed
    {
        return $query->whereBetween('due_date', [now()->addMonth()->startOfMonth()->format('Y-m-d'), now()->addMonth()->endOfMonth()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueThisYear($query): mixed
    {
        return $query->whereBetween('due_date', [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueNextYear($query): mixed
    {
        return $query->whereBetween('due_date', [now()->addYear()->startOfYear()->format('Y-m-d'), now()->addYear()->endOfYear()->format('Y-m-d')]);
    }

    /**
     * @param $query
     * @param $from
     * @param $to
     * @return mixed
     */
    public function scopeDueBetween($query, $from, $to): mixed
    {
        return $query->whereBetween('due_date', [$from, $to]);
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeDueBefore($query, $date): mixed
    {
        return $query->where('due_date', '<', $date);
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeDueAfter($query, $date): mixed
    {
        return $query->where('due_date', '>', $date);
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeDueOn($query, $date): mixed
    {
        return $query->where('due_date', $date);
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeDueOnOrBefore($query, $date): mixed
    {
        return $query->where('due_date', '<=', $date);
    }

    /**
     * @param $query
     * @param $date
     * @return mixed
     */
    public function scopeDueOnOrAfter($query, $date): mixed
    {
        return $query->where('due_date', '>=', $date);
    }

    /**
     * @param $query
     * @param $from
     * @param $to
     * @return mixed
     */
    public function scopeDueOnOrBetween($query, $from, $to): mixed
    {
        return $query->whereBetween('due_date', [$from, $to]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueOnOrBeforeToday($query): mixed
    {
        return $query->where('due_date', '<=', now()->format('Y-m-d'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeDueOnOrAfterToday($query): mixed
    {
        return $query->where('due_date', '>=', now()->format('Y-m-d'));
    }

    /**
     * @param $date
     * @return void
     */
    public function schedule($date): void
    {
        $job = $this->getJob();

        $job->delay($date)->dispatch();
    }

    /**
     * @return mixed
     */
    private function getJob(): mixed
    {
        return new $this->job($this->job_data);
    }
}

<?php

namespace Michal78\Tasks\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    public const TYPE_COMMAND = 'command';
    public const TYPE_ACTION = 'action';
    public const TYPE_EVENT = 'event';
    public const TYPE_SERVICE = 'service';

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED = 'failed';

    protected $table = 'tasks';

    protected $fillable = [
        'name',
        'type',
        'target',
        'method',
        'payload',
        'run_at',
        'status',
        'error_message',
        'last_ran_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'run_at' => 'datetime',
        'last_ran_at' => 'datetime',
    ];

    public function taskable(): MorphTo
    {
        return $this->morphTo();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TaskLog::class);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PENDING)
            ->where('run_at', '<=', now());
    }
}

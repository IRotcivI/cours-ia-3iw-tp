<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'completed_at',
        'status',
        'project_id',
        'assigned_to',
        'parent_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Task $task) {
            if ($task->isDirty('status')) {
                $task->completed_at = $task->status === TaskStatus::Done ? now() : null;
            }
        });

        static::deleting(function (Task $task) {
            $task->subtasks()->delete();
        });
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'status' => TaskStatus::class,
            'priority' => TaskPriority::class,
        ];
    }
}

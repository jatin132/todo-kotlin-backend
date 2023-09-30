<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'is_completed',
        'user_id',
    ];

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function todos()
    {
        return $this->belongsToMany(User::class, 'todos');
    }

    public function addTasksToProjects()
    {
        return $this->belongsToMany(Project::class, 'project_tasks', 'task_id', 'project_id')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }  

    public function projectsTasks()
    {
        return $this->belongsToMany(Project::class, 'project_tasks', 'task_id', 'project_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'project_name',
        'project_description',
        'cover_photo',
        'user_id',
    ];

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(User::class, 'projects');
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addedMembersToProjects()
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'member_id');
    }

    public function addTasksToProjects()
    {
        return $this->belongsToMany(Project::class, 'project_tasks', 'task_id', 'project_id')
            ->withPivot('user_id')
            ->withTimestamps();
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(Project::class, 'project_members');
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany(Todo::class, 'project_tasks', 'project_id', 'task_id');
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_tasks', 'project_id', 'user_id');
    }

}

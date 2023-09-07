<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'username',
        'phone',
        'email',
        'password',
        'profile_bio',
        'profile_photo',
        'age',
        'dob',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
    public function todos()
    {
        return $this->belongsToMany(User::class, 'todos');
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function durationTasks()
    {
        return $this->belongsToMany(User::class, 'duration_tasks');
    }

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function addedMembers()
    {
        return $this->belongsToMany(User::class, 'added_members', 'user_id', 'member_user_id');
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
}

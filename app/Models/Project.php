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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DurationTask extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'time',
        'user_id',
    ];

    /**
     * The roles that belong to the DurationTask
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function durationTasks()
    {
        return $this->belongsToMany(User::class, 'duration_tasks');
    }
}

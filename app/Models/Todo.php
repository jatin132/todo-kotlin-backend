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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'is_completed',
        'start_date',
        'end_date',
        'priority',
        'user_id',

    ];
    public function user():BelongsTo{
        return $this ->belongsTo(User::class);
    }
}

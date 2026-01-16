<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}

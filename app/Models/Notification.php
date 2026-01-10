<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [ 'read_at' ];

    public function expense(): BelongsTo {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{


    /**
     * Atributos que podem ser altertados
     * @var array
     */
    protected $fillable = [
        'description',
        'price_total',
        'parcels',
        'payment_date',
        'intermediary',
        'payer_id',
        'intermediaries',
        'maturity',
        'receive_notification'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'payer_id');
    }

}

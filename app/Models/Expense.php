<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Expense extends Model
{

    use Notifiable;

    /**
     * Atributos que podem ser altertados
     * @var array
     */
    protected $fillable = [
        'name',
        'priceTotal',
        'parcels',
        'datePayment',
        'intermediary',
        'payee_id',
        'intermediarys',
        'maturity',
        'receiveNotification'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'payee_id');
    }

}

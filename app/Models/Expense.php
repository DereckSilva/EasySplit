<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function notification(): HasMany {
        return $this->hasMany(Notification::class, 'notification_id', 'id');
    }

}

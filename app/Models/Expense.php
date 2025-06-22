<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    /**
     * Atributos que podem ser altertados
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'parcels',
        'datePayment',
        'intermediary',
        'payee_id',
        'intermediarys_id'
    ];

}

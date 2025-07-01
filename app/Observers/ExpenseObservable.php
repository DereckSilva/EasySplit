<?php

namespace App\Observers;

use App\Models\Expense;
use App\Notifications\ExpenseNotification;

class ExpenseObservable
{
    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $user = $expense->user()->first();
        $expense->notify(new ExpenseNotification($user, $expense, 'Conta atualizada pelo dono:'));
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $user = $expense->user()->first();
        $expense->notify(new ExpenseNotification($user, $expense, 'Conta removida pelo dono:'));
    }

}

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
        $user = $expense->user;
        $user->notify(new ExpenseNotification($user, $expense, 'Conta atualizada pelo dono:'));
    }

    public function created(Expense $expense): void
    {
        $user = $expense->user;
        $user->notify(new ExpenseNotification($user, $expense, 'Conta criada pelo dono:'));
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $user = $expense->user;
        $user->notify(new ExpenseNotification($user, $expense, 'Conta removida pelo dono:'));
    }

}

<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificaVencimentoContas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verifica-vencimento-contas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica data de vencimento das contas e avisa os usuários reponsáveis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expenseRepository = app('App\Repository\ExpenseRepository');
        $expenses          = $expenseRepository->findAll();

        collect($expenses)->each(function ($expense) use ($expenseRepository) {
            $maturity    = Carbon::parse($expense['maturity']);
            $currentDate = Carbon::now();
            $diff        = (int)$currentDate->diffInDays($maturity->toString(), false);

            $exp  = $expenseRepository->find($expense['id']);
            $user = $exp->user()->first();

            // realiza o envio da notificacao para o dono da conta - (contas não pagas)
            if (!$exp->paid) {
                $this->sendNotification($user, $exp, $diff);
            }

            $userRepository = app('App\Repository\UserRepository');
            $intermediarys  = json_decode($exp->intermediarys, true);
            collect($intermediarys)->each(function ($intermediary) use ($userRepository, $exp, $diff){
                // percorre os intermediários da conta - (envia notificação para quem ainda não pagou)
                if (!$intermediary['paid']) {
                    $user = $userRepository->find($intermediary['email'], 'email');
                    $this->sendNotification($user, $exp, $diff);
                }
            });
        });
    }

    private function sendNotification(User $user, Expense $expense, int $diff) {
        if ($diff == 0) {
            $expense->notify(new ExpenseNotification($user, $expense, 'Chegou o dia do vencimento da conta do'));
        } elseif ($diff <= 5) {
            $expense->notify(new ExpenseNotification($user, $expense, 'Faltam 5 dias para o vencimento da conta do'));
        } elseif ($diff > 5 && $diff <= 10) {
            $expense->notify(new ExpenseNotification($user, $expense, 'Faltam 10 dias para o vencimento da conta do'));
        }
    }
}

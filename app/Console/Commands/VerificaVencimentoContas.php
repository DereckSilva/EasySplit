<?php

namespace App\Console\Commands;

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
            if ($diff == 0) {
                $exp->notify(new ExpenseNotification($user, $exp, 'Chegou o dia do vencimento da conta do'));
            } elseif ($diff <= 5) {
                $exp->notify(new ExpenseNotification($user, $exp, 'Faltam 5 dias para o vencimento da conta do'));
            } elseif ($diff > 5 && $diff <= 10) {
                $exp->notify(new ExpenseNotification($user, $exp, 'Faltam 10 dias para o vencimento da conta do'));
            }
        });
    }
}

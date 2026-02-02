<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseNotification;
use App\Service\ExpenseService;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerificaVencimentoContas extends Command
{

    protected Expense $expenseModel;
    protected User $userModel;

    public function __construct() {
        parent::__construct();
        $this->expenseModel   = new Expense();
        $this->userModel      = new User();
    }

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
        $expenses = $this->expenseModel::all();

        // FINALIZAR REFATORAÇÃO

        collect($expenses)->each(function ($expense) {
            $maturity    = Carbon::parse($expense['maturity']);
            $currentDate = Carbon::now();
            $diff        = (int)$currentDate->diffInDays($maturity->toString(), false);

            $exp  = $this->expenseModel::find($expense['id']);
            $user = $this->userModel::find($exp['payer_id']);

            // realiza o envio da notificacao para o dono da conta - (contas não pagas)
            if (!$exp->paid) {
                $this->sendNotification($user, $exp, $diff);
            }

            $intermediaries  = json_decode($exp->intermediaries, true);
            collect($intermediaries)->each(function ($intermediary) use ($exp, $diff){
                // percorre os intermediários da conta - (envia notificação para quem ainda não pagou)
                if (!$intermediary['paid']) {
                    $user = $this->userModel::where('email', '=', $intermediary['email']);
                    $this->sendNotification($user, $exp, $diff);
                }
            });
        });
    }

    private function sendNotification(User $user, Expense $expense, int $diff): void {
        $mensagens = [
            0  => 'Chegou o dia do vencimento da conta do',
            5  => 'Faltam 5 dias para o vencimento da conta do',
            10 => 'Faltam 10 dias para o vencimento da conta do'
        ];
        if (in_array($diff, array_keys($mensagens))) {
            $user->notify(new ExpenseNotification($user, $expense, $mensagens[$diff]));
        }
    }
}

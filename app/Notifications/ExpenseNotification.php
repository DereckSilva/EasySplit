<?php

namespace App\Notifications;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseNotification extends Notification
{
    use Queueable;

    protected $user;

    protected $expense;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Expense $expense)
    {
        $this->user    = $user;
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {

        // faz a regra de dias para o expense

        return [
            'user_id' => $this->user->id,
            'message' => 'Conta criada pelo usuário ' . $this->user->name
        ];
    }
}

<?php

namespace App\Jobs;

use App\Mail\BoasVindas;
use App\Mail\ResetaSenha;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EnviaEmail implements ShouldQueue
{
    use Queueable;

    protected $nome;

    protected $email;

    protected $boasVindas;

    /**
     * Create a new job instance.
     */
    public function __construct($nome, $email, $boasVindas = false)
    {
        $this->nome  = $nome;
        $this->email = $email;
        $this->boasVindas = $boasVindas;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->boasVindas) {
            Mail::to($this->email)->send(new BoasVindas($this->nome));
        } else {
            Mail::to($this->email)->send(new ResetaSenha($this->nome, $this->email));
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VericaTokensExpirados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verifica-tokens-expirados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica os tokens expirados e remove-os da base de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table("personal_access_tokens")->where("expires_at", "<=", now())->delete();
    }
}

<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (RouteNotFoundException $e) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'Verifique o token de envio',
                'data' => []
            ], Response::HTTP_FORBIDDEN));
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:verifica-vencimento-contas')->daily();
        $schedule->command('app:verifica-tokens-expirados')->weekdays()->at('00:00');
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if (! $user) {
                return '/';
            }

            return match ($user->role) {
                \App\Models\User::ROLE_ADMIN => route('admin.dashboard'),
                \App\Models\User::ROLE_DOKTER, 'dokter' => route('dokter.dashboard'),
                \App\Models\User::ROLE_KADER => route('kader.dashboard'),
                default => route('pemilik.dashboard'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

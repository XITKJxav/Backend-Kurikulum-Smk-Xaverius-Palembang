<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                Log::info('AccessDeniedHttpException triggered');
                return response()->json([
                    'status' => false,
                    'status_code' => 403,
                    'message' => 'Akses tidak diperbolehkan'
                ], 403);
            }
        });

        // Jika ingin menangani pengecualian yang lebih umum
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                Log::info('HTTP Exception triggered: ' . get_class($e));
                return response()->json([
                    'status' => false,
                    'status_code' => $e->getStatusCode(),
                    'message' => $e->getMessage()
                ], $e->getStatusCode());
            }
        });

        // Handle Not Found (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'status_code' => 404,
                    'message' => 'Record not found.'
                ], 404);
            }
        });

        // Handle Authentication Error (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'status_code' => 401,
                    'message' => 'User is not authenticated'
                ], 401);
            }
        });

        // Generic Exception Handler for any other errors
        $exceptions->render(function (\Exception $e, Request $request) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        });
    })->create();

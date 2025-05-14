<?php

namespace App\Http\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface AuthInterface
{
    public function handleLogin(Request $request, string $model): JsonResponse;
    public function handleLogout(Request $request, string $model): JsonResponse;
    public function handleRequestCode(Request $request, string $model): JsonResponse;
    public function handleResetWithCode(Request $request, string $model): JsonResponse;
    public function handleRefreshToken(Request $request, string $model): JsonResponse;
}

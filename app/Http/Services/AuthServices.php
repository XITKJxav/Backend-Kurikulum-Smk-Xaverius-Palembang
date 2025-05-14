<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Teacher;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\ApiResponse;
use App\Http\Interfaces\AuthInterface;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Validation\ValidationException;

class AuthServices implements AuthInterface
{
    protected function resolveModel(string $guard): string
    {
        return match ($guard) {
            'user' => User::class,
            'teacher' => Teacher::class,
            default => abort(404, 'Unsupported model'),
        };
    }

    public function handleLogin(Request $request, string $guard): JsonResponse
    {
        try {
            $modelClass = $this->resolveModel($guard);

            Log::debug('Resolved model class:', [$modelClass]);

            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = $modelClass::where('email', $request->email)->first();

            if (!$user) {
                return (new ApiResponse(404, [], 'User not found'))->send();
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return (new ApiResponse(401, [], 'Unauthorized. Check your email or password.'))->send();
            }

            $at_expiration = 60;
            $rt_expiration = 30 * 24 * 60;

            $access_token = $user->createToken(
                'access_token',
                ['access-api'],
                Carbon::now()->addMinutes($at_expiration)
            )->plainTextToken;

            $refresh_token = $user->createToken(
                'refresh_token',
                ['issue-access-token'],
                Carbon::now()->addMinutes($rt_expiration)
            )->plainTextToken;

            $data = [];

            if ($guard === 'teacher') {
                $data = [
                    'kd_guru' => $user->kd_guru,
                    'name' => $user->name,
                    'email' => $user->email,
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                ];
            } elseif ($guard === 'user') {
                $data = [
                    'kd_pengurus_kelas' => $user->kd_kepengurusan_kelas,
                    'name' => $user->name,
                    'id_ruang_kelas' => $user->id_ruang_kelas,
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                ];
            }

            return (new ApiResponse(200, [$data], 'Login successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to Login'))->send();
        }
    }


    public function handleLogout(Request $request, string $guard): JsonResponse
    {
        try {

            $user = $request->user($guard);

            if (!$user) {
                return (new ApiResponse(401, [], 'User is not authenticated'))->send();
            }

            $token = $request->bearerToken();

            if (!$token) {
                return (new ApiResponse(400, [], 'No token provided'))->send();
            }

            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken || !$accessToken->can('access-api')) {
                return (new ApiResponse(403, [], 'Invalid access token for logout'))->send();
            }

            $accessToken->delete();

            return (new ApiResponse(200, [], 'Logout successful'))->send();
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to logout' . $e->getMessage()))->send();
        }
    }


    public function handleRequestCode(Request $request, string $guard): JsonResponse
    {
        try {
            $modelClass = $this->resolveModel($guard);

            $request->validate([
                'email' => 'required|email|exists:' . $modelClass . ',email',
            ]);

            $user = $modelClass::where('email', $request->email)->first();
            $code = rand(100000, 999999);

            $user->update([
                'otp' => $code,
                'otp_expires_at' => Carbon::now()->addMinutes(15),
            ]);

            $user->notify(new ResetPasswordNotification($code));

            return (new ApiResponse(200, [], 'Kode reset telah dikirim ke email.'))->send();
        } catch (\Exception $e) {
            Log::error('Request code error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to send code otp'))->send();
        }
    }

    public function handleResetWithCode(Request $request, string $guard): JsonResponse
    {
        try {
            $modelClass = $this->resolveModel($guard);

            $request->validate([
                'email' => 'required|email|exists:' . $modelClass . ',email',
                'code' => 'required|digits:6',
                'password' => 'required|confirmed|min:8',
            ]);

            $user = $modelClass::where('email', $request->email)
                ->where('otp', $request->code)
                ->where('otp_expires_at', '>=', now())
                ->first();

            if (!$user) {
                return (new ApiResponse(400, [], 'Kode tidak valid atau sudah kadaluarsa.'))->send();
            }

            $user->update([
                'password' => bcrypt($request->password),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            return (new ApiResponse(200, [], 'Password berhasil direset.'))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, $e->errors(), 'Validasi gagal.'))->send();
        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Terjadi kesalahan pada server.'))->send();
        }
    }

    public function handleRefreshToken(Request $request, string $guard): JsonResponse
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return (new ApiResponse(401, [], 'No token provided'))->send();
            }

            $refreshToken = PersonalAccessToken::findToken($token);

            if (!$refreshToken || !$refreshToken->can('issue-access-token')) {
                return (new ApiResponse(403, [], 'Invalid refresh token'))->send();
            }

            $user = $refreshToken->tokenable;
            $user->tokens()->where('name', 'access_token')->delete();

            $newAccessToken = $user->createToken('access_token', ['access-api'])->plainTextToken;

            return (new ApiResponse(200, ['access_token' => $newAccessToken], 'Access token refreshed successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to refresh token'))->send();
        }
    }
}

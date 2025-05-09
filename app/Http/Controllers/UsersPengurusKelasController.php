<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class UsersPengurusKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $response = new ApiResponse();

        try {

            $query = $request->search;
            $order = $request->orderBy === 'true' ? 'desc' : 'asc';

            $jurusanQuery = User::with("ruanganKelas");

            if ($query) {
                $jurusanQuery->where('nama_jurusan', 'like', '%' . $query . '%');
            }

            $data = $jurusanQuery->orderBy('name', $order)->paginate(4);

            return response()->json($response->callResponse(200, $data, 'User class coordinator fetched successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jurusan: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to fetch user class coordinator'), 500);
        }
    }

    public function show(string $id)
    {
        $response = new ApiResponse();

        try {
            $ruangKelas = User::with('ruanganKelas')->findOrFail($id);

            return response()->json($response->callResponse(200, [$ruangKelas], 'User class coordinator fetched successfully'), 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found: ' . $e->getMessage());
            return response()->json($response->callResponse(404, [], 'User class coordinator fetched successfully'), 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving jurusan: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to retrieve user class coordinator'), 500);
        }
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'id_ruang_kelas' => 'required',
                'no_telp' => 'required|string|min:10|max:15',
            ]);

            $kdKepengurusan = $this->generateKepengurusanCode();

            $user = User::create([
                'kd_kepengurusan_kelas' => $kdKepengurusan,
                'name' => $request->name,
                'email' => $request->email,
                'id_ruang_kelas' => $request->id_ruang_kelas,
                'status' => true,
                'password' => bcrypt($request->password),
                'no_telp' => $request->no_telp,
            ]);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'User registered successfully',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'no_telp' => $user->no_telp,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'status_code' => 422,
                'message' =>  $e->getMessage(),
                'data' => []
            ], 422);
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Failed to register user',
                'data' => []
            ], 500);
        }
    }

    private function generateKepengurusanCode(): string
    {
        $latest = User::orderBy('kd_kepengurusan_kelas', 'desc')->first();
        $lastCode = $latest ? intval(substr($latest->kd_kepengurusan_kelas, -3)) : 0;
        $newCode = str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);
        return 'KK-' . $newCode;
    }

    public function login(Request $request): JsonResponse
    {
        $response = new ApiResponse();

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json($response->callResponse(401, [], 'Unauthorized. Check your email or password.'), 401);
            }

            $user = Auth::user();

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

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Login successfully',
                'data' => [
                    'kd_pengurus_kelas' => $user->kd_kepengurusan_kelas,
                    'name' => $user->name,
                    'id_ruang_kelas' => $user->id_ruang_kelas,
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Failed to Login',
                'data' => []
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $request->bearerToken();
            $accessToken = PersonalAccessToken::findToken($token);


            if (!$user || $user == null) {
                return response()->json([
                    'status' => false,
                    'status_code' => 401,
                    'message' => 'User is not authenticated',
                ], 401);
            }

            if (!$accessToken || !$accessToken->can('access-api')) {
                return response()->json([
                    'status' => false,
                    'status_code' => 403,
                    'message' => 'Invalid access token for logout',
                ], 403);
            }

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Failed to logout',
            ], 500);
        }
    }

    public function requestCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $user = User::where('email', $request->email)->first();
            $code = rand(100000, 999999);

            $user->update([
                'otp' => $code,
                'otp_expires_at' => Carbon::now()->addMinutes(15),
            ]);

            $user->notify(new ResetPasswordNotification($code));

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Kode reset telah dikirim ke email.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Failed to send code otp',
            ], 500);
        }
    }

    public function resetWithCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'code' => 'required|digits:6',
                'password' => 'required|confirmed|min:8',
            ]);

            $user = User::where('email', $request->email)
                ->where('otp', $request->code)
                ->where('otp_expires_at', '>=', now())
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'Kode tidak valid atau sudah kadaluarsa.'
                ], 400);
            }

            $user->update([
                'password' => bcrypt($request->password),
                'reset_code' => null,
                'reset_code_expires_at' => null,
            ]);

            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Password berhasil direset.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'status_code' => 422,
                'message' => 'Validasi gagal.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Terjadi kesalahan pada server.',
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['status' => false, 'message' => 'No token provided'], 401);
        }

        $refreshToken = PersonalAccessToken::findToken($token);

        if (! $refreshToken || ! $refreshToken->can('issue-access-token')) {
            return response()->json(['status' => false, 'message' => 'Invalid refresh token'], 403);
        }

        $user = $refreshToken->tokenable;

        $user->tokens()->where('name', 'access_token')->delete();

        $newAccessToken = $user->createToken('access_token', ['access-api'])->plainTextToken;

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'access_token' => $newAccessToken,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {

            $validator = Validator::make($request->all(), [
                'name'             => 'sometimes|string|max:255',
                'email'            => 'sometimes|email|unique:users,email,' . $id . ',kd_kepengurusan_kelas',
                'no_telp'          => 'sometimes|string|min:10|max:15',
                'id_ruang_kelas'   => 'sometimes|exists:ruangankelas,id',
                'status'            => 'sometimes|boolean',
                'password'         => 'sometimes|nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'      => false,
                    'status_code' => 400,
                    'message'     => 'Error 400 Bad Request',
                    'errors'      => $validator->errors(),
                ], 400);
            }

            $user = User::findOrFail($id);

            $updateData = $request->only(['name', 'email', 'no_telp', 'id_ruang_kelas', 'status']);

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $updated = $user->update($updateData);

            if (! $updated) {
                return response()->json([
                    'status'      => false,
                    'status_code' => 400,
                    'message'     => 'Gagal memperbarui user',
                ], 400);
            }

            return response()->json([
                'status'      => true,
                'status_code' => 200,
                'message'     => 'User updated successfully',
                'data'        => [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'no_telp' => $user->no_telp,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status'      => false,
                'status_code' => 422,
                'message'     => $e->getMessage(),
                'errors'      => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status'      => false,
                'status_code' => 500,
                'message'     => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}

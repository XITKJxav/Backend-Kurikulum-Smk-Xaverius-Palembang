<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthServices;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class UsersPengurusKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {

            $query = $request->search;
            $order = $request->orderBy === 'true' ? 'desc' : 'asc';

            $jurusanQuery = User::with("ruanganKelas");

            if ($query) {
                $jurusanQuery->where('nama_jurusan', 'like', '%' . $query . '%');
            }

            $data = $jurusanQuery->orderBy('name', $order)->paginate(4);
            return (new ApiResponse(200, [$data], 'User class coordinator fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching jurusan: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to fetch user class coordinator'))->send();
        }
    }

    public function show(string $id)
    {

        try {
            $ruangKelas = User::with('ruanganKelas')->findOrFail($id);
            return (new ApiResponse(200, [$ruangKelas], 'User class coordinator fetched successfully'))->send();
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found: ' . $e->getMessage());
            return (new ApiResponse(404, [], 'User class coordinator not found'))->send();
        } catch (\Exception $e) {
            Log::error('Error retrieving jurusan: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to retrieve user class coordinator'))->send();
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


            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'no_telp' => $user->no_telp,
            ];
            return (new ApiResponse(200, [$data], 'User registered successfully'))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, [], $e->getMessage()))->send();
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());

            return (new ApiResponse(500, [], 'Failed to register user'))->send();
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'no_telp' => 'sometimes|string|min:10|max:15',
                'id_ruang_kelas' => 'sometimes|exists:ruangankelas,id',
                'status' => 'sometimes|boolean',
                'password' => 'sometimes|nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return (new ApiResponse(400, [], 'Error 400 Bad Request'))->send();
            }

            $user = User::findOrFail($id);

            $updateData = $request->only(['name', 'email', 'no_telp', 'id_ruang_kelas', 'status']);

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $updated = $user->update($updateData);

            if (!$updated) {
                return (new ApiResponse(400, [], 'Gagal memperbarui user'))->send();
            }

            return (new ApiResponse(200, [
                'name' => $user->name,
                'email' => $user->email,
                'no_telp' => $user->no_telp,
            ], 'User updated successfully'))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, $e->errors(), 'Validasi gagal.'))->send();
        } catch (\Throwable $e) {
            Log::error('Update error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Error: ' . $e->getMessage()))->send();
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
        return (new AuthServices())->handleLogin($request, "user");
    }

    public function logout(Request $request): JsonResponse
    {
        return (new AuthServices())->handleLogout($request, "user");
    }

    public function requestCode(Request $request)
    {
        return (new AuthServices())->handleRequestCode($request, "user");
    }
    public function resetWithCode(Request $request)
    {
        return (new AuthServices())->handleResetWithCode($request, "user");
    }

    public function refreshToken(Request $request)
    {
        return (new AuthServices())->handleRefreshToken($request, "user");
    }
}

<?php

namespace App\Http\Controllers\Murid;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Controllers\Controller;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Services\AuthServices;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class MuridController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(User::with('ruanganKelas'), 'name', 'kd_siswa')
                ->apply();

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

            $kdKepengurusan = new ReportGenerator();

            $data = User::create([
                'kd_siswa' => $kdKepengurusan->generator("kdSiswa", []),
                'name' => $request->name,
                'email' => $request->email,
                'id_ruang_kelas' => $request->id_ruang_kelas,
                'status' => true,
                'password' => bcrypt($request->password),
                'no_telp' => $request->no_telp,
            ]);

            return (new ApiResponse(200, [$data], 'User registered successfully'))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, [], $e->getMessage()))->send();
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return (new ApiResponse(409, [], 'Kode siswa sudah digunakan'))->send(); // 409 Conflict
            }
            return (new ApiResponse(500, [], 'Failed to register user' . $e->getMessage()))->send();
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

<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Controllers\Controller;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Services\AuthServices;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        try {

            $data = (new Filtering($request))
                ->setBuilder(Karyawan::query(), 'nama', 'kd_karyawan')
                ->apply();

            return (new ApiResponse(200, [$data], 'User Karyawan fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching Karyawan: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to fetch karyawan'))->send();
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
            $generator = new ReportGenerator();
            $kdKepengurusan = $generator->generator("kdSiswa");

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'id_ruang_kelas' => 'required',
                'no_telp' => 'required|string|min:10|max:15',
            ]);

            $data = Karyawan::create([
                'kd_karyawan' => $kdKepengurusan,
                'name' => $request->name,
                'email' => $request->email,
                'status' => true,
                'password' => bcrypt($request->password),
                'no_telp' => $request->no_telp,
                'id_role' => $request->id_role
            ]);

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
                'status' => 'sometimes|boolean',
                'password' => 'sometimes|nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return (new ApiResponse(400, [], 'Error 400 Bad Request'))->send();
            }

            $user = Karyawan::findOrFail($id);

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
        return (new AuthServices())->handleLogin($request, "karyawan");
    }

    public function logout(Request $request): JsonResponse
    {
        return (new AuthServices())->handleLogout($request, "karyawan");
    }

    public function requestCode(Request $request)
    {
        return (new AuthServices())->handleRequestCode($request, "karyawan");
    }

    public function resetWithCode(Request $request)
    {
        return (new AuthServices())->handleResetWithCode($request, "karyawan");
    }

    public function refreshToken(Request $request)
    {
        return (new AuthServices())->handleRefreshToken($request, "karyawan");
    }
}

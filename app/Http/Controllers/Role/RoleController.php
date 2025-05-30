<?php

namespace App\Http\Controllers\Role;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function getRole()
    {
        try {
            $data = Role::all();
            return (new ApiResponse(200, [$data], 'Successfully fetched roles'))->send();
        } catch (Exception $e) {
            return (new ApiResponse(500, [], 'Failed to fetch roles: ' . $e->getMessage()))->send();
        }
    }
}

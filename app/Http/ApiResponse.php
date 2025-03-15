<?php
namespace App\Http;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ApiResponse {
    public function callResponse($status_code, $data = [], $name = ""): array {
        switch ($status_code) {
            case 200:
                return $this->response200($data, $name);
            case 201:
                return $this->response201($data, $name);
            case 204:
                return $this->response204();
            case 400:
                return $this->response400($name);
            case 401:
                return $this->response401();
            case 404:
                return $this->response404($name);
            case 500:
                return $this->response500();
            default:
                return [
                    "status_code" => $status_code,
                    "message" => "Unknown response status",
                    "data" => []
                ];
        }
    }

    public function response200($data, $name): array {
        return[
            "status_code" => 200,
            "message" => "Success getting " . $name,
            "data" => [$data]
        ];
    }

    public function response201($data, $name): array {
        return [
            "status_code" => 201,
            "message" => "Success created " . $name,
            "data" => [$data]
        ];
    }

    public function response204(): array {
        return [
            "status_code" => 204,
            "message" => "No Content",
            "data" => []
        ];
    }

    public function response400($name): array {
        return [
            "status_code" => 400,
            "message" => "Bad Request: " . $name . " not found",
            "data" => []
        ];
    }

    public function response401(): array {
        return [
            "status_code" => 401,
            "message" => "Unauthorized",
            "data" => []
        ];
    }

    public function response404($name): array {
        return [
            "status_code" => 404,
            "message" => "Not Found: " . $name,
            "data" => []
        ];
    }

    public function response500(): array {
        return [
            "status_code" => 500,
            "message" => "Internal Server Error",
            "data" => []
        ];
    }
}

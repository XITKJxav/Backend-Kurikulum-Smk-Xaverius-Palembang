<?php

namespace App\Http;

class ApiResponse
{

    public function callResponse($status_code, $data = [], $message = ""): array
    {
        switch ($status_code) {
            case 200:
                return $this->response200($data, $message);
            case 201:
                return $this->response201($data, $message);
            case 204:
                return $this->response204();
            case 400:
                return $this->response400($message);
            case 401:
                return $this->response401();
            case 404:
                return $this->response404($message);
            case 422:
                return $this->response422($data, $message);
            case 500:
                return $this->response500();
            default:
                return [
                    "status" => false,
                    "status_code" => $status_code,
                    "message" => "Unknown response status",
                    "data" => []
                ];
        }
    }



    public function response200($data, $message): array
    {
        return [
            "status" => true,
            "status_code" => 200,
            "message" => $message,
            "data" =>  $data
        ];
    }

    public function response201($data, $message): array
    {
        return [
            "status" => true,
            "status_code" => 201,
            "message" => $message,
            "data" => $data
        ];
    }

    public function response204(): array
    {
        return [
            "status" => true,
            "status_code" => 204,
            "message" => "No Content",
            "data" => []
        ];
    }

    public function response400($name): array
    {
        return [
            "status" => false,
            "status_code" => 400,
            "message" => "Bad Request: " . $name,
            "data" => []
        ];
    }

    public function response401(): array
    {
        return [
            "status" => false,
            "status_code" => 401,
            "message" => "Unauthorized",
            "data" => []
        ];
    }

    public function response404($name): array
    {
        return [
            "status" => false,
            "status_code" => 404,
            "message" => "Not Found: " . $name,
            "data" => []
        ];
    }
    public function response422($data, $message): array
    {
        return [
            "status" => false,
            "status_code" => 422,
            "message" => $message,
            "data" => $data
        ];
    }


    public function response500(): array
    {
        return [
            "status" => false,
            "status_code" => 500,
            "message" => "Internal Server Error",
            "data" => []
        ];
    }
}

<?php

namespace App\Http;

class ApiResponse
{

    private int $status_code;
    private string $message;
    private array $data;

    public function __construct(int $status_code = 200, array $data = [], string $message = '')
    {
        $this->status_code = $status_code;
        $this->data = $data;
        $this->message = $message;
    }

    public function send()
    {
        $response = [
            'status' => $this->status_code >= 200 && $this->status_code < 300,
            'status_code' => $this->status_code,
            'message' => $this->message ?: $this->defaultMessage($this->status_code),
            'data' => $this->data
        ];

        return response()->json($response, $this->status_code);
    }

    private function defaultMessage($code): string
    {
        return match ($code) {
            200 => 'Success',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Invalid access token for',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            default => 'Unknown response'
        };
    }

    // public function callResponse($status_code, $data = [], $message = ""): array
    // {
    //     switch ($status_code) {
    //         case 200:
    //             return $this->response200($data, $message);
    //         case 201:
    //             return $this->response201($data, $message);
    //         case 204:
    //             return $this->response204();
    //         case 400:
    //             return $this->response400($message);
    //         case 401:
    //             return $this->response401();
    //         case 404:
    //             return $this->response404($message);
    //         case 422:
    //             return $this->response422($data, $message);
    //         case 500:
    //             return $this->response500();
    //         default:
    //             return [
    //                 "status" => false,
    //                 "status_code" => $status_code,
    //                 "message" => "Unknown response status",
    //                 "data" => []
    //             ];
    //     }
    // }



    // public function response200($data, $message): array
    // {
    //     return [
    //         "status" => true,
    //         "status_code" => 200,
    //         "message" => $message,
    //         "data" =>  $data
    //     ];
    // }

    // public function response201($data, $message): array
    // {
    //     return [
    //         "status" => true,
    //         "status_code" => 201,
    //         "message" => $message,
    //         "data" => $data
    //     ];
    // }

    // public function response204(): array
    // {
    //     return [
    //         "status" => true,
    //         "status_code" => 204,
    //         "message" => "No Content",
    //         "data" => []
    //     ];
    // }

    // public function response400($name): array
    // {
    //     return [
    //         "status" => false,
    //         "status_code" => 400,
    //         "message" => "Bad Request: " . $name,
    //         "data" => []
    //     ];
    // }

    // public function response401(): array
    // {
    //     return [
    //         "status" => false,
    //         "status_code" => 401,
    //         "message" => "Unauthorized",
    //         "data" => []
    //     ];
    // }

    // public function response404($name): array
    // {
    //     return [
    //         "status" => false,
    //         "status_code" => 404,
    //         "message" => "Not Found: " . $name,
    //         "data" => []
    //     ];
    // }
    // public function response422($data, $message): array
    // {
    //     return [
    //         "status" => false,
    //         "status_code" => 422,
    //         "message" => $message,
    //         "data" => $data
    //     ];
    // }


    // public function response500(): array
    // {
    //     return [
    //         "status" => false,
    //         "status_code" => 500,
    //         "message" => "Internal Server Error",
    //         "data" => []
    //     ];
    // }
}

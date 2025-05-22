<?php

namespace App\Http\Common\Utils;

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
    public static function callResponse(int $statusCode, $data, string $message): array
    {
        return [
            'status' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
    }
}

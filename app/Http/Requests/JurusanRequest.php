<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kd_jurusan' => 'string|max:10',
            'nama_jurusan' => 'required|string|max:50',
        ];
    }
}

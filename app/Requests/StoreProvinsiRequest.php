<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProvinsiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => ['required', 'regex:/^\d{2}$/', 'unique:provinsis,kode'],
            'nama' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode provinsi wajib diisi.',
            'kode.regex'    => 'Kode harus tepat 2 digit angka (00–99).',
            'kode.unique'   => 'Kode :input sudah digunakan oleh provinsi lain.',
            'nama.required' => 'Nama provinsi wajib diisi.',
            'nama.max'      => 'Nama provinsi maksimal 100 karakter.',
        ];
    }

    /** Kembalikan JSON saat validasi gagal (AJAX request) */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}

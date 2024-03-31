<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PegawaiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable',
            'nama' => 'required|string',
            'hp' => ['required', 'string', 'regex:/^08[0-9]{9,12}$/'],
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'pengguna',
            'nama' => 'nama lengkap',
            'hp' => 'nomor hp',
        ];
    }

    // set nilai user_id
    public function withValidator($validator)
    {
        $data['user_id'] = 1; // Ganti dengan Auth::id() jika menggunakan autentikasi Laravel
        $this->merge($data);
    }
}

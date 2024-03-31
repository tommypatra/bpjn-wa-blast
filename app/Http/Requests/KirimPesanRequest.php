<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KirimPesanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable',
            'wa_pesan_id' => 'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'pengguna',
            'wa_pesan_id' => 'format wa',
        ];
    }

    // set nilai user_id
    public function withValidator($validator)
    {
        $data['user_id'] = 1; // Ganti dengan Auth::id() jika menggunakan autentikasi Laravel
        $this->merge($data);
    }
}

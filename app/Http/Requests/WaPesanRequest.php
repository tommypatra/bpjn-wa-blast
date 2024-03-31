<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaPesanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable',
            'judul' => 'required|string',
            'pesan' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'user_id' => 'pengguna',
            'judul' => 'judul umum',
            'pesan' => 'isi pesan',
        ];
    }

    // set nilai user_id
    public function withValidator($validator)
    {
        $data['user_id'] = 1; // Ganti dengan Auth::id() jika menggunakan autentikasi Laravel
        $this->merge($data);
    }
}

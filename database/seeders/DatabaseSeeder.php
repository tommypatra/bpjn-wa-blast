<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\WaPesan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //untuk admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@app.com', //email login
            'password' => Hash::make('00000000'), // password default login 
        ]);

        //untuk pengguna
        for ($i = 1; $i <= 9; $i++) {
            User::create([
                'name' => 'Pengguna ' . $i,
                'email' => 'pengguna' . $i . '@app.com', //email login
                'password' => Hash::make('00000000'), // password default login 
            ]);
        }


        //untuk wapesan
        $dtdef = [
            ['user_id' => 1, 'judul' => 'upacara bendera 2024', 'pesan' => 'disampaikan kepada seluruh pegawai untuk wajib mengikuti upcara bendera tanggal 17 agustus 2024'],
            ['user_id' => 1, 'judul' => 'rapat bersama', 'pesan' => 'Mohon waktu luangnya untuk hadir rapat'],
            ['user_id' => 2, 'judul' => 'buka puasa bersama', 'pesan' => 'disampaikan bahwa pada tanggal 02 april 2024 untuk hadir pada acara buka puasa bersama'],
            ['user_id' => 3, 'judul' => 'kerjabakti akbar', 'pesan' => 'mohon kepada seluruh pegawai untuk mengikuti kerja bakti akbar'],
            ['user_id' => 3, 'judul' => 'apel senin', 'pesan' => 'disampaikan kepada seluruh pegawai bahwa tetap wajib mengikuti apel pagi setiap senin'],
            ['user_id' => 4, 'judul' => 'update skp', 'pesan' => 'penutupan pengisian skp pada aplikasi BKN segera ditutup, mohon pegawai wajib mengisi secepatnya'],
        ];

        foreach ($dtdef as $dt) {
            WaPesan::create([
                'user_id' => $dt['user_id'],
                'judul' => $dt['judul'],
                'pesan' => $dt['pesan'],
            ]);
        }


        //untuk pengguna
        // for ($i = 1; $i <= 9; $i++) {
        //     Pegawai::create([
        //         'user_id' => 1,
        //         'nama' => 'Pegawai ' . $i,
        //         'hp' => '085' . rand(2, 5) . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT),
        //     ]);
        // }

        //untuk wapesan
        $dtdef = [];

        foreach ($dtdef as $dt) {
            Pegawai::create([
                'user_id' => $dt['user_id'],
                'nama' => $dt['nama'],
                'hp' => $dt['hp'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\UserManual;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserManualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserManual::create([
            'title' => 'Panduan Penggunaan Aplikasi',
            'img' => 'path/to/image1.jpg',
            'short_desc' => 'Panduan singkat penggunaan aplikasi.',
            'initial_editor' => 'Admin',
            'latest_editor' => 'Admin2',
            'version' => '1.0',
            'content' => '<h1>Panduan Aplikasi</h1><p>Ini adalah panduan lengkap untuk menggunakan aplikasi.</p><img src="path/to/image1.jpg" alt="Contoh Gambar">',
            'category' => 'internal',
            'size' => '1.2MB',
            'user_id' => 1, // ID pengguna yang membuat manual
        ]);

        UserManual::create([
            'title' => 'Panduan Instalasi Aplikasi',
            'img' => 'path/to/image2.jpg',
            'short_desc' => 'Langkah-langkah instalasi aplikasi.',
            'initial_editor' => 'Admin',
            'latest_editor' => 'Admin3',
            'version' => '1.0',
            'content' => '<h1>Panduan Instalasi</h1><p>Ini adalah panduan lengkap untuk instalasi aplikasi.</p><img src="path/to/image2.jpg" alt="Contoh Gambar">',
            'category' => 'eksternal',
            'size' => '1.5MB',
            'user_id' => 1, // ID pengguna yang membuat manual
        ]);
    
        UserManual::create([
            'title' => 'Panduan Konfigurasi Aplikasi',
            'img' => 'path/to/image3.jpg',
            'short_desc' => 'Panduan untuk konfigurasi aplikasi.',
            'initial_editor' => 'Admin',
            'latest_editor' => 'Admin4',
            'version' => '1.0',
            'content' => '<h1>Panduan Konfigurasi</h1><p>Ini adalah panduan lengkap untuk konfigurasi aplikasi.</p><img src="path/to/image3.jpg" alt="Contoh Gambar">',
            'category' => 'internal',
            'size' => '1.3MB',
            'user_id' => 1, // ID pengguna yang membuat manual
        ]);

        
    }
}

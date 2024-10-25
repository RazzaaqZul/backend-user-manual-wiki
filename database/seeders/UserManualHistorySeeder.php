<?php

namespace Database\Seeders;

use App\Models\UserManualHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserManualHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserManualHistory::create([
            'title' => 'Perubahan pada Panduan Penggunaan Aplikasi',
            'img' => 'path/to/image1_update.jpg',
            'short_desc' => 'Memperbarui panduan penggunaan aplikasi.',
            'initial_editor' => 'Admin',
            'latest_editor' => 'Admin2',
            'version' => '1.1',
            'content' => '<h1>Pembaruan Panduan Aplikasi</h1><p>Ini adalah pembaruan untuk panduan penggunaan aplikasi.</p>',
            'category' => 'internal',
            'size' => '1.3MB',
            'user_manual_id' => 1, // Mengacu pada user_manual_id yang ada
        ]);

        UserManualHistory::create([
            'title' => 'Perubahan pada Panduan Instalasi',
            'img' => 'path/to/image2_update.jpg',
            'short_desc' => 'Memperbarui langkah-langkah instalasi.',
            'initial_editor' => 'Admin',
            'latest_editor' => 'rafli',
            'version' => "1.0.0",
            'content' => '<h1>Pembaruan Instalasi</h1><p>Ini adalah pembaruan untuk panduan instalasi aplikasi.</p>',
            'category' => 'eksternal',
            'size' => '1.6MB',
            'edited_by' => 'Admin',
            'user_manual_id' => 1, // Mengacu pada user_manual_id yang ada
        ]);
    }
}

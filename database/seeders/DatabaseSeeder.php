<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        User::factory()->create([
            'user_id' => '1',
            'username' => 'Test User',
            'password' => bcrypt('test@example.com'), 
            'email' => 'test@example.com', 
            'name' => 'Test User Name', 
        ]);
        // User::factory(10)->create();
    }
}

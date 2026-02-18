<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
              'password' => 'password123',// hashed automatically
               'name' => 'Test User',
            'email' => 'agent@supportflow.com',
              'password' => 'password123',
        ]);
    }
    
}

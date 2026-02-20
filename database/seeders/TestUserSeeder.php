<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users with different roles
        User::create([
            'name' => 'Demo Agent',
            'email' => 'agent@supportflow.com',
            'password' => Hash::make('password123'),
            'role' => 'agent',
            'department' => 'Customer Support'
        ]);

        User::create([
            'name' => 'Demo Supervisor',
            'email' => 'supervisor@supportflow.com',
            'password' => Hash::make('supervisor123'),
            'role' => 'supervisor',
            'department' => 'Support Team Lead'
        ]);

        User::create([
            'name' => 'Demo Admin',
            'email' => 'admin@supportflow.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'department' => 'System Administration'
        ]);
    }
    
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run()
    {
        // Create demo agent user
        User::updateOrCreate(
            ['email' => 'agent@supportflow.com'],
            [
                'name' => 'Demo Agent',
                'email' => 'agent@supportflow.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'role' => 'agent',
                'department' => 'customer_support',
                'is_active' => true,
            ]
        );

        // Create demo admin user
        User::updateOrCreate(
            ['email' => 'admin@supportflow.com'],
            [
                'name' => 'Demo Admin',
                'email' => 'admin@supportflow.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'role' => 'admin',
                'department' => 'management',
                'is_active' => true,
            ]
        );

        // Create demo supervisor user
        User::updateOrCreate(
            ['email' => 'supervisor@supportflow.com'],
            [
                'name' => 'Demo Supervisor',
                'email' => 'supervisor@supportflow.com',
                'password' => Hash::make('supervisor123'),
                'email_verified_at' => now(),
                'role' => 'supervisor',
                'department' => 'supervision',
                'is_active' => true,
            ]
        );

        // Create demo tickets for the agent
        $agent = User::where('email', 'agent@supportflow.com')->first();
        
        if ($agent) {
            // Create sample tickets
            \App\Models\Ticket::factory()->count(5)->create([
                'assigned_to' => $agent->id,
            ]);
        }
    }
}
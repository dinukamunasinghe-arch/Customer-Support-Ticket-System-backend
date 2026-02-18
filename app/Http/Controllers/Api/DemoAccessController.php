<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DemoAccessController extends Controller
{
    /**
     * Quick demo login with predefined credentials
     */
    public function quickDemo(Request $request)
    {
        $request->validate([
            'role' => 'sometimes|in:agent,admin,supervisor'
        ]);

        // Get role from request or default to agent
        $role = $request->get('role', 'agent');
        
        // Find demo user by role
        $user = User::where('role', $role)
                    ->where('email', 'like', "%@supportflow.com")
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Demo user not found. Please run seeders first.'
            ], 404);
        }

        // Create token
        $token = $user->createToken('demo-token', ['*'], now()->addDay())->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Demo access granted',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'avatar' => $user->avatar ?? $this->generateAvatar($user->name),
                ],
                'token' => $token,
                'expires_in' => 86400, // 24 hours in seconds
                'demo_info' => [
                    'message' => 'You are using a demo account. Some features may be limited.',
                    'reset_time' => now()->addDay()->toDateTimeString(),
                ]
            ]
        ]);
    }

    /**
     * Get list of available demo accounts
     */
    public function getDemoAccounts()
    {
        $demoAccounts = [
            [
                'role' => 'agent',
                'email' => 'agent@supportflow.com',
                'password' => 'password123',
                'description' => 'Standard agent with ticket management access',
                'capabilities' => ['view_tickets', 'reply_to_tickets', 'update_status']
            ],
            [
                'role' => 'supervisor',
                'email' => 'supervisor@supportflow.com',
                'password' => 'supervisor123',
                'description' => 'Supervisor with team overview and analytics',
                'capabilities' => ['view_all_tickets', 'assign_tickets', 'view_reports']
            ],
            [
                'role' => 'admin',
                'email' => 'admin@supportflow.com',
                'password' => 'admin123',
                'description' => 'Administrator with full system access',
                'capabilities' => ['manage_users', 'system_settings', 'all_features']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $demoAccounts
        ]);
    }

    /**
     * Reset demo data (for admin use)
     */
    public function resetDemoData(Request $request)
    {
        // This endpoint should be protected and only available in development
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => 'Demo reset is not available in production'
            ], 403);
        }

        // Run seeders to refresh demo data
        \Artisan::call('db:seed', ['--class' => 'DemoUserSeeder']);
        
        return response()->json([
            'success' => true,
            'message' => 'Demo data has been reset successfully'
        ]);
    }

    /**
     * Generate avatar from name initials
     */
    private function generateAvatar($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        // Generate a consistent color based on name
        $colors = ['4F46E5', '7C3AED', 'DB2777', 'DC2626', 'EA580C', '65A30D'];
        $colorIndex = crc32($name) % count($colors);
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=" . $colors[$colorIndex] . "&color=fff&size=128";
    }
}
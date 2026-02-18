<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)
                    ->where('is_active', true)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        // Check if this is a demo account
        $isDemo = str_contains($user->email, '@supportflow.com');

        // Revoke old tokens
        $user->tokens()->delete();
        
        // Create new token with appropriate expiration
        $tokenExpiration = $isDemo ? now()->addDay() : now()->addDays(7);
        $token = $user->createToken('auth-token', ['*'], $tokenExpiration)->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'agent',
                    'department' => $user->department ?? 'customer_support',
                    'avatar' => $user->avatar ?? $this->generateAvatar($user->name),
                    'is_demo' => $isDemo,
                ],
                'token' => $token,
                'expires_in' => $isDemo ? 86400 : 604800,
                'demo_warning' => $isDemo ? 'This is a demo account. Data may reset periodically.' : null,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $isDemo = str_contains($user->email, '@supportflow.com');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'department' => $user->department,
                    'avatar' => $user->avatar ?? $this->generateAvatar($user->name),
                    'is_demo' => $isDemo,
                    'last_login' => $user->last_login_at,
                    'permissions' => $this->getUserPermissions($user),
                ]
            ]
        ]);
    }

    private function getUserPermissions($user)
    {
        $permissions = [
            'agent' => [
                'view_assigned_tickets',
                'reply_to_tickets',
                'update_ticket_status',
            ],
            'supervisor' => [
                'view_all_tickets',
                'assign_tickets',
                'view_analytics',
                'manage_agents',
            ],
            'admin' => [
                'all_permissions',
                'manage_users',
                'system_settings',
                'view_logs',
            ]
        ];

        return $permissions[$user->role] ?? $permissions['agent'];
    }

    private function generateAvatar($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        $colors = ['4F46E5', '7C3AED', 'DB2777', 'DC2626', 'EA580C', '65A30D'];
        $colorIndex = crc32($name) % count($colors);
        
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=" . $colors[$colorIndex] . "&color=fff&size=128";
    }
}
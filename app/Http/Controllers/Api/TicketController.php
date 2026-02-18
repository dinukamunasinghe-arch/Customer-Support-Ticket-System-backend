<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
   public function index(Request $request)
    {
        $query = Ticket::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $tickets = $query->with('assignedUser')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ticket = Ticket::create($request->all());

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load('assignedUser')
        ], 201);
    }

    public function show(Ticket $ticket)
    {
     return response()->json([
        'success' => true,
        'data' => [
            'ticket' => $ticket->load(['replies.user', 'assignedUser'])
        ]
    ]);
    }

   public function update(Request $request, Ticket $ticket)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'status' => ['sometimes', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
        'priority' => ['sometimes', Rule::in(['low', 'medium', 'high', 'urgent'])],
        'assigned_to' => 'nullable|exists:users,id'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Update only allowed fields
    $ticket->update($request->only(['status', 'priority', 'assigned_to']));

    return response()->json([
        'success' => true,
        'message' => 'Ticket updated successfully',
        'data' => $ticket->fresh(['assignedUser'])
    ]);
}

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully'
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'urgent' => Ticket::where('priority', 'urgent')->where('status', '!=', 'resolved')->count()
        ];

        return response()->json($stats);
    }
}

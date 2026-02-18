<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketReplyController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'is_staff_reply' => 'boolean',
            'attachments' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reply = $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_staff_reply' => $request->is_staff_reply ?? false,
            'attachments' => $request->attachments
        ]);

        // Update ticket status if it's a staff reply
        if ($request->is_staff_reply && $ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        return response()->json([
            'message' => 'Reply added successfully',
            'reply' => $reply->load('user')
        ], 201);
    }
}
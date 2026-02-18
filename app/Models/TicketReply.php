<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ticket;
use App\Models\User;
class TicketReply extends Model
{
     use HasFactory;
     protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_staff_reply',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_staff_reply' => 'boolean'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

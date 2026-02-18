<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ticket extends Model
{
   use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'category',
        'customer_name',
        'customer_email',
        'customer_phone',
        'assigned_to'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getStatusColorAttribute()
    {
        return [
            'open' => 'blue',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray'
        ][$this->status] ?? 'gray';
    }

    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red'
        ][$this->priority] ?? 'gray';
    }
}

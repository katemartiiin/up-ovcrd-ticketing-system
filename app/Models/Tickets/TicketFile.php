<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'name',
        'path'
    ];

    protected $appends = ['fileUrl'];

    public function ticket() 
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->path);
    }
}

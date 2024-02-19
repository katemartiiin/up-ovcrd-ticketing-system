<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class TicketNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'author_id',
        'author_model',
        'note'
    ];

    protected $appends = ['createdDate', 'authorName'];

    public function ticket() 
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function getAuthorNameAttribute()
    {
        $name = "";

        $author = $this->author_model::findOrFail($this->author_id);
        
        if ($author) {
            $name = $author->name;
        }

        return $name;
    }

    public function getCreatedDateAttribute()
    {
        return Carbon::parse($this->created_at)->addHours(8)->format('Y-m-d g:i a');
    }
}

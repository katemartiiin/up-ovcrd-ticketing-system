<?php

namespace App\Models\Processes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Models\Office;
use App\Models\Tickets\Ticket;

class Process extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'office_id',
        'name',
        'description'
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'process_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Processes\Process;
use App\Models\Tickets\Ticket;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_code',
        'description',
        'head_id',
        'status'
    ];

    protected $appends = ['userCount'];

    public function processes()
    {
        return $this->hasMany(Process::class, 'office_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'office_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'office_id');
    }

    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }
}

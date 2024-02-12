<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'action'
    ];

    protected $appends = ['createdDate'];

    public function getCreatedDateAttribute()
    {
        return Carbon::parse($this->created_at)->addHours(8)->format('d M y g:i a');
    }
}

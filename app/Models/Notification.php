<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'resource_id',
        'description',
        'read'
    ];

    protected $appends = ['createdDate', 'dashboardDate'];

    public function getDashboardDateAttribute() 
    {
        return Carbon::parse($this->created_at)->format('d M y g:i a');
    }

    public function getCreatedDateAttribute() 
    {
        return Carbon::parse($this->created_at)->addHours(8)->format('Y-m-d g:i a');
    }
}

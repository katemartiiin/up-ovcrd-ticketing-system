<?php

namespace App\Models\ResearchIds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Office;
use App\Models\User;

class ResearchId extends Model
{
    use HasFactory;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $fillable = [
        'office_id',
        'research_code',
        'client_id',
        'status'
    ];

    protected $appends = ['officeCode', 'staffCount', 'clientName'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function staffs()
    {
        return $this->hasMany(ResearchIdUser::class, 'research_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function getClientNameAttribute()
    {
        return $this->client()->first()->name;
    }

    public function getOfficeCodeAttribute()
    {
        return $this->office()->first()->short_code;
    }

    public function getStaffCountAttribute()
    {
        return $this->staffs()->count();
    }
}

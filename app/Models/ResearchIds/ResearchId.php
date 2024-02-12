<?php

namespace App\Models\ResearchIds;

use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchId extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'research_code'
    ];

    protected $appends = ['officeCode', 'staffCount'];

    public function staffs()
    {
        return $this->hasMany(ResearchIdUser::class, 'research_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
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

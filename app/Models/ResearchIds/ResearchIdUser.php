<?php

namespace App\Models\ResearchIds;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Users\Admin;

class ResearchIdUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_id',
        'admin_id'
    ];

    public function research()
    {
        return $this->belongsTo(ResearchId::class, 'research_id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}

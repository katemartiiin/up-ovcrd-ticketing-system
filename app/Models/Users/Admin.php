<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

use App\Models\ActivityLog;
use App\Models\Office;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'office_id',
        'image_path',
        'gauth_id',
        'gauth_type',
        'role'
    ];

    protected $appends = ['officeCode'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function getOfficeCodeAttribute() {
        $officeCode = "";

        $officeCode = $this->office_id == 1 ? "N/A" : $this->office()->first()->short_code;

        return $officeCode;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

use App\Models\ActivityLog;
use App\Models\Office;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    const ROLE_ADMIN = 1;
    const ROLE_STAFF = 2;
    const ROLE_DIRECTOR = 3;
    const ROLE_VC = 4;
    const ROLE_CLIENT = 5;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

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
        'image_path',
        'google_id',
        'google_token',
        'google_avatar',
        'status',
        'role',
        'office_id'
    ];

    protected $appends = ['office', 'officeCode', 'roleName', 'avatarUrl'];

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

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function getOfficeAttribute()
    {
        $office = "";

        if ($this->office_id != "") {
            $office = Office::findOrFail($this->office_id);
        }

        return $office;
    }

    public function getOfficeCodeAttribute() {
        $officeCode = "";

        if ($this->office_id != "") {
            $officeCode = $this->getOfficeAttribute()->short_code;
        }

        return $officeCode;
    }

    public function getRoleNameAttribute() {
        $roleName = "Client";

        if ($this->role != self::ROLE_CLIENT) {
            $roleName = Role::findOrFail($this->role)->name;
        }

        return $roleName;
    }

    public function getAvatarUrlAttribute() {
        $path = "";

        if ($this->image_path != "") {
            $path = Storage::url($this->image_path);
        }

        return $path;
    }
}

<?php

namespace App\Models;

use App\Models\Processes\Process;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

use App\Models\Users\Admin;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'office_id',
        'admin_id',
        'title',
        'path',
        'from',
        'to'
    ];

    protected $appends = ['createdDate', 'fileUrl', 'officeCode', 'processName'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getOfficeCodeAttribute()
    {
        $code = "";
        if ($this->office_id != "") {
            $code = Office::findOrFail($this->office_id)->short_code;
        }
        return $code;
    }

    public function getProcessNameAttribute()
    {
        $process = "";
        if ($this->process_id != "") {
            $process = Process::findOrFail($this->process_id)->name;
        }
        return $process;
    }

    public function getCreatedDateAttribute() 
    {
        return Carbon::parse($this->created_at)->addHours(8)->format('Y-m-d g:i a');
    }
}

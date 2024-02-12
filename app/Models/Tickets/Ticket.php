<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\Models\Office;
use App\Models\Users\User;
use App\Models\Processes\Process;
use App\Models\ResearchIds\ResearchId;
use App\Models\Users\Admin;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_ACTIVE = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_RESOLVED = 2;

    protected $fillable = [
        'control_number',
        'office_id',
        'user_id',
        'staff_id',
        'process_id',
        'research_id',
        'title',
        'description',
        'status'
    ];

    protected $appends = [
        'statusLabel', 
        'createdDate', 
        'officeCode', 
        'processName',
        'ticketDue',
        'researchCode',
        'isClaimed',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class, 'ticket_id');
    }

    public function files() 
    {
        return $this->hasMany(TicketFile::class, 'ticket_id');
    }

    public function notes()
    {
        return $this->hasMany(TicketNote::class, 'ticket_id');
    }

    public function getStatusLabelAttribute() {

        $label = "";

        switch($this->status) {
            case self::STATUS_ACTIVE:
                $label = "Active";
                break;
            case self::STATUS_COMPLETED:
                $label = "Completed";
                break;
            case self::STATUS_RESOLVED:
                $label = "Resolved";
                break;
            default:
                $label = "Active";
                break;
        }

        return $label;
    }

    public function getOfficeCodeAttribute()
    {
        return $this->office()->first()->short_code;
    }

    public function getProcessNameAttribute()
    {
        return $this->process()->first()->name;
    }

    public function getTicketDueAttribute()
    {
        $currentDate = Carbon::now();
        $createdDate = Carbon::parse($this->updated_at);
        $duration = $currentDate->diffInDays($createdDate);

        return $duration;
    }

    public function getResearchCodeAttribute()
    {
        $code = "";
        if ($this->research_id != "") {
            $research = ResearchId::findOrFail($this->research_id);
            $code = $research->research_code;
        }

        return $code;
    }

    public function getIsClaimedAttribute()
    {
        $claimed = "";

        if ($this->staff_id != "") {
            $claimed = Admin::findOrFail($this->staff_id)->name;
        }

        return $claimed;
    }

    public function getCreatedDateAttribute() 
    {
        return Carbon::parse($this->created_at)->addHours(8)->format('Y-m-d g:i a');
    }
}

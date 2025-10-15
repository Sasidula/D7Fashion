<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'salary_type',
        'salary_rate',
        'calculated_salary',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Automatically set snapshot salary info when creating
     */
    protected static function booted()
    {
        static::creating(function ($attendance) {
            $user = User::find($attendance->user_id);
            if ($user) {
                $attendance->salary_type = $user->salary_type ?? 'none';
                $attendance->salary_rate = $user->salary_amount ?? 0;
            }
        });
    }

    /**
     * Calculate and update the salary for this attendance
     */
    public function calculateDailySalary()
    {
        if (!$this->check_in || !$this->check_out) {
            $this->calculated_salary = 0;
            $this->save();
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);
        $workedHours = $checkOut->diffInMinutes($checkIn, true) / 60;

        if ($this->salary_type === 'hourly') {
            $this->calculated_salary = $workedHours * $this->salary_rate;
        } elseif ($this->salary_type === 'monthly') {
            // monthly now means full fixed salary
            $this->calculated_salary = $this->salary_rate;
        } else {
            $this->calculated_salary = 0;
        }

        $this->save();
        return $this->calculated_salary;
    }
}



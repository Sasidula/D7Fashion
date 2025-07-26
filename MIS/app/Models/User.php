<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'salary_type',
        'salary_amount',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'salary_amount' => 'decimal:2',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function bonusAdjustments()
    {
        return $this->hasMany(EmployeeBonusAdjustment::class);
    }

    public function materialAssignments()
    {
        return $this->hasMany(MaterialAssignment::class);
    }

    public function assignedMaterials() // For materials assigned *by* this user
    {
        return $this->hasMany(MaterialAssignment::class, 'assigned_by');
    }

    public function internalProductItemsCreated()
    {
        return $this->hasMany(InternalProductItem::class, 'created_by');
    }

    public function externalProductItemsCreated()
    {
        return $this->hasMany(ExternalProductItem::class, 'created_by');
    }

}

<?php

declare(strict_types=1);

namespace Moe\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Moe\HRM\Contracts\EmployeeInterface;

class Employee extends Model implements EmployeeInterface
{
    use SoftDeletes;

    protected $table;

    protected $fillable = [
        'user_id',
        'employee_number',
        'full_name',
        'nickname',
        'position',
        'department_id',
        'supervisor_id',
        'join_date',
        'resign_date',
        'employment_status',
        'employment_type',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'marital_status',
        'identity_number',
        'identity_address',
        'current_address',
        'phone',
        'emergency_contact',
        'emergency_phone',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',
        'npwp',
        'is_active',
    ];

    protected $casts = [
        'join_date' => 'date',
        'resign_date' => 'date',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'identity_number',
        'bank_account_number',
        'npwp',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('hrm.tables.employees', 'hrm_employees');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('hrm.models.user', 'App\\Models\\User'));
    }

    /**
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subordinates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payrolls(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    // EmployeeInterface
    public function getEmployeeNumber(): string
    {
        return $this->employee_number;
    }

    public function getFullName(): string
    {
        return $this->full_name;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getDepartment(): string
    {
        return $this->department?->name ?? '-';
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }
}

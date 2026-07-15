<?php

declare(strict_types=1);

namespace Moe\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Moe\HRM\Contracts\AttendanceInterface;

class Attendance extends Model implements AttendanceInterface
{
    use SoftDeletes;

    protected $table;

    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_SICK = 'sick';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_PERMIT = 'permit';

    public const STATUS_LABELS = [
        self::STATUS_PRESENT => 'Hadir',
        self::STATUS_LATE => 'Terlambat',
        self::STATUS_ABSENT => 'Tidak Hadir',
        self::STATUS_SICK => 'Sakit',
        self::STATUS_LEAVE => 'Cuti',
        self::STATUS_PERMIT => 'Izin',
    ];

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'notes',
        'clock_in_location',
        'clock_out_location',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('hrm.tables.attendances', 'hrm_attendances');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(config('hrm.models.user', 'App\\Models\\User'), 'approved_by');
    }

    // AttendanceInterface
    public function clockIn(): void
    {
        $this->update([
            'clock_in' => now(),
            'status' => self::STATUS_PRESENT,
        ]);
    }

    public function clockOut(): void
    {
        $this->update(['clock_out' => now()]);
    }

    public function isClockedIn(): bool
    {
        return $this->clock_in !== null && $this->clock_out === null;
    }

    public function getTodayAttendance(): ?Attendance
    {
        return self::where('employee_id', $this->employee_id)
            ->whereDate('date', today())
            ->first();
    }

    public function getMonthlyAttendance(int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('employee_id', $this->employee_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();
    }

    public function getDurationAttribute(): ?string
    {
        if ($this->clock_in && $this->clock_out) {
            $diff = $this->clock_out->diff($this->clock_in);
            return $diff->format('%H:%I');
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }
}

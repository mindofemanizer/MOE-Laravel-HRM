<?php

namespace Moe\HRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Moe\HRM\Contracts\PayrollableInterface;

class Payroll extends Model implements PayrollableInterface
{
    use SoftDeletes;

    protected $table;

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draf',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_PAID => 'Dibayar',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = [
        'employee_id',
        'period_year',
        'period_month',
        'base_salary',
        'allowance_bpjs',
        'allowance_meal',
        'allowance_transport',
        'allowance_overtime',
        'allowance_bonus',
        'allowance_other',
        'allowance_total',
        'deduction_bpjs',
        'deduction_pph21',
        'deduction_absence',
        'deduction_late',
        'deduction_loan',
        'deduction_other',
        'deduction_total',
        'net_salary',
        'payment_day',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowance_bpjs' => 'decimal:2',
        'allowance_meal' => 'decimal:2',
        'allowance_transport' => 'decimal:2',
        'allowance_overtime' => 'decimal:2',
        'allowance_bonus' => 'decimal:2',
        'allowance_other' => 'decimal:2',
        'allowance_total' => 'decimal:2',
        'deduction_bpjs' => 'decimal:2',
        'deduction_pph21' => 'decimal:2',
        'deduction_absence' => 'decimal:2',
        'deduction_late' => 'decimal:2',
        'deduction_loan' => 'decimal:2',
        'deduction_other' => 'decimal:2',
        'deduction_total' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_day' => 'integer',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $hidden = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('hrm.tables.payrolls', 'hrm_payrolls');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(config('hrm.models.user', 'App\\Models\\User'), 'approved_by');
    }

    // PayrollableInterface
    public function getBaseSalary(): float
    {
        return (float) $this->base_salary;
    }

    public function getAllowances(): float
    {
        return (float) $this->allowance_total;
    }

    public function getDeductions(): float
    {
        return (float) $this->deduction_total;
    }

    public function calculateNetSalary(): float
    {
        return $this->getBaseSalary() + $this->getAllowances() - $this->getDeductions();
    }

    public function getPaymentDay(): int
    {
        return $this->payment_day ?? config('hrm.payroll.default_payment_day', 25);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function markPaid(): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }
}

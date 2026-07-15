<?php

declare(strict_types=1);

namespace Moe\HRM\Services;

use Moe\Core\Base\BaseService;
use Moe\HRM\Models\Employee;
use Moe\HRM\Models\Payroll;

class PayrollService extends BaseService
{
    /**
     * Generate payroll for a month.
     *
     * @param int $year
     * @param int $month
     * @return int
     */
    public function generateMonthly(int $year, int $month): int
    {
        $employees = Employee::where('is_active', true)->get();
        $generated = 0;

        foreach ($employees as $employee) {
            $baseSalary = config('hrm.payroll.default_base_salary', 0);

            $attendanceService = app(AttendanceService::class);
            $report = $attendanceService->getMonthlyReport($employee->id, $year, $month);

            $allowanceTotal = $baseSalary * config('hrm.payroll.allowance_rate', 0.1);
            $absenceDeduction = $report['absent'] * 50000;
            $lateDeduction = $report['late'] * 25000;
            $deductionTotal = $absenceDeduction + $lateDeduction;

            $netSalary = $baseSalary + $allowanceTotal - $deductionTotal;

            Payroll::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'period_year' => $year,
                    'period_month' => $month,
                ],
                [
                    'base_salary' => $baseSalary,
                    'allowance_total' => $allowanceTotal,
                    'deduction_total' => $deductionTotal,
                    'net_salary' => max($netSalary, 0),
                    'payment_day' => config('hrm.payroll.default_payment_day', 25),
                    'status' => Payroll::STATUS_DRAFT,
                ]
            );

            $generated++;
        }

        return $generated;
    }

    /**
     * Payroll batch approve.
     *
     * @param int $year
     * @param int $month
     * @return int
     */
    public function batchApprove(int $year, int $month): int
    {
        return Payroll::where('period_year', $year)
            ->where('period_month', $month)
            ->where('status', Payroll::STATUS_DRAFT)
            ->get()
            ->each(fn ($payroll) => $payroll->approve())
            ->count();
    }
}

<?php

use Moe\HRM\Models\Attendance;
use Moe\HRM\Models\Department;
use Moe\HRM\Models\Employee;
use Moe\HRM\Services\AttendanceService;
use Moe\HRM\Services\PayrollService;

beforeEach(function () {
    $department = Department::create(['name' => 'IT', 'code' => 'IT', 'is_active' => true]);
    $this->employee = Employee::create([
        'employee_number' => 'EMP-001',
        'full_name' => 'Budi Santoso',
        'department_id' => $department->id,
        'position' => 'Developer',
        'is_active' => true,
    ]);

    $this->attendanceService = new AttendanceService();
    $this->payrollService = new PayrollService();
});

it('can clock in', function () {
    $attendance = $this->attendanceService->clockIn($this->employee->id);

    expect($attendance)->toBeInstanceOf(Attendance::class);
    expect($attendance->clock_in)->not->toBeNull();
    expect($attendance->status)->toEqual(Attendance::STATUS_PRESENT);
});

it('can clock out', function () {
    $this->attendanceService->clockIn($this->employee->id);
    $attendance = $this->attendanceService->clockOut($this->employee->id);

    expect($attendance->clock_out)->not->toBeNull();
});

it('gets monthly report', function () {
    $this->attendanceService->clockIn($this->employee->id);
    $this->attendanceService->clockOut($this->employee->id);

    $report = $this->attendanceService->getMonthlyReport($this->employee->id, now()->year, now()->month);

    expect($report)->toBeArray();
    expect($report)->toHaveKey('present');
    expect($report)->toHaveKey('late');
});

it('can generate payroll', function () {
    $generated = $this->payrollService->generateMonthly(now()->year, now()->month);
    expect($generated)->toEqual(1);
});

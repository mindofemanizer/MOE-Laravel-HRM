<?php

namespace Moe\HRM\Tests;

use Moe\HRM\Models\Department;
use Moe\HRM\Models\Employee;
use Moe\HRM\Models\Attendance;
use Moe\HRM\Services\AttendanceService;
use Moe\HRM\Services\PayrollService;

class HRMServiceTest extends TestCase
{
    private AttendanceService $attendanceService;
    private PayrollService $payrollService;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_can_clock_in()
    {
        $attendance = $this->attendanceService->clockIn($this->employee->id);

        $this->assertInstanceOf(Attendance::class, $attendance);
        $this->assertNotNull($attendance->clock_in);
        $this->assertEquals(Attendance::STATUS_PRESENT, $attendance->status);
    }

    public function test_can_clock_out()
    {
        $this->attendanceService->clockIn($this->employee->id);
        $attendance = $this->attendanceService->clockOut($this->employee->id);

        $this->assertNotNull($attendance->clock_out);
    }

    public function test_get_monthly_report()
    {
        $this->attendanceService->clockIn($this->employee->id);
        $this->attendanceService->clockOut($this->employee->id);

        $report = $this->attendanceService->getMonthlyReport($this->employee->id, now()->year, now()->month);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('present', $report);
        $this->assertArrayHasKey('late', $report);
    }

    public function test_can_generate_payroll()
    {
        $generated = $this->payrollService->generateMonthly(now()->year, now()->month);
        $this->assertEquals(1, $generated);
    }
}

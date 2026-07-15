<?php

declare(strict_types=1);

namespace Moe\HRM\Services;

use Moe\Core\Base\BaseService;
use Moe\HRM\Models\Attendance;
use Moe\HRM\Models\Employee;

class AttendanceService extends BaseService
{
    /**
     * Clock in employee.
     */
    public function clockIn(int $employeeId, ?string $location = null): Attendance
    {
        $date = today();

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employeeId, 'date' => $date],
            ['status' => Attendance::STATUS_PRESENT]
        );

        if ($attendance->clock_in) {
            throw new \Exception('Sudah clock in hari ini');
        }

        $attendance->clockIn();

        if ($location) {
            $attendance->update(['clock_in_location' => $location]);
        }

        $fresh = $attendance->fresh();
        $lateCutoff = config('hrm.attendance.late_cutoff', '08:00');
        $clockInTime = $fresh->clock_in;
        $cutoffTime = $clockInTime->copy()->setTimeFromTimeString($lateCutoff);

        if ($clockInTime->gt($cutoffTime)) {
            $lateMinutes = (int) $clockInTime->diffInMinutes($cutoffTime);
            $fresh->update([
                'status' => Attendance::STATUS_LATE,
                'late_minutes' => $lateMinutes,
            ]);
        }

        return $attendance;
    }

    /**
     * Clock out employee.
     */
    public function clockOut(int $employeeId): Attendance
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', today())
            ->first();

        if (! $attendance || ! $attendance->clock_in) {
            throw new \Exception('Belum clock in hari ini');
        }

        if ($attendance->clock_out) {
            throw new \Exception('Sudah clock out hari ini');
        }

        $attendance->clockOut();

        $fresh = $attendance->fresh();
        $overtimeAfter = config('hrm.attendance.overtime_after', '17:00');
        $clockOut = $fresh->clock_out;
        $overtimeTime = $clockOut->copy()->setTimeFromTimeString($overtimeAfter);

        if ($clockOut->gt($overtimeTime)) {
            $overtimeMinutes = (int) $clockOut->diffInMinutes($overtimeTime);
            $fresh->update(['overtime_minutes' => $overtimeMinutes]);
        }

        return $attendance;
    }

    /**
     * Get monthly attendance report.
     */
    public function getMonthlyReport(int $employeeId, int $year, int $month): array
    {
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $present = 0;
        $late = 0;
        $absent = 0;
        $sick = 0;
        $leave = 0;
        $permit = 0;
        $totalLateMinutes = 0;
        $totalOvertimeMinutes = 0;

        foreach ($attendances as $attendance) {
            switch ($attendance->status) {
                case Attendance::STATUS_PRESENT:
                    $present++;
                    break;
                case Attendance::STATUS_LATE:
                    $late++;
                    $totalLateMinutes += $attendance->late_minutes;
                    break;
                case Attendance::STATUS_ABSENT:
                    $absent++;
                    break;
                case Attendance::STATUS_SICK:
                    $sick++;
                    break;
                case Attendance::STATUS_LEAVE:
                    $leave++;
                    break;
                case Attendance::STATUS_PERMIT:
                    $permit++;
                    break;
            }
            $totalOvertimeMinutes += $attendance->overtime_minutes;
        }

        return [
            'total' => $attendances->count(),
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'sick' => $sick,
            'leave' => $leave,
            'permit' => $permit,
            'total_late_minutes' => $totalLateMinutes,
            'total_overtime_minutes' => $totalOvertimeMinutes,
        ];
    }
}

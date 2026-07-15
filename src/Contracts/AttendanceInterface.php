<?php

namespace Moe\HRM\Contracts;

interface AttendanceInterface
{
    public function clockIn(): void;
    public function clockOut(): void;
    public function isClockedIn(): bool;
    public function getTodayAttendance(): ?\Illuminate\Database\Eloquent\Model;
    public function getMonthlyAttendance(int $year, int $month): \Illuminate\Database\Eloquent\Collection;
}

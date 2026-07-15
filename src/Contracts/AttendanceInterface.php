<?php

declare(strict_types=1);

namespace Moe\HRM\Contracts;

interface AttendanceInterface
{
    /**
     * @return void
     */
    public function clockIn(): void;

    /**
     * @return void
     */
    public function clockOut(): void;

    /**
     * @return bool
     */
    public function isClockedIn(): bool;

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getTodayAttendance(): ?\Illuminate\Database\Eloquent\Model;

    /**
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMonthlyAttendance(int $year, int $month): \Illuminate\Database\Eloquent\Collection;
}

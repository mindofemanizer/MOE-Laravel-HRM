# MOE-Laravel-HRM

HRM module for MOE ecosystem — Employee, Attendance, Payroll.

## Installation

```bash
composer require moe/laravel-hrm
php artisan vendor:publish --provider="Moe\HRM\HRMServiceProvider" --tag="hrm-config"
php artisan vendor:publish --provider="Moe\HRM\HRMServiceProvider" --tag="hrm-migrations"
php artisan migrate
```

## What's Included

### Models

| Model | Table | Description |
|-------|-------|-------------|
| `Employee` | `hrm_employees` | Data karyawan |
| `Department` | `hrm_departments` | Departemen |
| `Attendance` | `hrm_attendances` | Absensi harian |
| `Payroll` | `hrm_payrolls` | Penggajian |
| `Leave` | `hrm_leaves` | Cuti & izin |

### Services

| Service | Description |
|---------|-------------|
| `AttendanceService` | Clock in/out, monthly report |
| `PayrollService` | Generate payroll, batch approve |

### Contracts

| Contract | Description |
|----------|-------------|
| `EmployeeInterface` | Interface for employee data |
| `PayrollableInterface` | Interface for payroll calculation |
| `AttendanceInterface` | Interface for attendance |

## Usage

### Attendance

```php
use Moe\HRM\Services\AttendanceService;

$attendanceService = app(AttendanceService::class);

// Clock in
$attendanceService->clockIn($employeeId);

// Clock out
$attendanceService->clockOut($employeeId);

// Monthly report
$report = $attendanceService->getMonthlyReport($employeeId, 2026, 7);
```

### Payroll

```php
use Moe\HRM\Services\PayrollService;

$payrollService = app(PayrollService::class);

// Generate payroll for all employees
$generated = $payrollService->generateMonthly(2026, 7);

// Batch approve
$approved = $payrollService->batchApprove(2026, 7);
```

## Config

```php
// config/hrm.php
return [
    'attendance' => [
        'late_threshold_minutes' => 15,
        'late_cutoff' => '08:00',
        'overtime_after' => '17:00',
    ],
    'payroll' => [
        'default_payment_day' => 25,
        'allowance_rate' => 0.1,
    ],
];
```

## Requirements

- PHP ^8.2
- Laravel ^12.0|^13.0
- `moe/laravel-core`
- `moe/laravel-finance`

## License

MIT

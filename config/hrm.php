<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Model Bindings
    |--------------------------------------------------------------------------
    */

    'models' => [

        'user' => App\Models\User::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    */

    'tables' => [

        'employees' => 'hrm_employees',

        'departments' => 'hrm_departments',

        'attendances' => 'hrm_attendances',

        'payrolls' => 'hrm_payrolls',

        'leaves' => 'hrm_leaves',

    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Settings
    |--------------------------------------------------------------------------
    */

    'attendance' => [

        'late_threshold_minutes' => env('ATTENDANCE_LATE_THRESHOLD', 15),

        'late_cutoff' => env('ATTENDANCE_LATE_CUTOFF', '08:00'),

        'overtime_after' => env('ATTENDANCE_OVERTIME_AFTER', '17:00'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Payroll Settings
    |--------------------------------------------------------------------------
    */

    'payroll' => [

        'default_payment_day' => env('PAYROLL_PAYMENT_DAY', 25),

        'allowance_rate' => env('PAYROLL_ALLOWANCE_RATE', 0.1),

    ],

];

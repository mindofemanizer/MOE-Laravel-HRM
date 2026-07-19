<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departments
        Schema::create('hrm_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('head_employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Employees
        Schema::create('hrm_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_number', 30)->unique();
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('position')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->nullOnDelete();
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->string('employment_status', 50)->default('permanent')->comment('permanent/contract/probation/intern');
            $table->string('employment_type', 50)->default('full_time')->comment('full_time/part_time/freelance');
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('identity_number')->nullable();
            $table->text('identity_address')->nullable();
            $table->text('current_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('bpjs_ketenagakerjaan')->nullable();
            $table->string('bpjs_kesehatan')->nullable();
            $table->string('npwp')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'is_active']);
            $table->foreign('supervisor_id')->references('id')->on('hrm_employees')->nullOnDelete();
        });

        // Attendances
        Schema::create('hrm_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->string('status', 50)->default('present');
            $table->integer('late_minutes')->default(0);
            $table->integer('early_leave_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->string('clock_in_location')->nullable();
            $table->string('clock_out_location')->nullable();
            $table->ulid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'date']);
            $table->index(['employee_id', 'date']);
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // Payrolls
        Schema::create('hrm_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->integer('period_year');
            $table->integer('period_month');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('allowance_bpjs', 15, 2)->default(0);
            $table->decimal('allowance_meal', 15, 2)->default(0);
            $table->decimal('allowance_transport', 15, 2)->default(0);
            $table->decimal('allowance_overtime', 15, 2)->default(0);
            $table->decimal('allowance_bonus', 15, 2)->default(0);
            $table->decimal('allowance_other', 15, 2)->default(0);
            $table->decimal('allowance_total', 15, 2)->default(0);
            $table->decimal('deduction_bpjs', 15, 2)->default(0);
            $table->decimal('deduction_pph21', 15, 2)->default(0);
            $table->decimal('deduction_absence', 15, 2)->default(0);
            $table->decimal('deduction_late', 15, 2)->default(0);
            $table->decimal('deduction_loan', 15, 2)->default(0);
            $table->decimal('deduction_other', 15, 2)->default(0);
            $table->decimal('deduction_total', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->integer('payment_day')->default(25);
            $table->string('status', 50)->default('draft');
            $table->text('notes')->nullable();
            $table->ulid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'period_year', 'period_month']);
            $table->index(['period_year', 'period_month']);
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        // Leaves
        Schema::create('hrm_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->cascadeOnDelete();
            $table->string('type', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason')->nullable();
            $table->string('status', 50)->default('pending');
            $table->ulid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_leaves');
        Schema::dropIfExists('hrm_payrolls');
        Schema::dropIfExists('hrm_attendances');
        Schema::dropIfExists('hrm_employees');
        Schema::dropIfExists('hrm_departments');
    }
};

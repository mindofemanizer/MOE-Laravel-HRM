<?php

namespace Moe\HRM\Contracts;

interface PayrollableInterface
{
    public function getBaseSalary(): float;
    public function getAllowances(): float;
    public function getDeductions(): float;
    public function calculateNetSalary(): float;
    public function getPaymentDay(): int;
}

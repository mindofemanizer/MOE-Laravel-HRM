<?php

declare(strict_types=1);

namespace Moe\HRM\Contracts;

interface PayrollableInterface
{
    /**
     * @return float
     */
    public function getBaseSalary(): float;

    /**
     * @return float
     */
    public function getAllowances(): float;

    /**
     * @return float
     */
    public function getDeductions(): float;

    /**
     * @return float
     */
    public function calculateNetSalary(): float;

    /**
     * @return int
     */
    public function getPaymentDay(): int;
}

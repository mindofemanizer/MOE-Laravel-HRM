<?php

declare(strict_types=1);

namespace Moe\HRM\Contracts;

interface EmployeeInterface
{
    /**
     * @return string
     */
    public function getEmployeeNumber(): string;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getPosition(): string;

    /**
     * @return string
     */
    public function getDepartment(): string;

    /**
     * @return bool
     */
    public function isActive(): bool;
}

<?php

declare(strict_types=1);

namespace Moe\HRM\Contracts;

interface EmployeeInterface
{
    public function getEmployeeNumber(): string;
    public function getFullName(): string;
    public function getPosition(): string;
    public function getDepartment(): string;
    public function isActive(): bool;
}

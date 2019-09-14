<?php

declare(strict_types=1);

namespace App\Core\Faker;

interface FakerInterface
{
    public function getUserName(): string;
    public function getEmail(): string;
    public function getText(): string;
}

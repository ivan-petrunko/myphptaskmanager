<?php

declare(strict_types=1);

namespace App\Core\Security;

interface AuthInterface
{
    public function login(string $user, string $password): bool;

    public function logout(): void;

    public function isAllowed(int $action): bool;

    public function isLoggedIn(): bool;
}

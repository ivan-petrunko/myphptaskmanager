<?php

declare(strict_types=1);

namespace App\Core\Security;

class Auth implements AuthInterface
{
    private const COOKIE_NAME = 'auth';

    private const COOKIE_LIFETIME = 14400; // 4 hours

    public function login(string $user, string $password): bool
    {
        if ($user === 'admin' && $password === '123') {
            $_COOKIE[self::COOKIE_NAME] = '1';
            setcookie(self::COOKIE_NAME, '1', time() + self::COOKIE_LIFETIME, '/');
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        unset($_COOKIE[self::COOKIE_NAME]);
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
    }

    public function isAllowed(int $action): bool
    {
        // TODO: this is just for example. should be real permission check.
        return $this->isLoggedIn() && in_array($action, [Action::EDIT_TASK_TEXT, Action::MARK_TASK_DONE], true);
    }

    public function isLoggedIn(): bool
    {
        return isset($_COOKIE[self::COOKIE_NAME]);
    }
}

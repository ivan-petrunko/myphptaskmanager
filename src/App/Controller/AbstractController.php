<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AbstractController
{
    final public function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }

    /**
     * Simple input cleaner, strip tags, limit length.
     * @param string $value
     * @param int $maxLength
     * @return string
     */
    final protected function cleanInput(string $value, int $maxLength = 0): string
    {
        $result = htmlentities(trim(strip_tags($value)));
        if ($maxLength > 0) {
            $result = mb_substr($result, 0, $maxLength);
        }
        return $result;
    }
}

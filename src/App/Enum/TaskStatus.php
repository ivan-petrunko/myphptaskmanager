<?php

declare(strict_types=1);

namespace App\Enum;

final class TaskStatus
{
    public const NEW = 0;
    public const DONE = 1;

    private static $describeMap = [
        self::NEW => [
            'title' => 'Новая',
        ],
        self::DONE => [
            'title' => 'Завершена',
        ],
    ];

    /**
     * @return array
     */
    public static function getDescribeMap(): array
    {
        return self::$describeMap;
    }

    /**
     * @param int $status
     * @return string
     */
    public static function getTitle(int $status): string
    {
        return self::$describeMap[$status]['title'] ?? '';
    }
}

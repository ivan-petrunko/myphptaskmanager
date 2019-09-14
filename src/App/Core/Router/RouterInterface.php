<?php

declare(strict_types=1);

namespace App\Core\Router;

interface RouterInterface
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    /**
     * @param string $route
     * @param callable $callable
     * @param string $method 'GET'|'POST'
     * @return RouterInterface
     */
    public function addRoute(string $route, callable $callable, string $method = self::METHOD_GET): self;

    public function dispatch(): void;
}

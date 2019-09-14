<?php

declare(strict_types=1);

namespace App\Core\Router;

class Router implements RouterInterface
{
    private const SUPPORTED_METHODS = [self::METHOD_GET, self::METHOD_POST];

    /**
     * @var callable[]
     */
    private $getRoutes = [];

    /**
     * @var callable[]
     */
    private $postRoutes = [];

    public function addRoute(string $route, callable $callable, string $method = self::METHOD_GET): RouterInterface
    {
        if (!in_array($method, self::SUPPORTED_METHODS, true)) {
            throw new \InvalidArgumentException("Unsupported method {$method}");
        }
        switch ($method) {
            case self::METHOD_GET:
                $this->getRoutes[$route] = $callable;
                break;
            case self::METHOD_POST:
                $this->postRoutes[$route] = $callable;
                break;
        }
        return $this;
    }

    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $queryString = $_SERVER['REQUEST_URI'] ?? '/';

        if (in_array($requestMethod, self::SUPPORTED_METHODS, true)) {
            $queryString = $this->removeQueryStringVariables($queryString);
            [$callable, $matches] = $this->match($queryString, $requestMethod);
            if (is_callable($callable)) {
                $callable([
                    'request' => $_REQUEST ?? [],
                    'files' => $_FILES ?? [],
                    'matches' => $matches,
                ]);
                return;
            }
        }
        // TODO: refactor
        [$callable, $matches] = $this->match('/404.html', RouterInterface::METHOD_GET);
        $callable();
    }

    private function removeQueryStringVariables(string $url): string
    {
        if (!empty($url)) {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    private function match(string $queryString, string $method): ?array
    {
        $result = null;
        switch ($method) {
            case self::METHOD_GET:
                foreach ($this->getRoutes as $match => $callable) {
                    if (preg_match($match, $queryString, $matches)) {
                        $result = [$callable, $matches];
                        break;
                    }
                }
                break;
            case self::METHOD_POST:
                foreach ($this->postRoutes as $match => $callable) {
                    if (preg_match($match, $queryString, $matches)) {
                        $result = [$callable, $matches];
                        break;
                    }
                }
                break;
        }
        return $result;
    }
}

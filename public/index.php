<?php

declare(strict_types=1);

namespace {

    use App\Core\Router\RouterInterface;
    use Psr\Container\ContainerInterface;

    if (PHP_SAPI === 'cli-server') {
        // To help the built-in PHP dev server, check if the request was actually for
        // something which should probably be served as a static file
        $url  = parse_url($_SERVER['REQUEST_URI']);
        $file = __DIR__ . $url['path'];
        if (is_file($file)) {
            return false;
        }
    }

    /** @var ContainerInterface $container */
    $container = require __DIR__ . '/../src/bootstrap.php';

    /** @var RouterInterface $router */
    $router = $container->get(RouterInterface::class);
    $router->dispatch();
}

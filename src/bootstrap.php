<?php

declare(strict_types=1);

namespace {

    use DI\ContainerBuilder;
    use Psr\Log\LoggerInterface;

    require __DIR__ . '/../vendor/autoload.php';

    // Load .env
    $dotenv = \Dotenv\Dotenv::create([__DIR__ . '/..', __DIR__ . '/../../..']);
    $dotenv->load();

    try {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . 'config.php');
        $container = $containerBuilder->build();

        $logger = $container->get(LoggerInterface::class);

        set_error_handler(function (int $errno, string $errstr) use ($logger): bool {
            $logger->error('Error', ['errno' => $errno, 'errstr' => $errstr,]);
            return true;
        });
        set_exception_handler(function (\Throwable $e) use ($logger): void {
            $logger->error('Exception', ['message' => $e->getMessage(),]);
        });

        return $container;
    } catch (\Exception $e) {
        die('DI exception caught.' . PHP_EOL . $e->getMessage() . PHP_EOL);
    }
}

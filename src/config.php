<?php

declare(strict_types=1);

namespace {

    use App\Controller\AdminController;
    use App\Controller\AjaxController;
    use App\Controller\DefaultController;
    use App\Controller\TaskController;
    use App\Core\Faker\FakerInterface;
    use App\Core\Faker\LoremIpsumFaker;
    use App\Core\Router\Router;
    use App\Core\Router\RouterInterface;
    use App\Core\Security\Auth;
    use App\Core\Security\AuthInterface;
    use App\Model\Image;
    use App\Model\Task;
    use App\Model\TaskImage;
    use App\View\View;
    use App\View\ViewInterface;
    use joshtronic\LoremIpsum;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Psr\Container\ContainerInterface;
    use Psr\Log\LoggerInterface;

    return [
        // PDO
        PDO::class => function () {
            return new PDO(
                'mysql:host=' . (string)getenv('DB_HOST') . ';' .
                'port=' . (int)getenv('DB_PORT') . ';' .
                'dbname=' . (string)getenv('DB_NAME') . ';' .
                'charset=' . (string)getenv('DB_CHARSET'),
                (string)getenv('DB_USER'),
                (string)getenv('DB_PASSWORD')
            );
        },

        // log
        LoggerInterface::class => function () {
            return new Logger('logger', [
                new StreamHandler(__DIR__ . '/../var/logs/app/app.log', (int)getenv('LOG_LEVEL'))
            ]);
        },

        // models
        Task::class => function (ContainerInterface $container) {
            return new Task($container->get(PDO::class));
        },
        Image::class => function (ContainerInterface $container) {
            return new Image($container->get(PDO::class));
        },
        TaskImage::class => function (ContainerInterface $container) {
            return new TaskImage($container->get(PDO::class));
        },

        // views
        ViewInterface::class => DI\autowire(View::class), // autowiring

        // controllers
        DefaultController::class => function (ContainerInterface $container) {
            return new DefaultController(
                $container->get(ViewInterface::class),
                $container->get(AuthInterface::class),
                $container->get('css'),
                $container->get('js')
            );
        },
        AdminController::class => function (ContainerInterface $container) {
            return new AdminController(
                $container->get(ViewInterface::class),
                $container->get(AuthInterface::class),
                $container->get('css'),
                $container->get('js')
            );
        },
        TaskController::class => function (ContainerInterface $container) {
            return new TaskController(
                $container->get(ViewInterface::class),
                $container->get(AuthInterface::class),
                $container->get('css'),
                $container->get('js'),
                $container->get(Task::class),
                $container->get(TaskImage::class),
                $container->get(Image::class)
            );
        },
        AjaxController::class => function (ContainerInterface $container) {
            return new AjaxController(
                $container->get(ViewInterface::class),
                $container->get(AuthInterface::class),
                $container->get(FakerInterface::class),
                $container->get(Task::class),
                $container->get(Image::class)
            );
        },

        // routers
        RouterInterface::class => function (ContainerInterface $container) {
            return (new Router())
                // service routes
                ->addRoute('/^\/404\.html\/?$/', [$container->get(DefaultController::class), 'show404'])

                // task routes
                ->addRoute('/^\/?$/', [$container->get(TaskController::class), 'listTasks'])
                ->addRoute('/^\/add\/?$/', [$container->get(TaskController::class), 'addTask'])
                ->addRoute('/^\/view\/(?<taskId>\d+)\/?$/', [$container->get(TaskController::class), 'viewTask'])
                ->addRoute('/^\/insert_task\/?$/', [$container->get(TaskController::class), 'insertTask'], RouterInterface::METHOD_POST)
                ->addRoute('/^\/task_mark_done\/?$/', [$container->get(TaskController::class), 'taskMarkDone'], RouterInterface::METHOD_POST)

                // admin routes
                ->addRoute('/^\/login\/?$/', [$container->get(AdminController::class), 'loginForm'])
                ->addRoute('/^\/do_login\/?$/', [$container->get(AdminController::class), 'doLogin'], RouterInterface::METHOD_POST)
                ->addRoute('/^\/logout\/?$/', [$container->get(AdminController::class), 'doLogout'])

                // ajax
                ->addRoute('/^\/ajax\/task_text_update\/?$/', [$container->get(AjaxController::class), 'taskTextUpdate'], RouterInterface::METHOD_POST)
                ->addRoute('/^\/ajax\/upload_image\/?$/', [$container->get(AjaxController::class), 'uploadImage'], RouterInterface::METHOD_POST)
                ->addRoute('/^\/ajax\/task_preview\/?$/', [$container->get(AjaxController::class), 'taskPreview'], RouterInterface::METHOD_POST)
                ->addRoute('/^\/ajax\/task_lorem_ipsum\/?$/', [$container->get(AjaxController::class), 'taskLoremIpsum'], RouterInterface::METHOD_POST)
                ;
        },

        // security
        AuthInterface::class => DI\autowire(Auth::class),

        // faker
        FakerInterface::class => function () {
            return new LoremIpsumFaker(new LoremIpsum());
        },

        // css
        'css' => [
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
        ],

        // js
        'js' => [
            'https://code.jquery.com/jquery-3.4.1.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js',
            'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js',
            '/assets/js/main.js',
        ],
    ];
}

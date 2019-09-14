<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Html\Context;
use App\Core\Security\AuthInterface;
use App\View\ViewInterface;

class DefaultController extends AbstractController
{
    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var string[]
     */
    protected $css;

    /**
     * @var string[]
     */
    protected $js;

    /**
     * DefaultController constructor.
     * @param ViewInterface $view
     * @param AuthInterface $auth
     * @param string[] $css
     * @param string[] $js
     */
    public function __construct(ViewInterface $view, AuthInterface $auth, array $css, array $js)
    {
        $this->view = $view;
        $this->auth = $auth;
        $this->css = $css;
        $this->js = $js;
    }

    final public function show404(): void
    {
        $data = [
            'context' => new Context(
                $this->auth,
                '404 Страница не найдена',
                '404 Страница не найдена',
                $this->css,
                $this->js
            ),
        ];
        header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
        header('Status: 404 Not Found');
        $this->view->render('common/header.html.php', $data);
        $this->view->render('error/404.html.php', $data);
        $this->view->render('common/footer.html.php', $data);
    }
}

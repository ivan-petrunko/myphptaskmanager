<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Html\Context;

class AdminController extends DefaultController
{
    public function loginForm(array $args = []): void
    {
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/');
        }
        $data = [
            'context' => new Context(
                $this->auth,
                'Авторизация',
                'Авторизация',
                $this->css,
                $this->js
            ),
        ];
        $this->view->render('common/header.html.php', $data);
        $this->view->render('admin/login.html.php', $data);
        $this->view->render('common/footer.html.php', $data);
    }

    public function doLogin(array $args = []): void
    {
        $login = $args['request']['login'];
        $password = $args['request']['password'];

        if (!$this->auth->login($login, $password)) {
            die('User not found.');
        }

        $this->redirect('/');
    }

    public function doLogout(array $args = []): void
    {
        $this->auth->logout();
        $this->redirect('/');
    }
}

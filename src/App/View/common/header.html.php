<?php
use App\Core\Html\Context;

/** @var Context $context */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index,follow" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <title><?=$context->getTitle()?></title>
    <?php if (!empty($context->getCss())): ?>
        <?php foreach ($context->getCss() as $css): ?>
                <link rel="stylesheet" href="<?=$css?>" />
        <?php endforeach; ?>
    <?php endif; ?>
    <style type="text/css">
        body {
            padding-top: 4rem;
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="/">Задачник</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Список задач</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/add/">Создать задачу</a>
                </li>
                <li class="nav-item">
                    <?php if ($context->getAuth()->isLoggedIn()): ?>
                        <a class="nav-link" href="/logout/">Выйти</a>
                    <?php else: ?>
                        <a class="nav-link" href="/login/">Вход для администраторов</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>
</header>

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
    <div class="container">
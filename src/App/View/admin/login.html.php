<?php
use App\Core\Html\Context;

/** @var Context $context */
?>

<div class="row"><h1><?=$context->getHeading()?></h1></div>

<div class="row">
    <form action="/do_login/" method="post">
        <div class="form-label-group m-2">
            <label for="login" class="sr-only">Имя пользователя:</label>
            <input type="text" class="form-control" id="login" name="login" value="admin" placeholder="Имя пользователя" maxlength="512" required="required" />
        </div>

        <div class="form-label-group m-2">
            <label for="password" class="sr-only">Пароль:</label>
            <input type="password" class="form-control" id="password" name="password" value="123" placeholder="Пароль" maxlength="512" required="required" />
        </div>

        <div class="form-label-group m-2">
            <button type="submit" class="btn btn-primary">Войти</button>
            <a class="btn btn-secondary" href="/">Отмена</a>
        </div>
    </form>
</div>

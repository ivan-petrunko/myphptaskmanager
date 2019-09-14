<?php
use App\Core\Html\Context;

/** @var Context $context */
?>

<div class="row"><h1><?=$context->getHeading()?></h1></div>
<div class="row">
    <p>Запрошенная страница не существует.</p>
</div>
<div class="row">
    <p><a class="btn btn-secondary" href="/">Перейти на главную</a></p>
</div>

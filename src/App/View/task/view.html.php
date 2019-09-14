<?php

use App\Core\Html\Context;
use App\Core\Security\Action;
use App\Enum\TaskStatus;
use App\Model\Image;
use App\Model\Task;

/** @var Context $context */
/** @var Task $task */
/** @var Image $image */
?>

<div class="row"><h1><?=$context->getHeading()?></h1></div>

<div class="row">
    <table class="table">
        <?php if ($task->getId()): ?>
            <tr>
                <td>ID</td>
                <td><?=$task->getId()?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Имя пользователя</td>
            <td><?=$task->getUserName()?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><a href="mailto:<?=$task->getEmail()?>"><?=$task->getEmail()?></a></td>
        </tr>
        <tr>
            <td>Статус</td>
            <td>
                <?=TaskStatus::getTitle($task->getStatus())?>
                <?php if ($task->getId() && $context->getAuth()->isAllowed(Action::MARK_TASK_DONE)): ?>
                    <form action="/task_mark_done/" method="post">
                        <input type="hidden" name="task_id" value="<?=$task->getId()?>" />
                        <button type="submit" class="btn btn-success" <?=($task->getStatus() === TaskStatus::DONE ? 'disabled' : '')?>>Завершить</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Текст</td>
            <td>
                <?php if ($task->getId() && $context->getAuth()->isAllowed(Action::EDIT_TASK_TEXT)): ?>
                    <textarea class="form-control" id="taskText" rows="5" maxlength="1024" <?=($task->getStatus() === TaskStatus::DONE ? 'readonly' : '')?>><?=$task->getText()?></textarea>
                    <button type="button" id="btnTaskTextUpdate" class="btn btn-info" <?=($task->getStatus() === TaskStatus::DONE ? 'disabled' : '')?> data-task-id="<?=$task->getId()?>">Сохранить текст</button>
                    <div id="taskTextUpdateMessageContainer"></div>
                <?php else: ?>
                    <?=$task->getText()?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Изображение</td>
            <td>
                <?php if ($image !== null): ?>
                    <img class="img" src="<?=$image->getUrl()?>" />
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>

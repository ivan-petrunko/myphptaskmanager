<?php

use App\Core\Html\Context;
use App\Enum\TaskStatus;

/** @var Context $context */
/** @var App\Model\Task[] $tasks */
/** @var string $currentOrderBy */
/** @var string $currentOrderDirection */
/** @var array[] $orderByLinks */
/** @var int $currentPage */
/** @var int $pageCount */
?>

<div class="row"><h1><?=$context->getHeading()?></h1></div>

<div class="row">
    <?php if (!empty($tasks)): ?>
        <table class="table table-striped">
            <tr>
                <th><a href="<?=$orderByLinks['user_name']['url']?>">Пользователь <?=$orderByLinks['user_name']['direction']?></a></th>
                <th><a href="<?=$orderByLinks['email']['url']?>">Email <?=$orderByLinks['email']['direction']?></a></th>
                <th><a href="<?=$orderByLinks['status']['url']?>">Статус <?=$orderByLinks['status']['direction']?></a></th>
                <th>Текст</th>
                <th><a href="<?=$orderByLinks['id']['url']?>">ID <?=$orderByLinks['id']['direction']?></a></th>
            </tr>
            <?php foreach ($tasks as $task): ?>
                <tr <?=($task->getStatus() === TaskStatus::DONE ? 'style="text-decoration: line-through;"' : '')?>>
                    <td><?=$task->getUserName()?></td>
                    <td><?=$task->getEmail()?></td>
                    <td><?=TaskStatus::getTitle($task->getStatus())?></td>
                    <td><a href="/view/<?=$task->getId()?>/"><?=mb_substr($task->getText(), 0, 20)?>...</a></td>
                    <td><a href="/view/<?=$task->getId()?>/"><?=$task->getId()?></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Задач нет.</p>
    <?php endif; ?>
</div>

<?php if ($pageCount > 1): ?>
    <div class="row">
        <nav aria-label="Pagination">
            <ul class="pagination">
                <li class="page-item <?=($currentPage <= 1 ? 'disabled': '')?>">
                    <a class="page-link" href="?orderBy=<?=$currentOrderBy?>&orderDirection=<?=$currentOrderDirection?>&page=<?=($currentPage-1)?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($page = 1; $page <= $pageCount; $page++) : ?>
                    <?php if ($page === $currentPage) : ?>
                        <li class="page-item active" aria-current="page">
                            <a class="page-link" href="?orderBy=<?=$currentOrderBy?>&orderDirection=<?=$currentOrderDirection?>&page=<?=$page?>"><?=$page?> <span class="sr-only">(current)</span></a>
                        </li>
                    <?php else: ?>
                        <li class="page-item"><a class="page-link" href="?orderBy=<?=$currentOrderBy?>&orderDirection=<?=$currentOrderDirection?>&page=<?=$page?>"><?=$page?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li class="page-item <?=($currentPage >= $pageCount ? 'disabled': '')?>">
                    <a class="page-link" href="?orderBy=<?=$currentOrderBy?>&orderDirection=<?=$currentOrderDirection?>&page=<?=($currentPage+1)?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
</div>

<?php endif; ?>

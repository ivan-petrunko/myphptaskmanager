<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Html\Context;
use App\Core\Security\AuthInterface;
use App\Enum\TaskStatus;
use App\Model\Image;
use App\Model\Task;
use App\Model\TaskImage;
use App\View\ViewInterface;

class TaskController extends DefaultController
{
    private const PER_PAGE = 3;

    private const ORDER_BY_COOKIE = 'order_by';

    private const ORDER_DIRECTION_COOKIE = 'order_direction';

    private const ORDER_COOKIE_LIFETIME = 31536000;

    /**
     * @var Task
     */
    private $taskModel;

    /**
     * @var TaskImage
     */
    private $taskImageModel;

    /**
     * @var Image
     */
    private $imageModel;

    /**
     * TaskController constructor.
     * @param ViewInterface $view
     * @param AuthInterface $auth
     * @param array $css
     * @param array $js
     * @param Task $taskModel
     * @param TaskImage $taskImageModel
     * @param Image $imageModel
     */
    public function __construct(
        ViewInterface $view,
        AuthInterface $auth,
        array $css,
        array $js,
        Task $taskModel,
        TaskImage $taskImageModel,
        Image $imageModel
    )     {
        parent::__construct($view, $auth, $css, $js);
        $this->taskModel = $taskModel;
        $this->taskImageModel = $taskImageModel;
        $this->imageModel = $imageModel;
    }

    public function listTasks(array $args = []): void
    {
        // deal with ordering
        $currentOrderBy = 'id';
        $currentOrderDirection = 'desc';

        $userOrderBy = $args['request']['orderBy'] ?? $_COOKIE[self::ORDER_BY_COOKIE] ?? $currentOrderBy;
        $userOrderDirection = $args['request']['orderDirection'] ?? $_COOKIE[self::ORDER_DIRECTION_COOKIE] ?? $currentOrderDirection;

        if (in_array($userOrderBy, $this->taskModel->getOrderByFields(), true)) {
            $currentOrderBy = $userOrderBy;
        }
        if (in_array($userOrderDirection, ['asc', 'desc'], true)) {
            $currentOrderDirection = $userOrderDirection;
        }
        $orderBy = ["{$currentOrderBy} {$currentOrderDirection}"];

        $orderByFields = $this->taskModel->getOrderByFields();
        $orderByLinks = [];
        foreach ($orderByFields as $field) {
            $orderByLinks[$field]['url'] = "?orderBy={$field}&orderDirection=" . ($currentOrderBy === $field && $currentOrderDirection === 'asc' ? 'desc' : 'asc');
            $orderByLinks[$field]['direction'] = $currentOrderBy === $field
                ? ($currentOrderDirection === 'asc' ? '&uarr;' : '&darr;')
                : '';
        }
        if (!isset($_COOKIE[self::ORDER_BY_COOKIE])
            || $_COOKIE[self::ORDER_BY_COOKIE] !== $currentOrderBy) {
            setcookie(self::ORDER_BY_COOKIE, $currentOrderBy, time() + self::ORDER_COOKIE_LIFETIME, '/');
        }
        if (!isset($_COOKIE[self::ORDER_DIRECTION_COOKIE])
            || $_COOKIE[self::ORDER_DIRECTION_COOKIE] !== $currentOrderDirection) {
            setcookie(self::ORDER_DIRECTION_COOKIE, $currentOrderDirection, time() + self::ORDER_COOKIE_LIFETIME, '/');
        }

        // deal with pagination
        $currentPage = (int)($args['request']['page'] ?? 1);
        $offset = ($currentPage > 0 ? $currentPage - 1 : $currentPage) * self::PER_PAGE;
        $limit = self::PER_PAGE;

        $totalRows = $this->taskModel->getCountByConditions();
        $pageCount = ceil($totalRows / self::PER_PAGE);


        // query data
        $tasks = $this->taskModel->getByConditions([], [], $orderBy, $limit, $offset);

        $data = [
            'context' => new Context(
                $this->auth,
                'Задачи' . ($pageCount > 1 ? ", страница {$currentPage} / {$pageCount}" : ''),
                'Задачи' . ($pageCount > 1 ? ", страница {$currentPage} / {$pageCount}" : ''),
                $this->css,
                $this->js
            ),
            'tasks' => $tasks,
            'currentOrderBy' => $currentOrderBy,
            'currentOrderDirection' => $currentOrderDirection,
            'orderByLinks' => $orderByLinks,
            'currentPage' => $currentPage,
            'pageCount' => $pageCount,
        ];
        $this->view->render('common/header.html.php', $data);
        $this->view->render('task/list.html.php', $data);
        $this->view->render('common/footer.html.php', $data);
    }

    public function viewTask(array $args = []): void
    {
        $taskId = (int)$args['matches']['taskId'];

        $task = $this->taskModel->getById($taskId);
        if ($task === null) {
            $this->show404();
            return;
        }

        $image = null;
        /** @var TaskImage[] $taskImages */
        $taskImages = $this->taskImageModel->getByConditions(["task_id='{$taskId}'"], [], [], 1);
        if (!empty($taskImages)) {
            $taskImage = reset($taskImages);
            $image = $this->imageModel->getById($taskImage->getImageId());
        }

        $data = [
            'context' => new Context(
                $this->auth,
                "Задача #{$taskId}",
                "Задача #{$taskId}",
                $this->css,
                $this->js
            ),
            'task' => $task,
            'image' => $image,
            'auth' => $this->auth
        ];
        $this->view->render('common/header.html.php', $data);
        $this->view->render('task/view.html.php', $data);
        $this->view->render('common/footer.html.php', $data);
    }

    public function addTask(array $args = []): void
    {
        $data = [
            'context' => new Context(
                $this->auth,
                'Создание новой задачи',
                'Создание новой задачи',
                $this->css,
                $this->js
            ),
        ];
        $this->view->render('common/header.html.php', $data);
        $this->view->render('task/add.html.php', $data);
        $this->view->render('common/footer.html.php', $data);
    }

    public function insertTask(array $args = []): void
    {
        $userName = $this->cleanInput($args['request']['user_name'], 512);
        $email = $this->cleanInput($args['request']['email'], 512);
        $text = $this->cleanInput($args['request']['text'], 1024);
        $imageId = (int)$args['request']['image_id'];

        $newTask = (new Task($this->taskModel->getPdo()))
            ->setUserName($userName)
            ->setEmail($email)
            ->setText($text)
        ;
        $newTask->save();

        if ($imageId > 0) {
            $newTaskImage = (new TaskImage($this->taskModel->getPdo()))
                ->setTaskId($newTask->getId())
                ->setImageId($imageId);
            $newTaskImage->save();
        }

        if ($newTask->getId()) {
            $this->redirect("/view/{$newTask->getId()}/");
        }

        die('Error saving task. No task ID.');
    }

    public function taskMarkDone(array $args = []): void
    {
        $taskId = (int)$args['request']['task_id'];
        if (!$taskId) {
            die('Invalid taskId.');
        }
        /** @var Task $task */
        $task = $this->taskModel->getById($taskId);
        if ($task === null) {
            die("Task {$taskId} not found.");
        }
        $task->setStatus(TaskStatus::DONE);
        $task->save();
        $this->redirect("/view/{$taskId}/");
    }
}

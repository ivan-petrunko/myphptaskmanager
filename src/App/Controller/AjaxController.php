<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Faker\FakerInterface;
use App\Core\Html\Context;
use App\Core\Security\Action;
use App\Core\Security\AuthInterface;
use App\Model\Image;
use App\Model\Task;
use App\View\ViewInterface;

class AjaxController extends AbstractController
{
    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var FakerInterface
     */
    private $faker;

    /**
     * @var Task
     */
    private $taskModel;

    /**
     * @var Image
     */
    private $image;

    /**
     * AjaxController constructor.
     * @param ViewInterface $view
     * @param AuthInterface $auth
     * @param FakerInterface $faker
     * @param Task $taskModel
     * @param Image $image
     */
    public function __construct(
        ViewInterface $view,
        AuthInterface $auth,
        FakerInterface $faker,
        Task $taskModel,
        Image $image
    ) {
        $this->view = $view;
        $this->auth = $auth;
        $this->faker = $faker;
        $this->taskModel = $taskModel;
        $this->image = $image;
    }

    public function taskTextUpdate(array $args = []): void
    {
        header('Content-Type: application/json');

        try {
            if (!$this->auth->isAllowed(Action::EDIT_TASK_TEXT)) {
                throw new \Exception('Forbidden.');
            }
            $taskId = (int)$args['request']['id'];
            $taskText = $this->cleanInput($args['request']['text'], 1024);
            if (!$taskId) {
                throw new \Exception('Invalid taskId.');
            }
            if (empty($taskText)) {
                throw new \Exception('Invalid text.');
            }
            /** @var Task $task */
            $task = $this->taskModel->getById($taskId);
            if ($task === null) {
                throw new \Exception("Task {$taskId} not found.");
            }
            $task->setText($taskText);
            $task->save();

            $response = ['success' => true, 'message' => 'Сохранено.',];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage(),];
        }
        die(json_encode($response, JSON_FORCE_OBJECT));
    }

    public function uploadImage(array $args = []): void
    {
        try {
            $file = $args['files']['file'] ?? null;

            if (empty($file)) {
                throw new \Exception('Не передан файл.');
            }
            if ($file['size'] > 10485760) {
                throw new \Exception('Превышен максимальный размер файла 10 МБ.');
            }
            if (!in_array($file['type'], ['image/jpeg', 'image/jpg', 'image/png', 'image/gif',], true)) {
                throw new \Exception('Неподдерживаемый формат изображения (допустимы только JPG, PNG, GIF).');
            }
            $extension = strtolower(end(explode('.', $file['name'])));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                throw new \Exception('Неверное расширение файла (допустимо только jpg, jpeg, png, gif).');
            }
            $hash = md5_file($file['tmp_name']);

            $maxWidth = 320;
            $maxHeight = 240;
            $quality = 80;

            $imageSize = getimagesize($file['tmp_name']);
            $originalWidth = (int)$imageSize[0];
            $originalHeight = (int)$imageSize[1];
            if ($originalWidth < $maxWidth) {
                throw new \Exception('Ширина картинки слишком маленькая.');
            }
            if ($originalHeight < $maxHeight) {
                throw new \Exception('Высота картинки слишком маленькая.');
            }

            $widthRatio = $maxWidth / $originalWidth;
            $heightRatio = $maxHeight / $originalHeight;

            $ratio = min($widthRatio, $heightRatio);

            $newWidth  = (int)($originalWidth  * $ratio);
            $newHeight = (int)($originalHeight * $ratio);

            $imageModel = (new Image($this->image->getPdo()))
                ->setWidth($newWidth)
                ->setHeight($newHeight)
                ->setExtension($extension)
                ->setHash($hash)
            ;
            $imageModel->save();

            $originalImage = imagecreatefromstring(file_get_contents($file['tmp_name']));

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($extension === 'gif' || $extension === 'png') {
                imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
            }
            imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            $imageDir = __DIR__ . '/../../../public/uploads';
            $newImagePath = $imageDir . '/' . $imageModel->getFileName();
            if ($extension === 'jpg' || $extension === 'jpeg') {
                imagejpeg($newImage, $newImagePath, $quality);
            } elseif ($extension === 'png') {
                imagepng($newImage, $newImagePath);
            } elseif ($extension === 'gif') {
                imagegif($newImage, $newImagePath);
            }

            imagedestroy($originalImage);
            imagedestroy($newImage);
            @unlink($file['tmp_name']);

            $response = [
                'success' => true,
                'message' => 'Файл загружен.',
                'imageId' => $imageModel->getId(),
                'imageUrl' => $imageModel->getUrl(),
            ];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage(),];
        }
        die(json_encode($response, JSON_FORCE_OBJECT));
    }

    public function taskPreview(array $args = []): void
    {
        header('Content-Type: application/json');

        $userName = $this->cleanInput($args['request']['user_name'], 512);
        $email = $this->cleanInput($args['request']['email'], 512);
        $text = $this->cleanInput($args['request']['text'], 1024);
        $imageId = (int)$args['request']['image_id'];

        $task = (new Task($this->taskModel->getPdo()))
            ->setUserName($userName)
            ->setEmail($email)
            ->setText($text);
        $image = null;
        if ($imageId > 0) {
            $image = $this->image->getById($imageId);
        }

        $data = [
            'context' => new Context(
                $this->auth,
                '',
                ''
            ),
            'task' => $task,
            'image' => $image,
        ];

        $html = $this->view->fetch('task/view.html.php', $data);

        $response = ['success' => true, 'message' => $html,];

        die(json_encode($response, JSON_FORCE_OBJECT));
    }

    public function taskLoremIpsum(array $args = []): void
    {
        header('Content-Type: application/json');

        $response = [
            'success' => true,
            'user_name' => $this->faker->getUserName(),
            'email' => $this->faker->getEmail(),
            'text' => $this->faker->getText(),
        ];

        die(json_encode($response, JSON_FORCE_OBJECT));
    }
}

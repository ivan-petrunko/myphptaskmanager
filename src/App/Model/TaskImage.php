<?php

declare(strict_types=1);

namespace App\Model;

class TaskImage extends AbstractModel
{
    /** @var int */
    private $taskId;

    /** @var int */
    private $imageId;

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     * @return TaskImage
     */
    public function setTaskId(int $taskId): TaskImage
    {
        $this->taskId = $taskId;
        return $this;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @param int $imageId
     * @return TaskImage
     */
    public function setImageId(int $imageId): TaskImage
    {
        $this->imageId = $imageId;
        return $this;
    }

    protected function getTableName(): string
    {
        return 'task_image';
    }

    protected function getFieldsForInsert(): string
    {
        return 'task_id, image_id';
    }

    protected function getBindAliasesForInsert(): string
    {
        return ':task_id, :image_id';
    }

    protected function getBindMapForInsert(): array
    {
        return [
            ':task_id' => $this->taskId,
            ':image_id' => $this->imageId,
        ];
    }

    protected function toUpdateString(): string
    {
        $updateArray = [
            'task_id' => $this->taskId,
            'image_id' => $this->imageId,
        ];
        $this->convertKeyValueArrayToAssignmentArray($updateArray);
        return implode(', ', $updateArray);
    }

    public static function fromArray(array $array, \PDO $pdo): AbstractModel
    {
        return (new static($pdo))
            ->setTaskId((int)$array['task_id'])
            ->setImageId((int)$array['image_id'])
            ->setId(!empty($array['id']) ? (int)$array['id'] : null)
            ;
    }
}

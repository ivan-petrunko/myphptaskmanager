<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\TaskStatus;

class Task extends AbstractModel
{
    /** @var string */
    private $userName;

    /** @var string */
    private $email;

    /** @var string */
    private $text;

    /** @var int */
    private $status = TaskStatus::NEW;

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return Task
     */
    public function setUserName(string $userName): Task
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Task
     */
    public function setEmail(string $email): Task
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Task
     */
    public function setText(string $text): Task
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Task
     */
    public function setStatus(int $status): Task
    {
        $this->status = $status;
        return $this;
    }

    public function getOrderByFields(): array
    {
        return [
            'id',
            'user_name',
            'email',
            'status',
        ];
    }

    protected function getTableName(): string
    {
        return 'task';
    }

    protected function getFieldsForInsert(): string
    {
        return 'user_name, email, text, status';
    }

    protected function getBindAliasesForInsert(): string
    {
        return ':user_name, :email, :text, :status';
    }

    protected function getBindMapForInsert(): array
    {
        return [
            ':user_name' => $this->userName,
            ':email' => $this->email,
            ':text' => $this->text,
            ':status' => $this->status,
        ];
    }

    protected function toUpdateString(): string
    {
        $updateArray = [
            'user_name' => $this->userName,
            'email' => $this->email,
            'text' => $this->text,
            'status' => $this->status,
        ];
        $updateArray = $this->convertKeyValueArrayToAssignmentArray($updateArray);
        return implode(', ', $updateArray);
    }

    public static function fromArray(array $array, \PDO $pdo): AbstractModel
    {
        return (new static($pdo))
            ->setUserName($array['user_name'])
            ->setEmail($array['email'])
            ->setText($array['text'])
            ->setStatus((int)$array['status'])
            ->setId(!empty($array['id']) ? (int)$array['id'] : null)
            ;
    }
}

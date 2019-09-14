<?php

declare(strict_types=1);

namespace App\Model;

abstract class AbstractModel
{
    /** @var \PDO */
    private $pdo;

    /** @var int|null */
    protected $id;

    /**
     * AbstractModel constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function save(): void
    {
        if ($this->{$this->getPkField()} === null) {
            $sql = "insert into {$this->getTableName()} ({$this->getFieldsForInsert()}) 
            values ({$this->getBindAliasesForInsert()})";
            $stmt = $this->pdo->prepare($sql);
            $bindMap = $this->getBindMapForInsert();
            foreach ($bindMap as $parameter => $value) {
                $stmt->bindValue($parameter, $value);
            }
            $stmt->execute();
            $lastInsertId = (int)$this->pdo->lastInsertId();
            $this->{$this->getPkField()} = $lastInsertId;
        } else {
            $sql = "update {$this->getTableName()}
            set {$this->toUpdateString()}
            where {$this->getPkField()}=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $this->{$this->getPkField()});
            $stmt->execute();
        }
    }

    public function delete(): void
    {
        $sql = "delete from {$this->getTableName()} where {$this->getPkField()}=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $this->{$this->getPkField()});
        $stmt->execute();
    }

    public function getById(int $id): ?AbstractModel
    {
        $models = $this->getByConditions(["id={$id}"], [], [], 1);
        if (empty($models)) {
            return null;
        }
        $model = reset($models);
        return $model;
    }

    public function getByConditions(
        array $conditions = [],
        array $groupBy = [],
        array $orderBy = ['id'],
        int $limit = 1000,
        int $offset = 0
    ): array
    {
        $sql = "select * from {$this->getTableName()}";
        if (!empty($conditions)) {
            $conditionsStr = implode(' AND ', $conditions);
            $sql .= " where {$conditionsStr}";
        }
        if (!empty($groupBy)) {
            $groupByStr = implode(', ', $groupBy);
            $sql .= " group by {$groupByStr}";
        }
        if (!empty($orderBy)) {
            $orderByStr = implode(', ', $orderBy);
            $sql .= " order by {$orderByStr}";
        }
        if ($limit > 0) {
            $sql .= " limit {$limit}";
        }
        if ($offset > 0) {
            $sql .= " offset {$offset}";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($rows)) {
            return [];
        }
        $result = [];
        foreach ($rows as $row) {
            $result[] = static::fromArray($row, $this->pdo);
        }
        return $result;
    }

    public function getCountByConditions(array $conditions = []): int
    {
        $sql = "select count(*) cnt from {$this->getTableName()}";
        if (!empty($conditions)) {
            $conditionsStr = implode(' AND ', $conditions);
            $sql .= " where {$conditionsStr}";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (empty($row)) {
            return 0;
        }
        return (int)$row['cnt'];
    }

    public function getOrderByFields(): array
    {
        return ['id',];
    }

    protected function getPkField(): string
    {
        return 'id';
    }

    abstract protected function getTableName(): string;

    abstract protected function getFieldsForInsert(): string;

    abstract protected function getBindAliasesForInsert(): string;

    abstract protected function getBindMapForInsert(): array;

    abstract protected function toUpdateString(): string;

    abstract public static function fromArray(array $array, \PDO $pdo): self;

    final protected function convertKeyValueArrayToAssignmentArray(array $array): array
    {
        array_walk($array, function(&$value, $key){
            $value = "{$key}='{$value}'";
        });
        return $array;
    }
}

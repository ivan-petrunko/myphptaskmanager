<?php

declare(strict_types=1);

namespace App\Model;

class Image extends AbstractModel
{
    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var string */
    private $extension;

    /** @var string */
    private $hash;

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     * @return Image
     */
    public function setWidth(int $width): Image
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     * @return Image
     */
    public function setHeight(int $height): Image
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return Image
     */
    public function setExtension(string $extension): Image
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return Image
     */
    public function setHash(string $hash): Image
    {
        $this->hash = $hash;
        return $this;
    }

    protected function getTableName(): string
    {
        return 'image';
    }

    protected function getFieldsForInsert(): string
    {
        return 'width, height, extension, hash';
    }

    protected function getBindAliasesForInsert(): string
    {
        return ':width, :height, :extension, :hash';
    }

    protected function getBindMapForInsert(): array
    {
        return [
            ':width' => $this->width,
            ':height' => $this->height,
            ':extension' => $this->extension,
            ':hash' => $this->hash,
        ];
    }

    protected function toUpdateString(): string
    {
        $updateArray = [
            'width' => $this->width,
            'height' => $this->height,
            'extension' => $this->extension,
            'hash' => $this->hash,
        ];
        $this->convertKeyValueArrayToAssignmentArray($updateArray);
        return implode(', ', $updateArray);
    }

    public static function fromArray(array $array, \PDO $pdo): AbstractModel
    {
        return (new static($pdo))
            ->setWidth((int)$array['width'])
            ->setHeight((int)$array['height'])
            ->setExtension($array['extension'])
            ->setHash($array['hash'])
            ->setId(!empty($array['id']) ? (int)$array['id'] : null)
            ;
    }

    public function getFileName(): string
    {
        return "{$this->getId()}.{$this->getExtension()}";
    }

    public function getUrl(): string
    {
        return "/uploads/{$this->getFileName()}";
    }
}

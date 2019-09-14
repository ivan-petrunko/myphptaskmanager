<?php

declare(strict_types=1);

namespace App\View;

interface ViewInterface
{
    public function render(string $templateFilePath, array $data = []): void;
    public function fetch(string $templateFilePath, array $data = []): string;
}

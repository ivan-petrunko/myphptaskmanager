<?php

declare(strict_types=1);

namespace App\View;

class View implements ViewInterface
{
    public function render(string $templateFilePath, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        $fullTemplateFilePath = __DIR__  . DIRECTORY_SEPARATOR . $templateFilePath;
        if (is_readable($fullTemplateFilePath)) {
            require $fullTemplateFilePath;
        } else {
            throw new \RuntimeException("View {$fullTemplateFilePath} not found.");
        }
    }

    public function fetch(string $templateFilePath, array $data = []): string
    {
        ob_start();
        $this->render($templateFilePath, $data);
        $result = ob_get_clean();
        return $result;
    }
}

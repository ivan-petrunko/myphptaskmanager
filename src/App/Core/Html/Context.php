<?php

declare(strict_types=1);

namespace App\Core\Html;

use App\Core\Security\AuthInterface;

class Context
{
    /** @var AuthInterface */
    private $auth;

    /** @var string */
    private $title;

    /** @var string */
    private $heading;

    /** @var string[] */
    private $css;

    /** @var string[] */
    private $js;

    /** @var bool */
    private $isLocal;

    /**
     * Context constructor.
     * @param AuthInterface $auth
     * @param string $title
     * @param string $heading
     * @param string[] $css
     * @param string[] $js
     */
    public function __construct(AuthInterface $auth, string $title, string $heading, array $css = [], array $js = [])
    {
        $this->auth = $auth;
        $this->title = $title;
        $this->heading = $heading;
        $this->css = $css;
        $this->js = $js;
        $this->isLocal = isset($_ENV['docker'])
            || !isset($_SERVER['REMOTE_ADDR'])
            || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }

    /**
     * @return AuthInterface
     */
    public function getAuth(): AuthInterface
    {
        return $this->auth;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHeading(): string
    {
        return $this->heading;
    }

    /**
     * @return string[]
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * @return string[]
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->isLocal;
    }
}

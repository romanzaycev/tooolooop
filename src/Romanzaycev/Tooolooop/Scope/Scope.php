<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Scope;

use Romanzaycev\Tooolooop\Template\TemplateInterface;

/**
 * Class Scope
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Scope
 */
class Scope implements ScopeInterface
{

    /**
     * @var TemplateInterface
     */
    private $template;

    /**
     * @var \Closure
     */
    private $proxy;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Scope constructor.
     *
     * @param TemplateInterface $template
     * @param array $data
     */
    public function __construct(TemplateInterface $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;

        $this->proxy = function ($method, $arguments) use ($template) {
            return call_user_func_array([$template, $method], $arguments);
        };
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function block(string $name = 'content')
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $name
     */
    public function start(string $name)
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * @return void
     */
    public function end()
    {
        $this->proxyCall(__FUNCTION__);
    }

    /**
     * Escape variable and apply filters.
     *
     * @param mixed $variable
     * @param array $filters
     * @return mixed|string
     */
    public function e($variable, array $filters = [])
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * Set parent layout.
     *
     * @param string $layout
     */
    public function extend(string $layout)
    {
        $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * Load partial template.
     *
     * @param string $template
     * @param array $data
     * @return mixed
     */
    public function load(string $template, array $data = [])
    {
        return $this->proxyCall(__FUNCTION__, func_get_args());
    }

    /**
     * Perform template.
     *
     * @param string $path
     */
    public function perform(string $path)
    {
        $context = function () use ($path) {
            extract($this->data);

            include $path;
        };
        $context();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    private function proxyCall(string $method, array $arguments = [])
    {
        return $this->proxy->call($this->template, $method, $arguments);
    }
}
<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
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
    protected $data = [];

    /**
     * Set template.
     *
     * @param TemplateInterface $template
     */
    public function setTemplate(TemplateInterface $template)
    {
        $this->template = $template;
    }

    /**
     * Set template data.
     *
     * @param array $data
     */
    public function setData(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function block(string $name = 'content')
    {
        return $this->proxyCall(__FUNCTION__, \func_get_args());
    }

    /**
     * @param string $name
     */
    public function start(string $name)
    {
        $this->proxyCall(__FUNCTION__, \func_get_args());
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
        return $this->proxyCall(__FUNCTION__, \func_get_args());
    }

    /**
     * Set parent layout.
     *
     * @param string $layout
     * @param array $data
     */
    public function extend(string $layout, array $data = [])
    {
        $this->proxyCall(__FUNCTION__, \func_get_args());
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
        return $this->proxyCall(__FUNCTION__, \func_get_args());
    }

    /**
     * Perform template.
     *
     * @param string $path
     */
    public function perform(string $path)
    {
        $context = $this->getContext();
        $context($path);
    }

    /**
     * Get runtime context
     *
     * @return \Closure<string>
     */
    protected function getContext()
    {
        return function ($__0path__) {
            \extract($this->data);

            /** @noinspection PhpIncludeInspection */
            include $__0path__;
        };
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    private function proxyCall(string $method, array $arguments = [])
    {
        return $this->ensureProxy()->call($this->template, $method, $arguments);
    }

    /**
     * @return \Closure
     */
    private function ensureProxy()
    {
        $template = $this->template;
        if (\is_null($this->proxy)) {
            $this->proxy = function ($method, $arguments) use ($template) {
                return \call_user_func_array([$template, $method], $arguments);
            };
        }

        return $this->proxy;
    }
}

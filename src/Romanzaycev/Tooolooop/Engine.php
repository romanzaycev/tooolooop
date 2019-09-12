<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop;

use Romanzaycev\Tooolooop\Filter\Escape;
use Romanzaycev\Tooolooop\Filter\Filter;
use Romanzaycev\Tooolooop\Filter\Replace;
use Romanzaycev\Tooolooop\Scope\Scope;
use Romanzaycev\Tooolooop\Template\Template;
use Romanzaycev\Tooolooop\Template\TemplateInterface;
use Romanzaycev\Tooolooop\Filter\FilterInterface;
use Romanzaycev\Tooolooop\Exceptions\FilterNotFoundException;

/**
 * Class Engine
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop
 */
class Engine implements EngineInterface
{

    const VERSION = '0.4.1';

    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var array
     */
    private $filters = [
        'escape' => Escape::class,
        'replace' => Replace::class
    ];

    /**
     * @var string
     */
    private $scopeClass = Scope::class;

    /**
     * Engine constructor.
     *
     * @param string $directory templates directory
     * @param string $extension template file extension (default: 'php')
     */
    public function __construct(string $directory = '', string $extension = 'php')
    {
        $this->setDirectory($directory);
        $this->setExtension($extension);
    }

    /**
     * Make template instance.
     *
     * @param string $template template name
     * @return TemplateInterface
     */
    public function make(string $template): TemplateInterface
    {
        return new Template($this, $template);
    }

    /**
     * Get templates directory.
     *
     * @return string templates directory
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Set templates directory.
     *
     * @param string $directory templates directory
     */
    public function setDirectory(string $directory)
    {
        $directory = \str_replace('\\', DIRECTORY_SEPARATOR, $directory);
        $this->directory = DIRECTORY_SEPARATOR . trim($directory, DIRECTORY_SEPARATOR);
    }

    /**
     * Get template file extension.
     *
     * @return string template file extension
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Set template file extension.
     *
     * @param string $extension template file extension
     */
    public function setExtension(string $extension)
    {
        if (\substr($extension, 0, 1) === '.') {
            $extension = \substr($extension, 1);
        }

        $this->extension = $extension;
    }

    /**
     * Add filter.
     *
     * @param FilterInterface|string $filter
     * @param callable|null $callback
     */
    public function addFilter($filter, $callback = null)
    {
        if ($filter instanceof FilterInterface) {
            $this->filters[$filter->getName()] = $filter->getFunction();
        } elseif (\is_string($filter) && \is_callable($callback)) {
            $this->filters[$filter] = $callback;
        } else {
            throw new \InvalidArgumentException(
                \sprintf(
                    "Unexpected arguments types: %s and %s, expected FilterInterface or string; callable or null",
                    \gettype($filter),
                    \gettype($callback)
                )
            );
        }
    }

    /**
     * Get filter function.
     *
     * @param string $filter filter name
     * @return callable filter function
     * @throws FilterNotFoundException
     */
    public function getFilterFunction(string $filter): callable
    {
        if (!\array_key_exists($filter, $this->filters)) {
            throw new FilterNotFoundException(\sprintf("Not found filter with name \"%s\"", $filter));
        }

        if (\is_string($this->filters[$filter])) {
            /**
             * @var Filter $filterInstance
             */
            $filterInstance = new $this->filters[$filter];
            $this->filters[$filter] = $filterInstance->getFunction();
        }

        return $this->filters[$filter];
    }

    /**
     * Set default template scope class name.
     *
     * @param string $class class name
     */
    public function setScopeClass(string $class = Scope::class)
    {
        $this->scopeClass = $class;
    }

    /**
     * Get default scope class name.
     *
     * @return string class name
     */
    public function getScopeClass(): string
    {
        return $this->scopeClass;
    }

}

<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop;

use Psr\Container\ContainerInterface;
use Romanzaycev\Tooolooop\Scope\Scope;
use Romanzaycev\Tooolooop\Scope\ScopeInterface;
use Romanzaycev\Tooolooop\Template\TemplateInterface;
use Romanzaycev\Tooolooop\Filter\FilterInterface;

/**
 * Interface EngineInterface
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop
 */
interface EngineInterface
{

    /**
     * Makes template instance
     *
     * @param string $template template name
     * @return TemplateInterface
     */
    public function make(string $template): TemplateInterface;

    /**
     * Get templates directory.
     *
     * @return string
     */
    public function getDirectory(): string;

    /**
     * Set templates directory.
     *
     * @param string $directory
     */
    public function setDirectory(string $directory);

    /**
     * Get template file extension.
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Set template file extension.
     *
     * @param string $extension
     */
    public function setExtension(string $extension);

    /**
     * Add engine filter
     *
     * @param string|FilterInterface $filter
     * @param callable|null $callback
     */
    public function addFilter($filter, $callback);

    /**
     * Get filter function.
     *
     * @param string $name filter name
     * @return callable filter function
     */
    public function getFilterFunction(string $name): callable;

    /**
     * Set default template scope class name.
     *
     * @param string $class scope class name
     */
    public function setScopeClass(string $class = Scope::class);

    /**
     * Get default template scope class name.
     *
     * @return string class name
     */
    public function getScopeClass(): string;

    /**
     * Returns Scope instance
     *
     * @return ScopeInterface
     */
    public function getScope(): ScopeInterface;

    /**
     * Inject PSR-11 container instance
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container): void;

}
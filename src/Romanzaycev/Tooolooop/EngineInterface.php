<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop;

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

}
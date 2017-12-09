<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Scope;

/**
 * Interface ScopeInterface
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Scope
 */
interface ScopeInterface
{

    /**
     * @param string $name
     * @return mixed
     */
    public function block(string $name = 'content');

    /**
     * @param string $name
     * @return void
     */
    public function start(string $name);

    /**
     * @return void
     */
    public function end();

    /**
     * @param $variable
     * @param array $filters
     * @return mixed|string
     */
    public function e($variable, array $filters = []);

    /**
     * @param string $layout
     * @param array $data
     * @return void
     */
    public function extend(string $layout, array $data = []);

    /**
     * @param string $template
     * @param array $data
     * @return string
     */
    public function load(string $template, array $data = []);

}
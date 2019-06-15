<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Filter;

/**
 * Interface FilterInterface
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Filter
 */
interface FilterInterface
{

    /**
     * Get filter name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get filter function.
     *
     * @return callable
     */
    public function getFunction(): callable;

}
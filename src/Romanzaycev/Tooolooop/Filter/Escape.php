<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Filter;

/**
 * Class Escape
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Filter
 */
class Escape extends Filter
{

    /**
     * @inheritdoc
     */
    protected function defineName(): string
    {
        return "escape";
    }

    /**
     * @inheritdoc
     */
    protected function defineFunction(): callable
    {
        return function ($value = '') {
            return \htmlspecialchars(
                $value,
                \ENT_QUOTES,
                'UTF-8'
            );
        };
    }

}
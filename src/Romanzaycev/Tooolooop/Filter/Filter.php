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
 * Class Filter
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Filter
 */
abstract class Filter implements FilterInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $function;

    /**
     * Get filter name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (is_null($this->name)) {
            $this->name = $this->defineName();
        }

        return $this->name;
    }

    /**
     * Get filter function.
     *
     * @return callable
     */
    public function getFunction(): callable
    {
        if (is_null($this->function)) {
            $this->function = $this->defineFunction();
        }

        return $this->function;
    }

    /**
     * Define filter name.
     *
     * @return string filter name
     */
    abstract protected function defineName(): string;

    /**
     * Define filter function.
     *
     * @return callable filter function
     */
    abstract protected function defineFunction(): callable;

}
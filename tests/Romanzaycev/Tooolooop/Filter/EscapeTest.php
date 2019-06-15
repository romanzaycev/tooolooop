<?php declare(strict_types = 1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Tests;

use PHPUnit\Framework\TestCase;
use Romanzaycev\Tooolooop\Filter\Escape;
use Romanzaycev\Tooolooop\Filter\Filter;

/**
 * Class EscapeTest
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Tests
 */
class EscapeTest extends TestCase
{

    public function testConstruct()
    {
        $filter = new Escape();
        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function testGetName()
    {
        $filter = new Escape();
        $this->assertEquals('escape', $filter->getName());
    }

    public function testFunction()
    {
        $filter = new Escape();
        $this->assertEquals('&gt;', $filter->getFunction()('>'));
    }
}
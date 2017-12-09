<?php declare(strict_types = 1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Tests;

use PHPUnit\Framework\TestCase;
use Romanzaycev\Tooolooop\Filter\Filter;
use Romanzaycev\Tooolooop\Filter\Replace;

/**
 * Class ReplaceTest
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Tests
 */
class ReplaceTest extends TestCase
{

    public function testConstruct()
    {
        $filter = new Replace();
        $this->assertInstanceOf(Filter::class, $filter);
    }

    public function testGetName()
    {
        $filter = new Replace();
        $this->assertEquals('replace', $filter->getName());
    }

    public function testFunction()
    {
        $filter = new Replace();
        $this->assertEquals('b', $filter->getFunction()('a', 'a', 'b'));
    }

}
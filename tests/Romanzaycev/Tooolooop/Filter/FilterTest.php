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

/**
 * Class FilterTest
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Tests
 */
class FilterTest extends TestCase
{

    public function testConstruct()
    {
        $filterFixture = new class extends Filter {
            protected function defineName(): string
            {
                return 'foo';
            }

            protected function defineFunction(): callable
            {
                return function () {
                    return 'fooval';
                };
            }
        };

        $this->assertEquals('foo', $filterFixture->getName());
        $this->assertEquals('fooval', $filterFixture->getFunction()());
    }
}
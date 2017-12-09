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
use Romanzaycev\Tooolooop\Engine;
use Romanzaycev\Tooolooop\EngineInterface;
use Romanzaycev\Tooolooop\Exceptions\FilterNotFoundException;
use Romanzaycev\Tooolooop\Filter\FilterInterface;
use Romanzaycev\Tooolooop\Template\TemplateInterface;

/**
 * Class EngineTest
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Tests
 */
class EngineTest extends TestCase
{

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testConstruct()
    {
        $engine = new Engine();
        $this->assertInstanceOf(EngineInterface::class, $engine);
    }

    public function testConstructWithArgs()
    {
        $engine = new Engine(TOOOLOOOP_TEST_DIR . '/fixtures', 'php');
        $this->assertEquals(TOOOLOOOP_TEST_DIR . '/fixtures', $engine->getDirectory());
        $this->assertEquals('php', $engine->getExtension());
    }

    public function testMakeTemplate()
    {
        $engine = new Engine();
        $this->assertInstanceOf(TemplateInterface::class, $engine->make('foo'));
    }

    public function testSetDirectory()
    {
        $engine = new Engine();
        $engine->setDirectory(TOOOLOOOP_TEST_DIR . '/fixtures');
        $this->assertEquals(TOOOLOOOP_TEST_DIR . '/fixtures', $engine->getDirectory());
    }

    public function testSetExtension()
    {
        $engine = new Engine();
        $engine->setExtension('.foo');
        $this->assertEquals('foo', $engine->getExtension());
    }

    public function testAddFilterWithClosure()
    {
        $engine = new Engine();
        $closure = function () {
            return 'foo';
        };
        $engine->addFilter('foo', $closure);
        $this->assertEquals($closure(), $engine->getFilterFunction('foo')());
    }

    public function testAddFilterWithFilterInterface()
    {
        $mock = \Mockery::mock(FilterInterface::class);
        $mock
            ->shouldReceive('getName')
            ->andReturn('foo');
        $mock
            ->shouldReceive('getFunction')
            ->andReturn(function () {
                return 'foo';
            });

        $engine = new Engine();
        $engine->addFilter($mock);
        $this->assertEquals('foo', $engine->getFilterFunction('foo')());
    }

    public function testAddFilterWithInvalidArguments()
    {
        $engine = new Engine();
        $this->expectException(\InvalidArgumentException::class);
        $engine->addFilter('foo');
    }

    public function testGetNotExistsFilter()
    {
        $engine = new Engine();
        $this->expectException(FilterNotFoundException::class);
        $engine->getFilterFunction('foo');
    }

    public function testDefaultFilterEscape()
    {
        $engine = new Engine();
        $this->assertEquals('&gt;', $engine->getFilterFunction('escape')('>'));
    }

    public function testDefaultFilterReplace()
    {
        $engine = new Engine();
        $this->assertEquals('bar', $engine->getFilterFunction('replace')('foo', 'foo', 'bar'));
    }
}
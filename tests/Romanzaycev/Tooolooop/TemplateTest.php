<?php declare(strict_types = 1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Tests;

use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\TestCase;
use Romanzaycev\Tooolooop\EngineInterface;
use Romanzaycev\Tooolooop\Scope\Scope;
use Romanzaycev\Tooolooop\Template\Exceptions\NestedBlockRenderingException;
use Romanzaycev\Tooolooop\Template\Exceptions\NoStartingBlockException;
use Romanzaycev\Tooolooop\Template\Exceptions\RestrictedBlockName;
use Romanzaycev\Tooolooop\Template\Exceptions\TemplateNotFoundException;
use Romanzaycev\Tooolooop\Template\Template;
use Romanzaycev\Tooolooop\Template\TemplateInterface;

/**
 * Class TemplateTest
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Tests
 */
class TemplateTest extends TestCase
{

    /**
     * @var EngineInterface
     */
    private $engine;

    protected function setUp()
    {
        $engineMock = \Mockery::mock(EngineInterface::class);
        $engineMock
            ->shouldReceive('getDirectory')
            ->andReturn(TOOOLOOOP_TEST_DIR . '/templates');
        $engineMock
            ->shouldReceive('getExtension')
            ->andReturn('php');
        $engineMock
            ->shouldReceive('getFilterFunction')
            ->withArgs(['escape'])
            ->andReturn(function ($param) {
                return htmlspecialchars($param, ENT_QUOTES, 'UTF-8');
            });
        $engineMock
            ->shouldReceive('getScopeClass')
            ->andReturn(Scope::class);

        $this->engine = $engineMock;
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testConstruct()
    {
        $template = new Template($this->engine, 'foo');
        $this->assertInstanceOf(TemplateInterface::class, $template);
    }

    public function testAssign()
    {
        $template = new Template($this->engine, 'foo');
        $this->assertEquals($template, $template->assign(['foo', 'bar']));
    }

    public function testRender()
    {
        $template = new Template($this->engine, 'foo');
        $this->assertEquals('<div>bar</div>', $template->render(['foo' => 'bar']));
    }

    public function testRenderWithNotFoundTemplate()
    {
        $template = new Template($this->engine, 'not_found_template');
        $this->expectException(TemplateNotFoundException::class);
        $template->render();
    }

    public function testRenderWithUndefinedVariable()
    {
        $template = new Template($this->engine, 'foo');
        $this->expectException(Notice::class);
        $template->render();
    }

    public function testCorrectThrowsInBuffer()
    {
        $template = new Template($this->engine, 'throws');
        $this->expectException(\Exception::class);
        $template->render();
    }

    public function testRenderWithParentTemplate()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['parent'])
            ->andReturn(new Template($this->engine, 'parent'));

        $template = new Template($this->engine, 'child');
        $this->assertEquals('<div>YOLO</div>', $template->render());
    }

    public function testRenderParentTemplateWithDataPassing()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['parent_data'])
            ->andReturn(new Template($this->engine, 'parent_data'));

        $template = new Template($this->engine, 'child_data');
        $this->assertEquals('<div>YOLO</div>', $template->render());
    }

    public function testRenderWithSiblingParentTemplate()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['sibling/tpla'])
            ->andReturn(new Template($this->engine, 'sibling/tpla'));

        $template = new Template($this->engine, 'sibling/tplb');
        $this->assertEquals('<div>foo</div>', $template->render());
    }

    public function testRenderLoad()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['foo'])
            ->andReturn(new Template($this->engine, 'foo'));

        $template = new Template($this->engine, 'load');
        $this->assertEquals('<div>foo</div>', $template->render(['foo' => 'foo']));
    }

    public function testRenderWithSiblinggLoad()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['foo'])
            ->andReturn(new Template($this->engine, 'foo'));

        $template = new Template($this->engine, 'load_sibling');
        $this->assertEquals('<div>foo</div>', $template->render(['foo' => 'foo']));
    }

    public function testRenderWithEmptyBlock()
    {
        $template = new Template($this->engine, 'parent');
        $this->assertEquals('<div></div>', $template->render());
    }

    public function testRenderBlock()
    {
        $this
            ->engine
            ->shouldReceive('make')
            ->withArgs(['block/parent'])
            ->andReturn(new Template($this->engine, 'block/parent'));

        $template = new Template($this->engine, 'block/child');
        $this->assertEquals('<div>YOLO</div>', $template->render(['foo' => 'YOLO']));
    }

    public function testRenderBlockWithEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $template = new Template($this->engine, 'block/empty_name');
        $template->render();
    }

    public function testRenderBlockWithRestrictedName()
    {
        $this->expectException(RestrictedBlockName::class);
        $template = new Template($this->engine, 'block/restricted_name');
        $template->render();
    }

    public function testRenderWithCustomExtension()
    {
        $engineMock = \Mockery::mock(EngineInterface::class);
        $engineMock
            ->shouldReceive('getDirectory')
            ->andReturn(TOOOLOOOP_TEST_DIR . '/templates');
        $engineMock
            ->shouldReceive('getExtension')
            ->andReturn('tpl');
        $engineMock
            ->shouldReceive('getScopeClass')
            ->andReturn(Scope::class);

        $template = new Template($engineMock, 'custom_ext');
        $this->assertEquals('foo', $template->render());
    }

    public function testNestedBlockRenderingException()
    {
        $template = new Template($this->engine, 'block/nested');
        $this->expectException(NestedBlockRenderingException::class);
        $template->render();
    }

    public function testUnexpectedEndRendering()
    {
        $template = new Template($this->engine, 'block/end');
        $this->expectException(NoStartingBlockException::class);
        $template->render();
    }

    public function testEscapeFilter()
    {
        $template = new Template($this->engine, 'filter/foo');
        $this->assertEquals('<div>&gt;</div>', $template->render(['foo' => '>']));
    }

    public function testFilterPhpFunc()
    {
        $template = new Template($this->engine, 'filter/func');
        $this->assertEquals('Yolo', $template->render(['foo' => 'yolo']));
    }

    public function testFilterPhpFuncWithArgs()
    {
        $template = new Template($this->engine, 'filter/func_args');
        $this->assertEquals('foo', $template->render(['foo' => '    foo    ']));
    }

    public function testRenderWithCustomScope()
    {
        $template = new Template($this->engine, 'custom_scope');
        $fixture = new class extends Scope {
            protected function custom()
            {
                echo 'CustomScope';
            }
        };
        $this->assertEquals('CustomScope', $template->render([], $fixture));
    }
}
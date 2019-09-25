<?php declare(strict_types = 1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017-2019 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Tests;

use Mockery\MockInterface;
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

    protected function setUp(): void
    {
        
    }
    
    protected function tearDown(): void
    {
        \Mockery::close();
    }

    /**
     * @return EngineInterface|MockInterface
     */
    private function getEngineMock(): EngineInterface
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
                return \htmlspecialchars((string)$param, \ENT_QUOTES, 'UTF-8');
            });
        $engineMock
            ->shouldReceive('getScopeClass')
            ->andReturn(Scope::class);
        $engineMock
            ->shouldReceive('getScope')
            ->withAnyArgs()
            ->andReturnUsing(function () { return new Scope(); });
        
        return $engineMock;
    }
    
    public function testConstruct()
    {
        $template = new Template($this->getEngineMock(), 'foo');
        $this->assertInstanceOf(TemplateInterface::class, $template);
    }

    public function testAssign()
    {
        $template = new Template($this->getEngineMock(), 'foo');
        $this->assertEquals($template, $template->assign(['foo', 'bar']));
    }

    /**
     * @throws \Throwable
     */
    public function testRender()
    {
        $template = new Template($this->getEngineMock(), 'foo');
        $this->assertEquals('<div>bar</div>', $template->render(['foo' => 'bar']));
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithNotFoundTemplate()
    {
        $template = new Template($this->getEngineMock(), 'not_found_template');
        $this->expectException(TemplateNotFoundException::class);
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithUndefinedVariable()
    {
        $template = new Template($this->getEngineMock(), 'foo');
        $this->expectException(Notice::class);
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testCorrectThrowsInBuffer()
    {
        $template = new Template($this->getEngineMock(), 'throws');
        $this->expectException(\Exception::class);
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithParentTemplate()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['parent'])
            ->andReturn(new Template($engine, 'parent'));

        $template = new Template($engine, 'child');
        $this->assertEquals('<div>YOLO</div>', $template->render());
    }

    /**
     * @throws \Throwable
     */
    public function testRenderParentTemplateWithDataPassing()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['parent_data'])
            ->andReturn(new Template($engine, 'parent_data'));

        $template = new Template($engine, 'child_data');
        $this->assertEquals('<div>YOLO</div>', $template->render());
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithSiblingParentTemplate()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['sibling/tpla'])
            ->andReturn(new Template($engine, 'sibling/tpla'));

        $template = new Template($engine, 'sibling/tplb');
        $this->assertEquals('<div>foo</div>', $template->render());
    }

    /**
     * @throws \Throwable
     */
    public function testRenderLoad()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['foo'])
            ->andReturn(new Template($engine, 'foo'));

        $template = new Template($engine, 'load');
        $this->assertEquals('<div>foo</div>', $template->render(['foo' => 'foo']));
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithSiblinggLoad()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['foo'])
            ->andReturn(new Template($engine, 'foo'));

        $template = new Template($engine, 'load_sibling');
        $this->assertEquals('<div>foo</div>', $template->render(['foo' => 'foo']));
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithEmptyBlock()
    {
        $template = new Template($this->getEngineMock(), 'parent');
        $this->assertEquals('<div></div>', $template->render());
    }

    /**
     * @throws \Throwable
     */
    public function testRenderBlock()
    {
        $engine = $this->getEngineMock();
        $engine
            ->shouldReceive('make')
            ->withArgs(['block/parent'])
            ->andReturn(new Template($engine, 'block/parent'));

        $template = new Template($engine, 'block/child');
        $this->assertEquals('<div>YOLO</div>', $template->render(['foo' => 'YOLO']));
    }

    /**
     * @throws \Throwable
     */
    public function testRenderBlockWithEmptyName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $template = new Template($this->getEngineMock(), 'block/empty_name');
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testRenderBlockWithRestrictedName()
    {
        $this->expectException(RestrictedBlockName::class);
        $template = new Template($this->getEngineMock(), 'block/restricted_name');
        $template->render();
    }

    /**
     * @throws \Throwable
     */
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
        $engineMock
            ->shouldReceive('getScope')
            ->andReturn(new Scope());

        $template = new Template($engineMock, 'custom_ext');
        $this->assertEquals('foo', $template->render());
    }

    /**
     * @throws \Throwable
     */
    public function testNestedBlockRenderingException()
    {
        $template = new Template($this->getEngineMock(), 'block/nested');
        $this->expectException(NestedBlockRenderingException::class);
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testUnexpectedEndRendering()
    {
        $template = new Template($this->getEngineMock(), 'block/end');
        $this->expectException(NoStartingBlockException::class);
        $template->render();
    }

    /**
     * @throws \Throwable
     */
    public function testEscapeFilter()
    {
        $template = new Template($this->getEngineMock(), 'filter/foo');
        $this->assertEquals('<div>&gt;</div>', $template->render(['foo' => '>']));
    }

    /**
     * @throws \Throwable
     */
    public function testFilterPhpFunc()
    {
        $template = new Template($this->getEngineMock(), 'filter/func');
        $this->assertEquals('Yolo', $template->render(['foo' => 'yolo']));
    }

    /**
     * @throws \Throwable
     */
    public function testFilterPhpFuncWithArgs()
    {
        $template = new Template($this->getEngineMock(), 'filter/func_args');
        $this->assertEquals('foo', $template->render(['foo' => '    foo    ']));
    }

    /**
     * @throws \Throwable
     */
    public function testRenderWithCustomScope()
    {
        $template = new Template($this->getEngineMock(), 'custom_scope');
        $fixture = new class extends Scope {
            protected function custom()
            {
                echo 'CustomScope';
            }
        };
        $this->assertEquals('CustomScope', $template->render([], $fixture));
    }
}
<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Template;

use Romanzaycev\Tooolooop\EngineInterface;
use Romanzaycev\Tooolooop\Scope\Scope;
use Romanzaycev\Tooolooop\Template\Exceptions\NestedBlockRenderingException;
use Romanzaycev\Tooolooop\Template\Exceptions\NoStartingBlockException;
use Romanzaycev\Tooolooop\Template\Exceptions\TemplateNotFoundException;

/**
 * Class Template
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Template
 */
class Template implements TemplateInterface
{

    const CONTENT_NAME = 'content';

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $parentTemplate;

    /**
     * @var array
     */
    private $parentTemplateData = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var null|string
     */
    private $currentBlock = null;

    /**
     * @var array
     */
    private $blocks = [];

    /**
     * @var array
     */
    protected $inheritedBlocks = [];

    /**
     * Template constructor.
     *
     * @param EngineInterface $engine template engine
     * @param string $name template name
     */
    public function __construct(EngineInterface $engine, string $name)
    {
        $this->engine = $engine;
        $this->name = $name;
    }

    /**
     * Assign template data.
     *
     * @param array $data template data
     * @return TemplateInterface self template
     */
    public function assign(array $data = []): TemplateInterface
    {
        $_data = [];

        foreach (array_keys($data) as $key) {
            if (is_string($key)) {
                $_data[$key] = $data[$key];
            }
        }

        $this->data = array_merge($this->data, $_data);

        return $this;
    }

    /**
     * Render template.
     *
     * @param array $data template data
     * @return string rendered template
     * @throws \Throwable
     */
    public function render(array $data = []): string
    {
        $this->assign($data);

        if (!$this->checkPath()) {
            throw new TemplateNotFoundException(sprintf("Template \"%s\" not found", $this->name));
        }

        $bufferingLevel = 0;
        try {
            $bufferingLevel = ob_get_level();

            ob_start();
            $scope = new Scope($this, $this->data);
            $scope->perform($this->getPath());

            $content = ob_get_clean();

            if ($this->parentTemplate !== null) {
                /**
                 * @var Template $parentTemplate
                 */
                $parentTemplate = $this->engine->make(
                    $this->parentTemplate
                );

                $parentTemplate->inheritedBlocks = array_merge(
                    $this->blocks,
                    [self::CONTENT_NAME => $content]
                );

                $content = $parentTemplate->render(array_merge(
                    $this->data,
                    $this->parentTemplateData
                ));
            }

            return $content;
        } catch (\Throwable $e) {
            while (ob_get_level() > $bufferingLevel) {
                ob_end_clean();
            }

            throw $e;
        }
    }

    /**
     * Set parent template name.
     *
     * @param string $template parent template name
     * @param array $data parent template data
     */
    protected function extend(string $template, array $data = [])
    {
        if ($this->isSiblingTemplate($template)) {
            $this->parentTemplate = $this->resolveSiblingTemplatePath($template);
        } else {
            $this->parentTemplate = $template;
        }

        $this->parentTemplateData = $data;
    }

    /**
     * Include other template as chunk.
     *
     * @param string $template child template name
     * @param array $data additional child template data
     * @return string
     */
    protected function load(string $template, $data = []): string
    {
        if ($this->isSiblingTemplate($template)) {
            $template = $this->resolveSiblingTemplatePath($template);
        }

        return $this
            ->engine
            ->make($template)
            ->assign($this->data)
            ->render($data);
    }

    /**
     * Show child template block.
     *
     * @param string $block
     * @return string
     */
    protected function block(string $block = ''): string
    {
        if ($block === '') {
            $block = self::CONTENT_NAME;
        }

        if (array_key_exists($block, $this->inheritedBlocks)) {
            return $this->inheritedBlocks[$block];
        }

        return '';
    }

    /**
     * Set starting block marker.
     *
     * @param string $name
     * @throws NestedBlockRenderingException
     */
    protected function start(string $name)
    {
        if (!is_null($this->currentBlock)) {
            throw new NestedBlockRenderingException(
                sprintf(
                    "Nested block rendering is prohibited. Trying to render block \"%s\" in template \"%s\"",
                    $name,
                    $this->name . '.' . $this->engine->getExtension()
                )
            );
        }

        $this->currentBlock = $name;
        $this->blocks[$name] = '';

        ob_start();
    }

    /**
     * Set ending block marker.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    protected function end()
    {
        if (is_null($this->currentBlock)) {
            throw new NoStartingBlockException(
                sprintf("Unexpected block ending, template \"%s\"", $this->name)
            );
        }

        $content = ob_get_clean();
        $this->blocks[$this->currentBlock] = $content;
        $this->currentBlock = null;
    }

    /**
     * Apply filters and return escaped variable.
     *
     * @param mixed $variable
     * @param array $filters
     * @return mixed
     */
    protected function e($variable, $filters = [])
    {
        if (!in_array('escape', $filters)) {
            $filters[] = 'escape';
        }

        return $this->applyFunctions(
            $variable,
            $filters
        );
    }

    /**
     * @param string $template
     * @return bool
     */
    private function isSiblingTemplate(string $template): bool
    {
        if (substr($template, 0, 1) === '.') {
            $directory = dirname($this->getPath());
            $template = $this->sanitizePathSegment($template);
            $extension = $this->engine->getExtension();

            return (
                file_exists($directory . DIRECTORY_SEPARATOR . $template . '.' . $extension)
                && !is_dir($directory . DIRECTORY_SEPARATOR . $template . '.' . $extension)
            );
        }

        return false;
    }

    /**
     * @param string $template
     * @return string
     */
    private function resolveSiblingTemplatePath(string $template): string
    {
        $template = $this->sanitizePathSegment($template);
        $currentTemplate = $this->sanitizePathSegment($this->name);
        $segments = explode(DIRECTORY_SEPARATOR, $currentTemplate);
        $length = count($segments);

        if ($length === 1) {
            return $template;
        }

        $segments[$length - 1] = $template;

        return implode(DIRECTORY_SEPARATOR, $segments);
    }

    /**
     * @return bool
     */
    private function checkPath(): bool
    {
        $path = $this->getPath();

        return (file_exists($path) && !is_dir($path));
    }

    /**
     * @return string
     */
    private function getPath(): string
    {
        $name = $this->sanitizePathSegment($this->name);
        $ds = DIRECTORY_SEPARATOR;

        return sprintf(
            '%s%s%s%s.%s',
            DIRECTORY_SEPARATOR,
            trim($this->engine->getDirectory(), $ds),
            $ds,
            $name,
            $this->engine->getExtension()
        );
    }

    /**
     * @param string $pathSegment
     * @return string
     */
    private function sanitizePathSegment($pathSegment): string
    {
        $ds = DIRECTORY_SEPARATOR;
        $pathSegment = str_replace('\\', $ds, $pathSegment);

        $segments = explode($ds, trim($pathSegment, $ds));

        $sanitizedSegments = [];
        foreach ($segments as $segment) {
            $segment = trim(basename($segment), '.');

            if ($segment) {
                $sanitizedSegments[] = $segment;
            }
        }

        return implode($ds, $sanitizedSegments);
    }

    /**
     * @param mixed $value
     * @param array $functions
     * @return mixed
     */
    private function applyFunctions($value, array $functions = [])
    {
        $appliedValue = $value;

        foreach ($functions as $k => $v) {
            $arguments = [$appliedValue];
            if (is_numeric($k)) {
                $filter = $v;
                $params = [];
            } else {
                $filter = $k;
                $params = $v;
            }

            if (!is_array($params)) {
                $params = [$params];
            }

            $arguments = array_merge($arguments, $params);

            if ($appliedValue !== null) {
                if (is_callable($filter)) {
                    $appliedValue = call_user_func_array($filter, $arguments);
                } else {
                    $func = $this->engine->getFilterFunction($filter);
                    $appliedValue = call_user_func_array($func, $arguments);
                }
            }
        }

        return $appliedValue;
    }
}
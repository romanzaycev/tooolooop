<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Template;

use Romanzaycev\Tooolooop\Scope\ScopeInterface;

/**
 * Interface TemplateInterface
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Template
 */
interface TemplateInterface
{
    /**
     * Assign template data.
     *
     * @param array $data
     * @return TemplateInterface self
     */
    public function assign(array $data = []): TemplateInterface;

    /**
     * Render template.
     *
     * @param array $data
     * @param ScopeInterface|null $scope optional template custom scope
     * @return string rendered template
     */
    public function render(array $data = [], ScopeInterface $scope = null): string;

}
<?php declare(strict_types=1);

/**
 * This file is part of the Tooolooop.
 * Copyright (c) 2017 Roman Zaycev <box@romanzaycev.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Romanzaycev\Tooolooop\Scope;

use Romanzaycev\Tooolooop\Template\TemplateInterface;

/**
 * Interface ScopeInterface
 *
 * @author Roman Zaycev <box@romanzaycev.ru>
 * @package Romanzaycev\Tooolooop\Scope
 */
interface ScopeInterface
{

    /**
     * Set template.
     *
     * @param TemplateInterface $template
     * @return void
     */
    public function setTemplate(TemplateInterface $template);

    /**
     * Set template data.
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data = []);


    /**
     * Perform template.
     *
     * @param string $path
     */
    public function perform(string $path);

}
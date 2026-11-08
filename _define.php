<?php

/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Olivier Meunier and contributors
 *
 * @copyright Olivier Meunier
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'ContactMe',
        'Add a simple contact form on your blog',
        'Olivier Meunier and contributors',
        '10.0',
        [
            'date'     => '2026-11-08T10:12:29+0100',
            'requires' => [
                ['core', '2.40'],
                ['TemplateHelper'],
            ],
            'permissions' => 'My',
            'type'        => 'plugin',
            'settings'    => [
            ],

            'details'    => 'https://open-time.net/?q=contactMe',
            'support'    => 'https://github.com/franck-paul/contactMe',
            'repository' => 'https://raw.githubusercontent.com/franck-paul/contactMe/main/dcstore.xml',
            'license'    => 'gpl2',
        ]
    );
}

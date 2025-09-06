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
$this->registerModule(
    'ContactMe',
    'Add a simple contact form on your blog',
    'Olivier Meunier and contributors',
    '8.0',
    [
        'date'     => '2025-09-06T22:14:58+0200',
        'requires' => [
            ['core', '2.36'],
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

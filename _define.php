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
    '7.1',
    [
        'date'     => '2025-05-05T13:37:27+0200',
        'requires' => [
            ['core', '2.34'],
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

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
    '6.1',
    [
        'date'        => '2025-02-26T16:09:26+0100',
        'requires'    => [['core', '2.33']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [
        ],

        'details'    => 'https://open-time.net/?q=contactMe',
        'support'    => 'https://github.com/franck-paul/contactMe',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/contactMe/main/dcstore.xml',
    ]
);

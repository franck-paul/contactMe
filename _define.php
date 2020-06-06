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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "ContactMe",                              // Name
    "Add a simple contact form on your blog", // Description
    "Olivier Meunier and contributors",       // Author
    '1.10',                                   // Version
    [
        'requires'    => [['core', '2.16']],                         // Dependencies
        'permissions' => 'admin',                                    // Permissions
        'type'        => 'plugin',                                   // Type
        'support'     => 'https://github.com/franck-paul/contactMe', // Support URL
        'settings'    => []
    ]
);

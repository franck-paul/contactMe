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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'ContactMe',                              // Name
    'Add a simple contact form on your blog', // Description
    'Olivier Meunier and contributors',       // Author
    '1.14',                                   // Version
    [
        'requires'    => [['core', '2.19']],                         // Dependencies
        'permissions' => 'admin',                                    // Permissions
        'type'        => 'plugin',                                   // Type
        'settings'    => [],

        'details'    => 'https://open-time.net/?q=contactMe',       // Details URL
        'support'    => 'https://github.com/franck-paul/contactMe', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/contactMe/main/dcstore.xml'
    ]
);

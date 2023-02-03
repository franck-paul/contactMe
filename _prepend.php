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
dcCore::app()->url->register('contactme', 'contact', '^contact(?:/(.+))?$', ['urlContactMe', 'contact']);

// Cope with new activation (since 1.10)
if (dcCore::app()->blog) {
    dcCore::app()->blog->settings->addNamespace('contactme');
    if (!dcCore::app()->blog->settings->contactme->settingExists('active')) {
        // Set active flag to true only if recipient(s) is/are set
        dcCore::app()->blog->settings->contactme->put('active', (bool) dcCore::app()->blog->settings->contactme->cm_recipients, 'boolean');
    }
}

require_once __DIR__ . '/_widgets.php';

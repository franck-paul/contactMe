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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    $old_version = dcCore::app()->getVersion(basename(__DIR__));

    if (version_compare((string) $old_version, '1.10', '<')) {
        // Default activation = true
        dcCore::app()->blog->settings->contactme->put('active', true, 'boolean', 'Active', false, true);
    }

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;

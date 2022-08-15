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

$new_version = dcCore::app()->plugins->moduleInfo('contactMe', 'version');
$old_version = dcCore::app()->getVersion('contactMe');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try {
    if (version_compare($old_version, '1.10', '<')) {
        // Default activation = true
        dcCore::app()->blog->settings->addNamespace('contactme');
        dcCore::app()->blog->settings->contactme->put('active', true, 'boolean', 'Active', false, true);
    }

    dcCore::app()->setVersion('contactMe', $new_version);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;

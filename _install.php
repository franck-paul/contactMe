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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$new_version = $core->plugins->moduleInfo('contactMe', 'version');
$old_version = $core->getVersion('contactMe');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try
{
    if (version_compare($old_version, '1.10', '<')) {
        // Default activation = true
        $core->blog->settings->addNamespace('contactme');
        $core->blog->settings->contactme->put('active', true, 'boolean', 'Active', false, true);
    }

    $core->setVersion('contactMe', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;

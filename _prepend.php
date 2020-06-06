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

$core->url->register('contactme', 'contact', '^contact(?:/(.+))?$', ['urlContactMe', 'contact']);

// Cope with new activation (since 1.10)
$core->blog->settings->addNamespace('contactme');
if (!$core->blog->settings->contactme->settingExists('active')) {
    // Set active flag to true only if recipient(s) is/are set
    $core->blog->settings->contactme->put('active', (boolean) $core->blog->settings->contactme->cm_recipients, 'boolean');
}

require dirname(__FILE__) . '/_widgets.php';

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

$core->addBehavior('initWidgets', ['contactMeWidgets', 'initWidgets']);

class contactMeWidgets
{
    public static function initWidgets($w)
    {
        $w->create('contactMe', __('Contact me'), ['tplContactMe', 'contactMeWidget'], null, __('Link to the contact form'));
        $w->contactMe->setting('title', __('Title:'), __('Contact'));
        $w->contactMe->setting('link_title', __('Link title:'), __('Contact me'));
        $w->contactMe->setting('homeonly', __('Display on:'), 0, 'combo',
            [
                __('All pages')           => 0,
                __('Home page only')      => 1,
                __('Except on home page') => 2
            ]
        );
        $w->contactMe->setting('content_only', __('Content only'), 0, 'check');
        $w->contactMe->setting('class', __('CSS class:'), '');
        $w->contactMe->setting('offline', __('Offline'), 0, 'check');
    }
}

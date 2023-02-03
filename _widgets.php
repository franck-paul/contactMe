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
class contactMeWidgets
{
    public static function initWidgets($w)
    {
        $w
            ->create('contactMe', __('Contact me'), ['tplContactMe', 'contactMeWidget'], null, __('Link to the contact form'))
            ->addTitle(__('Contact'))
            ->setting('link_title', __('Link title:'), __('Contact me'))
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
    }
}

dcCore::app()->addBehavior('initWidgets', [contactMeWidgets::class, 'initWidgets']);

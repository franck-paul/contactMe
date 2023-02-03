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

// dead but useful code, in order to have translations
__('ContactMe') . __('Add a simple contact form on your blog');

dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Contact me'),
    'plugin.php?p=contactMe',
    urldecode(dcPage::getPF('contactMe/icon.svg')),
    preg_match('/plugin.php\?p=contactMe(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
        dcAuth::PERMISSION_ADMIN,
    ]), dcCore::app()->blog->id)
);

class contactMeAdmin
{
    public static function adminDashboardFavorites($favs)
    {
        $favs->register('contactMe', [
            'title'       => __('Contact me'),
            'url'         => 'plugin.php?p=contactMe',
            'small-icon'  => urldecode(dcPage::getPF('contactMe/icon.svg')),
            'large-icon'  => urldecode(dcPage::getPF('contactMe/icon.svg')),
            'permissions' => dcCore::app()->auth->makePermissions([
                dcAuth::PERMISSION_ADMIN,
            ]),
        ]);
    }

    public static function adminRteFlags($rte)
    {
        $rte['contactme'] = [true, __('Contact me form caption and messages')];
    }
}

/* Register favorite */
dcCore::app()->addBehaviors([
    'adminDashboardFavoritesV2' => [contactMeAdmin::class, 'adminDashboardFavorites'],
    'adminRteFlagsV2'           => [contactMeAdmin::class, 'adminRteFlags'],
]);

class contactMeSimpleMenu
{
    public static function adminSimpleMenuAddType($items)
    {
        $items['contactme'] = new ArrayObject([__('Contact me'), false]);
    }

    public static function adminSimpleMenuBeforeEdit($item_type, $item_select, $args)
    {
        if ($item_type == 'contactme') {
            $args[0] = __('Contact me');
            $args[1] = __('Mail contact form');
            $args[2] .= dcCore::app()->url->getURLFor('contactme');
        }
    }
}

dcCore::app()->addBehaviors([
    'adminSimpleMenuAddType'    => [contactMeSimpleMenu::class, 'adminSimpleMenuAddType'],
    'adminSimpleMenuBeforeEdit' => [contactMeSimpleMenu::class, 'adminSimpleMenuBeforeEdit'],
]);

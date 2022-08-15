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

$_menu['Blog']->addItem(
    __('Contact me'),
    'plugin.php?p=contactMe',
    urldecode(dcPage::getPF('contactMe/icon.svg')),
    preg_match('/plugin.php\?p=contactMe(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check('admin', dcCore::app()->blog->id)
);

/* Register favorite */
dcCore::app()->addBehavior('adminDashboardFavorites', ['contactMeAdmin', 'adminDashboardFavorites']);
dcCore::app()->addBehavior('adminRteFlags', ['contactMeAdmin', 'adminRteFlags']);

class contactMeAdmin
{
    public static function adminDashboardFavorites($core, $favs)
    {
        $favs->register('contactMe', [
            'title'       => __('Contact me'),
            'url'         => 'plugin.php?p=contactMe',
            'small-icon'  => urldecode(dcPage::getPF('contactMe/icon.svg')),
            'large-icon'  => urldecode(dcPage::getPF('contactMe/icon.svg')),
            'permissions' => 'admin',
        ]);
    }

    public static function adminRteFlags($core, $rte)
    {
        $rte['contactme'] = [true, __('Contact me form caption and messages')];
    }
}

dcCore::app()->addBehavior('adminSimpleMenuAddType', ['contactMeSimpleMenu', 'adminSimpleMenuAddType']);
dcCore::app()->addBehavior('adminSimpleMenuBeforeEdit', ['contactMeSimpleMenu', 'adminSimpleMenuBeforeEdit']);

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

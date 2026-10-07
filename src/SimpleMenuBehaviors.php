<?php

/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use ArrayObject;
use Dotclear\App;
use Dotclear\Plugin\simpleMenu\MenuItemEdit;

class SimpleMenuBehaviors
{
    /**
     * @param      ArrayObject<array-key, array{string, bool}>  $arrayObject  The items
     */
    public static function adminSimpleMenuAddType(ArrayObject $arrayObject): string
    {
        $arrayObject['contactme'] = [__('Contact me'), false];

        return '';
    }

    public static function adminSimpleMenuBeforeEditV2(MenuItemEdit $menuItemEdit): string
    {
        if ($menuItemEdit->getType() === 'contactme') {
            $menuItemEdit->setLabel(__('Contact me'));
            $menuItemEdit->setDescription(__('Mail contact form'));
            $menuItemEdit->setUrl($menuItemEdit->getUrl() . App::url()->getURLFor('contactme'));
        }

        return '';
    }
}

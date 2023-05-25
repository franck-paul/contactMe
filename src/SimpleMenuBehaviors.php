<?php
/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use ArrayObject;
use dcCore;

class SimpleMenuBehaviors
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

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
    /**
     * @param      ArrayObject<string, ArrayObject<int, mixed>>  $items  The items
     *
     * @return     string
     */
    public static function adminSimpleMenuAddType($items): string
    {
        $items['contactme'] = new ArrayObject([__('Contact me'), false]);

        return '';
    }

    /**
     * @param      string               $item_type    The item type
     * @param      string               $item_select  The item select
     * @param      array<int, string>   $args         The arguments
     *
     * @return     string
     */
    public static function adminSimpleMenuBeforeEdit($item_type, $item_select, $args): string
    {
        if ($item_type == 'contactme') {
            $args[0] = __('Contact me');
            $args[1] = __('Mail contact form');
            $args[2] .= dcCore::app()->url->getURLFor('contactme');
        }

        return '';
    }
}

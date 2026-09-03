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
use Dotclear\Core\Backend\Favorites;

class BackendBehaviors
{
    /**
     * @param      Favorites  $favorites   The favs
     */
    public static function adminDashboardFavorites(Favorites $favorites): string
    {
        $favorites->register('contactMe', [
            'title'          => __('Contact me'),
            'url'            => My::manageUrl(),
            'menu-icon'      => My::icon(),
            'dashboard-icon' => My::icon(),
            'permissions'    => My::checkContext(My::MENU),
        ]);

        return '';
    }

    /**
     * @param      ArrayObject<string, mixed>  $arrayObject
     */
    public static function adminRteFlags(ArrayObject $arrayObject): string
    {
        $arrayObject['contactme'] = [true, __('Contact me form caption and messages')];

        return '';
    }
}

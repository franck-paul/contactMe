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
use dcFavorites;

class BackendBehaviors
{
    public static function adminDashboardFavorites(dcFavorites $favs)
    {
        $favs->register('contactMe', [
            'title'       => __('Contact me'),
            'url'         => My::makeUrl(),
            'small-icon'  => My::icons(),
            'large-icon'  => My::icons(),
            'permissions' => My::checkContext(My::MENU),
        ]);
    }

    public static function adminRteFlags(ArrayObject $rte)
    {
        $rte['contactme'] = [true, __('Contact me form caption and messages')];
    }
}

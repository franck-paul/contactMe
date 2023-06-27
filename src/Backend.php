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

use dcAdmin;
use dcCore;
use dcNsProcess;

class Backend extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::BACKEND);

        // dead but useful code, in order to have translations
        __('ContactMe') . __('Add a simple contact form on your blog');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
            __('Contact me'),
            My::makeUrl(),
            My::icons(),
            preg_match(My::urlScheme(), $_SERVER['REQUEST_URI']),
            My::checkContext(My::MENU)
        );

        /* Register favorite */
        dcCore::app()->addBehaviors([
            'adminDashboardFavoritesV2' => [BackendBehaviors::class, 'adminDashboardFavorites'],
            'adminRteFlagsV2'           => [BackendBehaviors::class, 'adminRteFlags'],

            // SimpleMenu behaviors
            'adminSimpleMenuAddType'    => [SimpleMenuBehaviors::class, 'adminSimpleMenuAddType'],
            'adminSimpleMenuBeforeEdit' => [SimpleMenuBehaviors::class, 'adminSimpleMenuBeforeEdit'],
        ]);

        if (My::checkContext(My::WIDGETS)) {
            dcCore::app()->addBehaviors([
                'initWidgets' => [Widgets::class, 'initWidgets'],
            ]);
        }

        return true;
    }
}

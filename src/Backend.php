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

use Dotclear\App;
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // dead but useful code, in order to have translations
        __('ContactMe') . __('Add a simple contact form on your blog');

        return self::status(My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        My::addBackendMenuItem(App::backend()->menus()::MENU_BLOG);

        /* Register favorite */
        App::behavior()->addBehaviors([
            'adminDashboardFavoritesV2' => BackendBehaviors::adminDashboardFavorites(...),
            'adminRteFlagsV2'           => BackendBehaviors::adminRteFlags(...),

            // SimpleMenu behaviors
            'adminSimpleMenuAddType'    => SimpleMenuBehaviors::adminSimpleMenuAddType(...),
            'adminSimpleMenuBeforeEdit' => SimpleMenuBehaviors::adminSimpleMenuBeforeEdit(...),
        ]);

        if (My::checkContext(My::WIDGETS)) {
            App::behavior()->addBehaviors([
                'initWidgets' => Widgets::initWidgets(...),
            ]);
        }

        return true;
    }
}

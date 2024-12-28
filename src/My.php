<?php

/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Jean-Christian Denis, Franck Paul and contributors
 *
 * @copyright Jean-Christian Denis, Franck Paul
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use Dotclear\App;
use Dotclear\Core\Frontend\Utility;
use Dotclear\Module\MyPlugin;

/**
 * Plugin definitions
 */
class My extends MyPlugin
{
    protected static function checkCustomContext(int $context): ?bool
    {
        return match ($context) {
            // Limit backend to content admin and pages user
            self::BACKEND, self::MANAGE, self::MENU, self::WIDGETS => App::task()->checkContext('BACKEND')
                && App::blog()->isDefined()
                && App::auth()->check(App::auth()->makePermissions([
                    App::auth()::PERMISSION_ADMIN,
                ]), App::blog()->id()),

            default => null,
        };
    }

    /**
     * Return template path to use
     */
    public static function tplPath(): string
    {
        $tplset = App::themes()->moduleInfo(App::blog()->settings()->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT, $tplset]))) {
            // a sub-dir exists for my plugin with this tplset
            return implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT, $tplset]);
        }

        $tplset = App::config()->defaultTplset();
        if (!empty($tplset) && is_dir(implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT, $tplset]))) {
            // a sub-dir exists for my plugin with the default tplset
            return implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT, $tplset]);
        }

        // return base tpl dir
        return implode(DIRECTORY_SEPARATOR, [My::path(), Utility::TPL_ROOT]);
    }
}

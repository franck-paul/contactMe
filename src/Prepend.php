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

use dcCore;
use dcNamespace;
use dcNsProcess;

class Prepend extends dcNsProcess
{
    protected static $init = false; /** @deprecated since 2.27 */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::PREPEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->url->register('contactme', 'contact', '^contact(?:/(.+))?$', [FrontendUrl::class, 'contact']);

        if (dcCore::app()->blog) {
            $settings = dcCore::app()->blog->settings->get(My::id());
            if (!$settings->settingExists('active')) {
                // Set active flag to true only if recipient(s) is/are set
                $settings->put('active', (bool) $settings->recipients, dcNamespace::NS_BOOL);
            }
        }

        return true;
    }
}

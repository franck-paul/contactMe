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
use Dotclear\Core\Process;

class Prepend extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::PREPEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        dcCore::app()->url->register('contactme', 'contact', '^contact(?:/(.+))?$', FrontendUrl::contact(...));

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

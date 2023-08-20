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
use Dotclear\Core\Process;

class Frontend extends Process
{
    public static function init(): bool
    {
        // Localized string we find in template
        __('Subject');
        __('Message');

        return self::status(My::checkContext(My::FRONTEND));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        // Don't do things in frontend if plugin disabled
        $settings = dcCore::app()->blog->settings->get(My::id());
        if (!(bool) $settings->active) {
            return false;
        }

        dcCore::app()->tpl->addValue('ContactMeURL', FrontendTemplate::ContactMeURL(...));
        dcCore::app()->tpl->addBlock('ContactMeIf', FrontendTemplate::ContactMeIf(...));
        dcCore::app()->tpl->addValue('ContactMePageTitle', FrontendTemplate::ContactMePageTitle(...));
        dcCore::app()->tpl->addValue('ContactMeFormCaption', FrontendTemplate::ContactMeFormCaption(...));
        dcCore::app()->tpl->addValue('ContactMeMsgSuccess', FrontendTemplate::ContactMeMsgSuccess(...));
        dcCore::app()->tpl->addValue('ContactMeMsgError', FrontendTemplate::ContactMeMsgError(...));
        dcCore::app()->tpl->addValue('ContactMeName', FrontendTemplate::ContactMeName(...));
        dcCore::app()->tpl->addValue('ContactMeEmail', FrontendTemplate::ContactMeEmail(...));
        dcCore::app()->tpl->addValue('ContactMeSite', FrontendTemplate::ContactMeSite(...));
        dcCore::app()->tpl->addValue('ContactMeSubject', FrontendTemplate::ContactMeSubject(...));
        dcCore::app()->tpl->addValue('ContactMeMessage', FrontendTemplate::ContactMeMessage(...));

        dcCore::app()->addBehaviors([
            'publicBreadcrumb' => FrontendBehaviors::publicBreadcrumb(...),

            'initWidgets' => Widgets::initWidgets(...),
        ]);

        return true;
    }
}

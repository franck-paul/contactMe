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
use dcNsProcess;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::FRONTEND);

        // Localized string we find in template
        __('Subject');
        __('Message');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->tpl->addValue('ContactMeURL', [FrontendTemplate::class, 'ContactMeURL']);
        dcCore::app()->tpl->addBlock('ContactMeIf', [FrontendTemplate::class, 'ContactMeIf']);
        dcCore::app()->tpl->addValue('ContactMePageTitle', [FrontendTemplate::class, 'ContactMePageTitle']);
        dcCore::app()->tpl->addValue('ContactMeFormCaption', [FrontendTemplate::class, 'ContactMeFormCaption']);
        dcCore::app()->tpl->addValue('ContactMeMsgSuccess', [FrontendTemplate::class, 'ContactMeMsgSuccess']);
        dcCore::app()->tpl->addValue('ContactMeMsgError', [FrontendTemplate::class, 'ContactMeMsgError']);
        dcCore::app()->tpl->addValue('ContactMeName', [FrontendTemplate::class, 'ContactMeName']);
        dcCore::app()->tpl->addValue('ContactMeEmail', [FrontendTemplate::class, 'ContactMeEmail']);
        dcCore::app()->tpl->addValue('ContactMeSite', [FrontendTemplate::class, 'ContactMeSite']);
        dcCore::app()->tpl->addValue('ContactMeSubject', [FrontendTemplate::class, 'ContactMeSubject']);
        dcCore::app()->tpl->addValue('ContactMeMessage', [FrontendTemplate::class, 'ContactMeMessage']);

        dcCore::app()->addBehaviors([
            'publicBreadcrumb' => [FrontendBehaviors::class, 'publicBreadcrumb'],

            'initWidgets' => [Widgets::class, 'initWidgets'],
        ]);

        return true;
    }
}

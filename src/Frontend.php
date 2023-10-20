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
        $settings = My::settings();
        if (!(bool) $settings->active) {
            return false;
        }

        App::frontend()->template()->addValue('ContactMeURL', FrontendTemplate::ContactMeURL(...));
        App::frontend()->template()->addBlock('ContactMeIf', FrontendTemplate::ContactMeIf(...));
        App::frontend()->template()->addValue('ContactMePageTitle', FrontendTemplate::ContactMePageTitle(...));
        App::frontend()->template()->addValue('ContactMeFormCaption', FrontendTemplate::ContactMeFormCaption(...));
        App::frontend()->template()->addValue('ContactMeMsgSuccess', FrontendTemplate::ContactMeMsgSuccess(...));
        App::frontend()->template()->addValue('ContactMeMsgError', FrontendTemplate::ContactMeMsgError(...));
        App::frontend()->template()->addValue('ContactMeName', FrontendTemplate::ContactMeName(...));
        App::frontend()->template()->addValue('ContactMeEmail', FrontendTemplate::ContactMeEmail(...));
        App::frontend()->template()->addValue('ContactMeSite', FrontendTemplate::ContactMeSite(...));
        App::frontend()->template()->addValue('ContactMeSubject', FrontendTemplate::ContactMeSubject(...));
        App::frontend()->template()->addValue('ContactMeMessage', FrontendTemplate::ContactMeMessage(...));

        App::behavior()->addBehaviors([
            'publicBreadcrumb' => FrontendBehaviors::publicBreadcrumb(...),

            'initWidgets' => Widgets::initWidgets(...),
        ]);

        return true;
    }
}

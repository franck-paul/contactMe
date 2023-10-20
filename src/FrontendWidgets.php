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
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    public static function renderWidget(WidgetsElement $w): string
    {
        if ($w->offline) {
            return '';
        }

        if (($w->homeonly == 1 && !App::url()->isHome(App::url()->type)) || ($w->homeonly == 2 && App::url()->isHome(App::url()->type))) {
            return '';
        }

        $settings = My::settings();
        if (!$settings->recipients || !$settings->active) {
            return '';
        }

        $res = ($w->title ? $w->renderTitle(Html::escapeHTML($w->title)) : '') .
        '<p><a href="' . App::blog()->url() . App::url()->getURLFor('contactme') . '">' .
            ($w->link_title ? Html::escapeHTML($w->link_title) : __('Contact me')) .
            '</a></p>';

        return $w->renderDiv((bool) $w->content_only, 'contact-me ' . $w->class, '', $res);
    }
}

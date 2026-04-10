<?php

/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use Dotclear\App;
use Dotclear\Helper\Html\Form\Link;
use Dotclear\Helper\Html\Form\None;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Set;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Dotclear\Plugin\widgets\WidgetsElement;

class FrontendWidgets
{
    public static function renderWidget(WidgetsElement $w): string
    {
        if ($w->offline) {
            return '';
        }

        if (($w->homeonly == 1 && !App::url()->isHome(App::url()->getType())) || ($w->homeonly == 2 && App::url()->isHome(App::url()->getType()))) {
            return '';
        }

        $settings = My::settings();
        if (!$settings->recipients || !$settings->active) {
            return '';
        }

        $link_title = is_string($link_title = $w->get('link_title')) ? $link_title : '';
        if ($link_title === '') {
            $link_title = __('Contact me');
        }

        $buffer = (new Set())
            ->items([
                $w->title ? new Text(null, $w->renderTitle(Html::escapeHTML($w->title))) : new None(),
                (new Para())
                    ->items([
                        (new Link())
                            ->href(App::blog()->url() . App::url()->getURLFor('contactme'))
                            ->text($link_title),
                    ]),
            ])
        ->render();

        return $w->renderDiv((bool) $w->content_only, 'contact-me ' . $w->class, '', $buffer);
    }
}

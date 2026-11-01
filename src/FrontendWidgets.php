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
    public static function renderWidget(WidgetsElement $widgetsElement): string
    {
        if ($widgetsElement->isOffline()) {
            return '';
        }

        if (!$widgetsElement->checkHomeOnly(App::url()->getType())) {
            return '';
        }

        $settings = My::settings();
        if ($settings->getStr('recipients', false) === '' || !$settings->getBool('active', false)) {
            return '';
        }

        $link_title = is_string($link_title = $widgetsElement->get('link_title')) ? $link_title : '';
        if ($link_title === '') {
            $link_title = __('Contact me');
        }

        $buffer = (new Set())
            ->items([
                $widgetsElement->getTitle() !== '' ? new Text(null, $widgetsElement->renderTitle(Html::escapeHTML($widgetsElement->getTitle()))) : new None(),
                (new Para())
                    ->items([
                        (new Link())
                            ->href(App::blog()->url() . App::url()->getURLFor('contactme'))
                            ->text($link_title),
                    ]),
            ])
        ->render();

        return $widgetsElement->renderDiv($widgetsElement->getContentOnly(), 'contact-me ' . $widgetsElement->getClass(), '', $buffer);
    }
}

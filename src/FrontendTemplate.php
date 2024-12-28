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

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Frontend\Tpl;
use Dotclear\Helper\Html\Html;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeURL(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::blog()->url().App::url()->getURLFor("contactme")') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function ContactMeIf(array|ArrayObject $attr, string $content): string
    {
        $if = [];

        $operator = isset($attr['operator']) ? Tpl::getOperator($attr['operator']) : '&&';

        if (isset($attr['sent'])) {
            $sign = (bool) $attr['sent'] ? '' : '!';
            $if[] = $sign . "App::frontend()->context()->contactme['sent']";
        }

        if (isset($attr['error'])) {
            $sign = (bool) $attr['error'] ? '' : '!';
            $if[] = $sign . "App::frontend()->context()->contactme['error']";
        }

        if ($if !== []) {
            return '<?php if(' . implode(' ' . $operator . ' ', $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMePageTitle(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::blog()->settings()->' . My::id() . '->page_title') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeFormCaption(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::blog()->settings()->' . My::id() . '->form_caption') . ' ?>';
    }

    public static function ContactMeMsgSuccess(): string
    {
        return '<?= App::blog()->settings()->' . My::id() . '->msg_success ?>';
    }

    public static function ContactMeMsgError(): string
    {
        return '<?= sprintf(App::blog()->settings()->' . My::id() . '->msg_error,' . Html::class . '::escapeHTML(App::frontend()->context()->contactme["error_msg"])) ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeName(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::frontend()->context()->contactme["name"]') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeEmail(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::frontend()->context()->contactme["email"]') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeSite(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::frontend()->context()->contactme["site"]') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeSubject(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::frontend()->context()->contactme["subject"]') . ' ?>';
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeMessage(array|ArrayObject $attr): string
    {
        $f = App::frontend()->template()->getFilters($attr);

        return '<?= ' . sprintf($f, 'App::frontend()->context()->contactme["message"]') . ' ?>';
    }
}

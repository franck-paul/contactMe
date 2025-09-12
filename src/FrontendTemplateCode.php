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

class FrontendTemplateCode
{
    /**
     * PHP code for tpl:ContactMeURL value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeURL(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::blog()->url() . App::url()->getURLFor('contactme'),
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeIf block
     */
    public static function ContactMeIf(
        string $_test_HTML,
        string $_content_HTML
    ): void {
        /* @phpstan-ignore-next-line */
        if (($_test_HTML) === true) : ?>
            $_content_HTML
        <?php endif;
    }

    /**
     * PHP code for tpl:ContactMePageTitle value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMePageTitle(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::blog()->settings()->$_id_HTML->page_title,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeFormCaption value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeFormCaption(
        string $_id_HTML,
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::blog()->settings()->$_id_HTML->form_caption,
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeMsgSuccess value
     */
    public static function ContactMeMsgSuccess(
        string $_id_HTML,
    ): void {
        echo App::blog()->settings()->$_id_HTML->msg_success;
    }

    /**
     * PHP code for tpl:ContactMeMsgError value
     */
    public static function ContactMeMsgError(
        string $_id_HTML,
    ): void {
        echo sprintf(
            App::blog()->settings()->$_id_HTML->msg_error,
            \Dotclear\Helper\Html\Html::escapeHTML(App::frontend()->context()->contactme['error_msg'])
        );
    }

    /**
     * PHP code for tpl:ContactMeName value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeName(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->contactme['name'],
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeEmail value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeEmail(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->contactme['email'],
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeSite value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeSite(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->contactme['site'],
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeSubject value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeSubject(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->contactme['subject'],
            $_params_,
            $_tag_
        );
    }

    /**
     * PHP code for tpl:ContactMeMessage value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeMessage(
        array $_params_,
        string $_tag_
    ): void {
        echo App::frontend()->context()::global_filters(
            App::frontend()->context()->contactme['message'],
            $_params_,
            $_tag_
        );
    }
}

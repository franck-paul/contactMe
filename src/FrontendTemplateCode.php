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
        string $_id_,
        array $_params_,
        string $_tag_
    ): void {
        $contactme_page_title = is_string($contactme_page_title = App::blog()->settings()->get($_id_)->page_title) ? $contactme_page_title : '';
        if ($contactme_page_title !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_page_title,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_page_title);
    }

    /**
     * PHP code for tpl:ContactMeFormCaption value
     *
     * @param      array<int|string, mixed>     $_params_  The parameters
     */
    public static function ContactMeFormCaption(
        string $_id_,
        array $_params_,
        string $_tag_
    ): void {
        $contactme_form_caption = is_string($contactme_form_caption = App::blog()->settings()->get($_id_)->form_caption) ? $contactme_form_caption : '';
        if ($contactme_form_caption !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_form_caption,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_form_caption);
    }

    /**
     * PHP code for tpl:ContactMeMsgSuccess value
     */
    public static function ContactMeMsgSuccess(
        string $_id_,
    ): void {
        $contactme_msg_success = is_string($contactme_msg_success = App::blog()->settings()->get($_id_)->msg_success) ? $contactme_msg_success : '';
        if ($contactme_msg_success !== '') {
            echo $contactme_msg_success;
        }
        unset($contactme_msg_success);
    }

    /**
     * PHP code for tpl:ContactMeMsgError value
     */
    public static function ContactMeMsgError(
        string $_id_,
    ): void {
        $contactme_msg_error = is_string($contactme_msg_error = App::blog()->settings()->get($_id_)->msg_error) ? $contactme_msg_error : '';
        $contactme_error_msg = is_array(App::frontend()->context()->contactme) && is_string($contactme_error_msg = App::frontend()->context()->contactme['error_msg'] ?? '') ? $contactme_error_msg : '';
        if ($contactme_msg_error !== '' && $contactme_error_msg !== '') {
            echo sprintf(
                $contactme_msg_error,
                \Dotclear\Helper\Html\Html::escapeHTML($contactme_error_msg)
            );
        }
        unset($contactme_msg_error, $contactme_error_msg);
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
        $contactme_name = is_array(App::frontend()->context()->contactme) && is_string($contactme_name = App::frontend()->context()->contactme['name'] ?? '') ? $contactme_name : '';
        if ($contactme_name !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_name,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_name);
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
        $contactme_mail = is_array(App::frontend()->context()->contactme) && is_string($contactme_mail = App::frontend()->context()->contactme['email'] ?? '') ? $contactme_mail : '';
        if ($contactme_mail !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_mail,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_mail);
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
        $contactme_site = is_array(App::frontend()->context()->contactme) && is_string($contactme_site = App::frontend()->context()->contactme['site'] ?? '') ? $contactme_site : '';
        if ($contactme_site !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_site,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_site);
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
        $contactme_subject = is_array(App::frontend()->context()->contactme) && is_string($contactme_subject = App::frontend()->context()->contactme['subject'] ?? '') ? $contactme_subject : '';
        if ($contactme_subject !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_subject,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_subject);
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
        $contactme_message = is_array(App::frontend()->context()->contactme) && is_string($contactme_message = App::frontend()->context()->contactme['message'] ?? '') ? $contactme_message : '';
        if ($contactme_message !== '') {
            echo App::frontend()->context()::global_filters(
                $contactme_message,
                $_params_,
                $_tag_
            );
        }
        unset($contactme_message);
    }
}

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
use dcAntispam;
use dcBlog;
use dcCore;
use dcPublic;
use dcUrlHandlers;
use Dotclear\Helper\File\Path;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\Mail\Mail;
use Dotclear\Helper\Text;
use Exception;

class FrontendUrl extends dcUrlHandlers
{
    public static function contact($args)
    {
        $settings = dcCore::app()->blog->settings->get(My::id());
        if (!$settings->recipients || !$settings->active) {
            self::p404();
        }

        dcCore::app()->ctx->contactme = new ArrayObject([
            'name'      => '',
            'email'     => '',
            'site'      => '',
            'subject'   => '',
            'message'   => '',
            'sent'      => false,
            'error'     => false,
            'error_msg' => '',
        ]);

        $send_msg = isset($_POST['c_name']) && isset($_POST['c_mail']) && isset($_POST['c_site']) && isset($_POST['c_message']) && isset($_POST['c_subject']);

        if ($args == 'sent') {
            dcCore::app()->ctx->contactme['sent'] = true;
        } elseif ($send_msg) {
            # Spam trap
            if (!empty($_POST['f_mail'])) {
                Http::head(412, 'Precondition Failed');
                header('Content-Type: text/plain');
                echo 'So Long, and Thanks For All the Fish';
                exit;
            }

            try {
                dcCore::app()->ctx->contactme['name']    = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_name']);
                dcCore::app()->ctx->contactme['email']   = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_mail']);
                dcCore::app()->ctx->contactme['site']    = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_site']);
                dcCore::app()->ctx->contactme['subject'] = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_subject']);
                dcCore::app()->ctx->contactme['message'] = $_POST['c_message'];

                # Checks provided fields
                if (empty($_POST['c_name'])) {
                    throw new Exception(__('You must provide a name.'));
                }

                if (!Text::isEmail($_POST['c_mail'])) {
                    throw new Exception(__('You must provide a valid email address.'));
                }

                if (empty($_POST['c_subject'])) {
                    throw new Exception(__('You must provide a subject.'));
                }

                if (empty($_POST['c_message'])) {
                    throw new Exception(__('You must write a message.'));
                }

                # Checks recipients addresses
                $recipients = explode(',', $settings->recipients);
                $rc2        = [];
                foreach ($recipients as $v) {
                    $v = trim((string) $v);
                    if (!empty($v) && Text::isEmail($v)) {
                        $rc2[] = $v;
                    }
                }
                $recipients = $rc2;
                unset($rc2);

                if (empty($recipients)) {
                    throw new Exception(__('No valid contact recipient was found.'));
                }

                # Check message form spam
                if ($settings->use_antispam && class_exists('dcAntispam') && isset(dcCore::app()->spamfilters)) {
                    # Fake cursor to check spam
                    $cur                    = dcCore::app()->con->openCursor('foo');    // @phpstan-ignore-line
                    $cur->comment_trackback = 0;
                    $cur->comment_author    = dcCore::app()->ctx->contactme['name'];
                    $cur->comment_email     = dcCore::app()->ctx->contactme['email'];
                    $cur->comment_site      = dcCore::app()->ctx->contactme['site'];
                    $cur->comment_ip        = Http::realIP();
                    $cur->comment_content   = dcCore::app()->ctx->contactme['message'];
                    $cur->post_id           = 0; // That could break things...
                    $cur->comment_status    = dcBlog::COMMENT_PUBLISHED;

                    @dcAntispam::isSpam($cur);

                    if ($cur->comment_status == dcBlog::COMMENT_JUNK) {   // @phpstan-ignore-line
                        unset($cur);

                        throw new Exception(__('Message seems to be a spam.'));
                    }
                    unset($cur);
                }

                if ($settings->smtp_account) {
                    $from = mail::B64Header(str_replace(':', '-', dcCore::app()->blog->name)) . ' <' . $settings->smtp_account . '>';
                } else {
                    $from = mail::B64Header(dcCore::app()->ctx->contactme['name']) . ' <' . dcCore::app()->ctx->contactme['email'] . '>';
                }

                # Sending mail
                $headers = [
                    'From: ' . $from,
                    'Reply-To: ' . mail::B64Header(dcCore::app()->ctx->contactme['name']) . ' <' . dcCore::app()->ctx->contactme['email'] . '>',
                    'Content-Type: text/plain; charset=UTF-8;',
                    'X-Originating-IP: ' . Http::realIP(),
                    'X-Mailer: Dotclear',
                    'X-Blog-Id: ' . mail::B64Header(dcCore::app()->blog->id),
                    'X-Blog-Name: ' . mail::B64Header(dcCore::app()->blog->name),
                    'X-Blog-Url: ' . mail::B64Header(dcCore::app()->blog->url),
                ];

                $subject = dcCore::app()->ctx->contactme['subject'];
                if ($settings->subject_prefix) {
                    $subject = $settings->subject_prefix . ' ' . $subject;
                }
                $subject = mail::B64Header($subject);

                $msg = __("Hi there!\n\nYou received a message from your blog's contact page.") .
                "\n\n" .
                sprintf(__('Blog: %s'), dcCore::app()->blog->name) . "\n" .
                sprintf(__('Message from: %s <%s>'), dcCore::app()->ctx->contactme['name'], dcCore::app()->ctx->contactme['email']) . "\n" .
                sprintf(__('Website: %s'), dcCore::app()->ctx->contactme['site']) . "\n\n" .
                __('Message:') . "\n" .
                "-----------------------------------------------------------\n" .
                dcCore::app()->ctx->contactme['message'] . "\n\n";

                foreach ($recipients as $email) {
                    mail::sendMail($email, $subject, $msg, $headers);
                }
                Http::redirect(dcCore::app()->blog->url . dcCore::app()->url->getURLFor('contactme') . '/sent');
            } catch (Exception $e) {
                dcCore::app()->ctx->contactme['error']     = true;
                dcCore::app()->ctx->contactme['error_msg'] = $e->getMessage();
            }
        }

        $tplset           = dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme, 'tplset');
        $default_template = Path::real(dcCore::app()->plugins->moduleInfo(My::id(), 'root')) . DIRECTORY_SEPARATOR . dcPublic::TPL_ROOT . DIRECTORY_SEPARATOR;
        if (!empty($tplset) && is_dir($default_template . $tplset)) {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), $default_template . $tplset);
        } else {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), $default_template . DC_DEFAULT_TPLSET);
        }

        self::serveDocument('contact_me.html');
        exit;
    }
}

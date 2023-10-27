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
use Dotclear\Core\Frontend\Url;
use Dotclear\Helper\Network\Http;
use Dotclear\Helper\Network\Mail\Mail;
use Dotclear\Helper\Text;
use Dotclear\Plugin\antispam\Antispam;
use Exception;

class FrontendUrl extends Url
{
    /**
     * @param      null|string  $args   The arguments
     */
    public static function contact(?string $args): void
    {
        $settings = My::settings();
        if (!$settings->recipients || !$settings->active) {
            self::p404();
        }

        App::frontend()->context()->contactme = new ArrayObject([
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
            App::frontend()->context()->contactme['sent'] = true;
        } elseif ($send_msg) {
            # Spam trap
            if (!empty($_POST['f_mail'])) {
                Http::head(412, 'Precondition Failed');
                header('Content-Type: text/plain');
                echo 'So Long, and Thanks For All the Fish';
                exit;
            }

            try {
                App::frontend()->context()->contactme['name']    = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_name']);
                App::frontend()->context()->contactme['email']   = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_mail']);
                App::frontend()->context()->contactme['site']    = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_site']);
                App::frontend()->context()->contactme['subject'] = (string) preg_replace('/[\n\r]/', '', (string) $_POST['c_subject']);
                App::frontend()->context()->contactme['message'] = $_POST['c_message'];

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
                    if ($v !== '' && Text::isEmail($v)) {
                        $rc2[] = $v;
                    }
                }

                $recipients = $rc2;
                unset($rc2);

                if ($recipients === []) {
                    throw new Exception(__('No valid contact recipient was found.'));
                }

                # Check message form spam
                if ($settings->use_antispam && class_exists('Antispam')) {
                    # Fake cursor to check spam
                    $cur                    = App::con()->openCursor('foo');
                    $cur->comment_trackback = 0;
                    $cur->comment_author    = App::frontend()->context()->contactme['name'];
                    $cur->comment_email     = App::frontend()->context()->contactme['email'];
                    $cur->comment_site      = App::frontend()->context()->contactme['site'];
                    $cur->comment_ip        = Http::realIP();
                    $cur->comment_content   = App::frontend()->context()->contactme['message'];
                    $cur->post_id           = 0; // That could break things...
                    $cur->comment_status    = App::blog()::COMMENT_PUBLISHED;

                    Antispam::isSpam($cur);

                    if ($cur->comment_status === App::blog()::COMMENT_JUNK) { // @phpstan-ignore-line — Antispam::isSpam() may modify it!
                        unset($cur);

                        throw new Exception(__('Message seems to be a spam.'));
                    }

                    unset($cur);
                }

                if ($settings->smtp_account) {
                    $from = mail::B64Header(str_replace(':', '-', App::blog()->name())) . ' <' . $settings->smtp_account . '>';
                } else {
                    $from = mail::B64Header((string) App::frontend()->context()->contactme['name']) . ' <' . App::frontend()->context()->contactme['email'] . '>';
                }

                # Sending mail
                $headers = [
                    'From: ' . $from,
                    'Reply-To: ' . mail::B64Header((string) App::frontend()->context()->contactme['name']) . ' <' . App::frontend()->context()->contactme['email'] . '>',
                    'Content-Type: text/plain; charset=UTF-8;',
                    'X-Originating-IP: ' . Http::realIP(),
                    'X-Mailer: Dotclear',
                    'X-Blog-Id: ' . mail::B64Header(App::blog()->id()),
                    'X-Blog-Name: ' . mail::B64Header(App::blog()->name()),
                    'X-Blog-Url: ' . mail::B64Header(App::blog()->url()),
                ];

                $subject = App::frontend()->context()->contactme['subject'];
                if ($settings->subject_prefix) {
                    $subject = $settings->subject_prefix . ' ' . $subject;
                }

                $subject = mail::B64Header((string) $subject);

                $msg = __("Hi there!\n\nYou received a message from your blog's contact page.") .
                "\n\n" .
                sprintf(__('Blog: %s'), App::blog()->name()) . "\n" .
                sprintf(__('Message from: %s <%s>'), App::frontend()->context()->contactme['name'], App::frontend()->context()->contactme['email']) . "\n" .
                sprintf(__('Website: %s'), App::frontend()->context()->contactme['site']) . "\n\n" .
                __('Message:') . "\n" .
                "-----------------------------------------------------------\n" .
                App::frontend()->context()->contactme['message'] . "\n\n";

                foreach ($recipients as $email) {
                    mail::sendMail($email, $subject, $msg, $headers);
                }

                Http::redirect(App::blog()->url() . App::url()->getURLFor('contactme') . '/sent');
            } catch (Exception $e) {
                App::frontend()->context()->contactme['error']     = true;
                App::frontend()->context()->contactme['error_msg'] = $e->getMessage();
            }
        }

        App::frontend()->template()->appendPath(My::tplPath());
        self::serveDocument('contact_me.html');
        exit;
    }
}

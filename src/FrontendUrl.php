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

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Url;
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
        if ($settings->getStr('recipients', false) === '' || !$settings->getBool('active')) {
            self::p404();
        }

        // Post data helpers
        $_Str = fn (string $name, string $default = ''): string => isset($_POST[$name]) && is_string($val = $_POST[$name]) ? $val : $default;

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

        if ($args === 'sent') {
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
                $name    = $_Str('c_name');
                $mail    = $_Str('c_mail');
                $site    = $_Str('c_site');
                $subject = $_Str('c_subject');
                $message = $_Str('c_message');

                $name    = str_replace(["\n", "\r"], '', $name);
                $mail    = str_replace(["\n", "\r"], '', $mail);
                $site    = str_replace(["\n", "\r"], '', $site);
                $subject = str_replace(["\n", "\r"], '', $subject);

                App::frontend()->context()->contactme['name']    = $name;
                App::frontend()->context()->contactme['email']   = $mail;
                App::frontend()->context()->contactme['site']    = $site;
                App::frontend()->context()->contactme['subject'] = $subject;
                App::frontend()->context()->contactme['message'] = $message;

                # Checks provided fields
                if ($name === '') {
                    throw new Exception(__('You must provide a name.'));
                }

                if (!Text::isEmail($mail)) {
                    throw new Exception(__('You must provide a valid email address.'));
                }

                if ($subject === '') {
                    throw new Exception(__('You must provide a subject.'));
                }

                if ($message === '') {
                    throw new Exception(__('You must write a message.'));
                }

                # Checks recipients addresses
                $recipients = $settings->getStr('recipients', false);
                $recipients = explode(',', $recipients);
                $rc2        = [];
                foreach ($recipients as $recipient) {
                    $recipient = trim($recipient);
                    if ($recipient !== '' && Text::isEmail($recipient)) {
                        $rc2[] = $recipient;
                    }
                }

                $recipients = $rc2;
                if ($recipients === []) {
                    throw new Exception(__('No valid contact recipient was found.'));
                }

                # Check message form spam
                if ($settings->getBool('use_antispam') && class_exists('Dotclear\Plugin\antispam\Antispam')) {
                    # Fake cursor to check spam
                    $cur = App::db()->con()->openCursor('foo');
                    $cur->setBoolField('comment_trackback', false);
                    $cur->setStrField('comment_author', $name);
                    $cur->setStrField('comment_email', $mail);
                    $cur->setStrField('comment_site', $site);
                    $cur->setStrField('comment_ip', Http::realIP());
                    $cur->setStrField('comment_content', $message);
                    $cur->setIntField('post_id', 0); // That could break things...
                    $cur->setIntField('comment_status', App::status()->comment()::PUBLISHED);

                    Antispam::isSpam($cur);

                    if ($cur->intField('comment_status') === App::status()->comment()::JUNK) {
                        throw new Exception(__('Message seems to be a spam.'));
                    }
                }

                $smtp_account = $settings->getStr('smtp_account', false);
                if ($smtp_account !== '') {
                    $from = Mail::B64Header(str_replace(':', '-', App::blog()->name())) . ' <' . $smtp_account . '>';
                } else {
                    $from = Mail::B64Header($name) . ' <' . $mail . '>';
                }

                # Sending mail
                $headers = [
                    'From: ' . $from,
                    'Reply-To: ' . Mail::B64Header($name) . ' <' . $mail . '>',
                    'Content-Type: text/plain; charset=UTF-8;',
                    'X-Originating-IP: ' . Http::realIP(),
                    'X-Mailer: Dotclear',
                    'X-Blog-Id: ' . Mail::B64Header(App::blog()->id()),
                    'X-Blog-Name: ' . Mail::B64Header(App::blog()->name()),
                    'X-Blog-Url: ' . Mail::B64Header(App::blog()->url()),
                ];

                $prefix = $settings->getStr('subject_prefix', false);
                if ($prefix !== '') {
                    $subject = $prefix . ' ' . $subject;
                }

                $subject = Mail::B64Header($subject);

                $msg = __("Hi there!\n\nYou received a message from your blog's contact page.") .
                "\n\n" .
                sprintf(__('Blog: %s'), App::blog()->name()) . "\n" .
                sprintf(__('Message from: %s <%s>'), $name, $mail) . "\n" .
                sprintf(__('Website: %s'), $site) . "\n\n" .
                __('Message:') . "\n" .
                "-----------------------------------------------------------\n" .
                $message . "\n\n";

                foreach ($recipients as $recipient) {
                    Mail::sendMail($recipient, $subject, $msg, $headers);
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

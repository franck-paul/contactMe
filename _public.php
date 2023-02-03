<?php
/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Olivier Meunier and contributors
 *
 * @copyright Olivier Meunier
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */

# Localized string we find in template
__('Subject');
__('Message');

class extContactMe
{
    public static function publicBreadcrumb($context)
    {
        if ($context == 'contactme') {
            return __('Contact me');
        }
    }
}

dcCore::app()->addBehavior('publicBreadcrumb', [extContactMe::class, 'publicBreadcrumb']);

class urlContactMe extends dcUrlHandlers
{
    public static function contact($args)
    {
        if (!dcCore::app()->blog->settings->contactme->cm_recipients || !dcCore::app()->blog->settings->contactme->active) {
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
                http::head(412, 'Precondition Failed');
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

                if (!text::isEmail($_POST['c_mail'])) {
                    throw new Exception(__('You must provide a valid email address.'));
                }

                if (empty($_POST['c_subject'])) {
                    throw new Exception(__('You must provide a subject.'));
                }

                if (empty($_POST['c_message'])) {
                    throw new Exception(__('You must write a message.'));
                }

                # Checks recipients addresses
                $recipients = explode(',', dcCore::app()->blog->settings->contactme->cm_recipients);
                $rc2        = [];
                foreach ($recipients as $v) {
                    $v = trim((string) $v);
                    if (!empty($v) && text::isEmail($v)) {
                        $rc2[] = $v;
                    }
                }
                $recipients = $rc2;
                unset($rc2);

                if (empty($recipients)) {
                    throw new Exception(__('No valid contact recipient was found.'));
                }

                # Check message form spam
                if (dcCore::app()->blog->settings->contactme->cm_use_antispam && class_exists('dcAntispam') && isset(dcCore::app()->spamfilters)) {
                    # Fake cursor to check spam
                    $cur                    = dcCore::app()->con->openCursor('foo');    // @phpstan-ignore-line
                    $cur->comment_trackback = 0;
                    $cur->comment_author    = dcCore::app()->ctx->contactme['name'];
                    $cur->comment_email     = dcCore::app()->ctx->contactme['email'];
                    $cur->comment_site      = dcCore::app()->ctx->contactme['site'];
                    $cur->comment_ip        = http::realIP();
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

                if (dcCore::app()->blog->settings->contactme->cm_smtp_account) {
                    $from = mail::B64Header(str_replace(':', '-', dcCore::app()->blog->name)) . ' <' . dcCore::app()->blog->settings->contactme->cm_smtp_account . '>';
                } else {
                    $from = mail::B64Header(dcCore::app()->ctx->contactme['name']) . ' <' . dcCore::app()->ctx->contactme['email'] . '>';
                }

                # Sending mail
                $headers = [
                    'From: ' . $from,
                    'Reply-To: ' . mail::B64Header(dcCore::app()->ctx->contactme['name']) . ' <' . dcCore::app()->ctx->contactme['email'] . '>',
                    'Content-Type: text/plain; charset=UTF-8;',
                    'X-Originating-IP: ' . http::realIP(),
                    'X-Mailer: Dotclear',
                    'X-Blog-Id: ' . mail::B64Header(dcCore::app()->blog->id),
                    'X-Blog-Name: ' . mail::B64Header(dcCore::app()->blog->name),
                    'X-Blog-Url: ' . mail::B64Header(dcCore::app()->blog->url),
                ];

                $subject = dcCore::app()->ctx->contactme['subject'];
                if (dcCore::app()->blog->settings->contactme->cm_subject_prefix) {
                    $subject = dcCore::app()->blog->settings->contactme->cm_subject_prefix . ' ' . $subject;
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
                http::redirect(dcCore::app()->blog->url . dcCore::app()->url->getURLFor('contactme') . '/sent');
            } catch (Exception $e) {
                dcCore::app()->ctx->contactme['error']     = true;
                dcCore::app()->ctx->contactme['error_msg'] = $e->getMessage();
            }
        }

        $tplset = dcCore::app()->themes->moduleInfo(dcCore::app()->blog->settings->system->theme, 'tplset');
        if (!empty($tplset) && is_dir(__DIR__ . '/' . dcPublic::TPL_ROOT . '/' . $tplset)) {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/' . dcPublic::TPL_ROOT . '/' . $tplset);
        } else {
            dcCore::app()->tpl->setPath(dcCore::app()->tpl->getPath(), __DIR__ . '/' . dcPublic::TPL_ROOT . '/' . DC_DEFAULT_TPLSET);
        }
        self::serveDocument('contact_me.html');
        exit;
    }
}

class tplContactMe
{
    public static function ContactMeURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("contactme")') . '; ?>';
    }

    public static function ContactMeIf($attr, $content)
    {
        $if = [];

        $operator = isset($attr['operator']) ? dcTemplate::getOperator($attr['operator']) : '&&';

        if (isset($attr['sent'])) {
            $sign = (bool) $attr['sent'] ? '' : '!';
            $if[] = $sign . "dcCore::app()->ctx->contactme['sent']";
        }

        if (isset($attr['error'])) {
            $sign = (bool) $attr['error'] ? '' : '!';
            $if[] = $sign . "dcCore::app()->ctx->contactme['error']";
        }

        if (!empty($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    public static function ContactMePageTitle($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->settings->contactme->cm_page_title') . '; ?>';
    }

    public static function ContactMeFormCaption($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->settings->contactme->cm_form_caption') . '; ?>';
    }

    public static function ContactMeMsgSuccess()
    {
        return '<?php echo dcCore::app()->blog->settings->contactme->cm_msg_success; ?>';
    }

    public static function ContactMeMsgError()
    {
        return '<?php echo sprintf(dcCore::app()->blog->settings->contactme->cm_msg_error,html::escapeHTML(dcCore::app()->ctx->contactme["error_msg"])); ?>';
    }

    public static function ContactMeName($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["name"]') . '; ?>';
    }

    public static function ContactMeEmail($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["email"]') . '; ?>';
    }

    public static function ContactMeSite($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["site"]') . '; ?>';
    }

    public static function ContactMeSubject($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["subject"]') . '; ?>';
    }

    public static function ContactMeMessage($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["message"]') . '; ?>';
    }

    # Widget function
    public static function contactMeWidget($w)
    {
        if ($w->offline) {
            return;
        }

        if (($w->homeonly == 1 && !dcCore::app()->url->isHome(dcCore::app()->url->type)) || ($w->homeonly == 2 && dcCore::app()->url->isHome(dcCore::app()->url->type))) {
            return;
        }

        if (!dcCore::app()->blog->settings->contactme->cm_recipients || !dcCore::app()->blog->settings->contactme->active) {
            return;
        }

        $res = ($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '') .
        '<p><a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('contactme') . '">' .
            ($w->link_title ? html::escapeHTML($w->link_title) : __('Contact me')) .
            '</a></p>';

        return $w->renderDiv($w->content_only, 'contact-me ' . $w->class, '', $res);
    }
}

dcCore::app()->tpl->addValue('ContactMeURL', [tplContactMe::class, 'ContactMeURL']);
dcCore::app()->tpl->addBlock('ContactMeIf', [tplContactMe::class, 'ContactMeIf']);
dcCore::app()->tpl->addValue('ContactMePageTitle', [tplContactMe::class, 'ContactMePageTitle']);
dcCore::app()->tpl->addValue('ContactMeFormCaption', [tplContactMe::class, 'ContactMeFormCaption']);
dcCore::app()->tpl->addValue('ContactMeMsgSuccess', [tplContactMe::class, 'ContactMeMsgSuccess']);
dcCore::app()->tpl->addValue('ContactMeMsgError', [tplContactMe::class, 'ContactMeMsgError']);
dcCore::app()->tpl->addValue('ContactMeName', [tplContactMe::class, 'ContactMeName']);
dcCore::app()->tpl->addValue('ContactMeEmail', [tplContactMe::class, 'ContactMeEmail']);
dcCore::app()->tpl->addValue('ContactMeSite', [tplContactMe::class, 'ContactMeSite']);
dcCore::app()->tpl->addValue('ContactMeSubject', [tplContactMe::class, 'ContactMeSubject']);
dcCore::app()->tpl->addValue('ContactMeMessage', [tplContactMe::class, 'ContactMeMessage']);

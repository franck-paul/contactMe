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
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Span;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Textarea;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Text as TextHelper;
use Exception;

class Manage extends Process
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (isset($_POST['recipients'])) {
            try {
                $active         = !empty($_POST['active']);
                $recipients     = $_POST['recipients'];
                $subject_prefix = $_POST['subject_prefix'];
                $page_title     = $_POST['page_title'];
                $form_caption   = $_POST['form_caption'];
                $msg_success    = $_POST['msg_success'];
                $msg_error      = $_POST['msg_error'];
                $smtp_account   = $_POST['smtp_account'];

                if (empty($_POST['recipients'])) {
                    throw new Exception(__('No recipients.'));
                }

                if (empty($_POST['page_title'])) {
                    throw new Exception(__('No page title.'));
                }

                if (empty($_POST['msg_success'])) {
                    throw new Exception(__('No success message.'));
                }

                if (empty($_POST['msg_error'])) {
                    throw new Exception(__('No error message.'));
                }

                $r  = explode(',', (string) $recipients);
                $r2 = [];

                foreach ($r as $v) {
                    $v = trim($v);
                    if ($v === '') {
                        continue;
                    }

                    if (!TextHelper::isEmail($v)) {
                        throw new Exception(sprintf(__('%s is not a valid e-mail address.'), Html::escapeHTML($v)));
                    }

                    $r2[] = $v;
                }

                $recipients = implode(', ', $r2);

                // Everything's fine, save options
                $settings = My::settings();
                $settings->put('active', $active, 'boolean');
                $settings->put('recipients', $recipients, 'string', 'ContactMe recipients');
                $settings->put('subject_prefix', $subject_prefix, 'string', 'ContactMe subject prefix');
                $settings->put('page_title', $page_title, 'string', 'ContactMe page title');
                $settings->put('form_caption', $form_caption, 'string', 'ContactMe form caption');
                $settings->put('msg_success', $msg_success, 'string', 'ContactMe success message');
                $settings->put('msg_error', $msg_error, 'string', 'ContactMe error message');
                $settings->put('smtp_account', $smtp_account, 'string', 'ContactMe SMTP account');

                if (App::plugins()->moduleExists('antispam')) {
                    $settings->put('use_antispam', !empty($_POST['use_antispam']), 'boolean', 'ContactMe should use comments spam filter');
                }

                App::blog()->triggerBlog();
                Notices::addSuccessNotice(__('Setting have been successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $head        = '';
        $rich_editor = App::auth()->getOption('editor');
        $rte_flag    = true;
        $rte_flags   = @App::auth()->prefs()->interface->rte_flags;
        if (is_array($rte_flags) && in_array('contactme', $rte_flags)) {
            $rte_flag = $rte_flags['contactme'];
        }

        if ($rte_flag) {
            $head = App::behavior()->callBehavior(
                'adminPostEditor',
                $rich_editor['xhtml'],
                'contactme',
                ['#form_caption', '#msg_success', '#msg_error'],
                'xhtml'
            ) .
            My::jsLoad('contactme.js');
        }

        $settings = My::settings();

        $active         = $settings->active;
        $recipients     = $settings->recipients;
        $subject_prefix = $settings->subject_prefix;
        $page_title     = $settings->page_title;
        $form_caption   = $settings->form_caption;
        $msg_success    = $settings->msg_success;
        $msg_error      = $settings->msg_error;
        $use_antispam   = $settings->use_antispam;
        $smtp_account   = $settings->smtp_account;

        $antispam_enabled = App::plugins()->moduleExists('antispam');

        if ($page_title === null) {
            $page_title = __('Contact me');
        }

        if ($form_caption === null) {
            $form_caption = __('<p>You can use the following form to send me an e-mail.</p>');
        }

        if ($msg_success === null) {
            $msg_success = __('<p style="color:green"><strong>Thank you for your message.</strong></p>');
        }

        if ($msg_error === null) {
            $msg_error = __('<p style="color:red"><strong>An error occured:</strong> %s</p>');
        }

        Page::openModule(My::name(), $head);

        echo Page::breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Contact me')                      => '',
            ]
        );
        echo Notices::getNotices();

        // Form

        // Antispam options
        $options = [];
        if ($antispam_enabled) {
            $options[] = (new Para())->items([
                (new Checkbox('use_antispam', (bool) $use_antispam))
                    ->value(1)
                    ->label((new Label(__('Use comments spam filter'), Label::INSIDE_TEXT_AFTER))),
            ]);
        }

        echo (new Form('contactme'))
            ->action(App::backend()->getPageURL())
            ->method('post')
            ->fields([
                (new Para())->items([
                    (new Checkbox('active', $active))
                        ->value(1)
                        ->label((new Label(__('Activate contactMe on blog'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Note())
                    ->class('form-note')
                    ->text(sprintf(__('Fields preceded by %s are mandatory.'), (new Span('*'))->class('required')->render())),
                (new Text('h3', __('E-Mail settings'))),
                (new Para())->items([
                    (new Input('recipients'))
                        ->class('maximal')
                        ->size(30)
                        ->maxlength(512)
                        ->value(Html::escapeHTML($recipients))
                        ->required(true)
                        ->placeholder(__('Email'))
                        ->label((new Label(
                            (new Span('*'))->render() . __('Comma separated recipients list:'),
                            Label::INSIDE_TEXT_BEFORE
                        ))->id('recipients_label')->class('required')->title(__('Required field'))),
                ]),
                (new Para())->items([
                    (new Input('subject_prefix'))
                        ->size(30)
                        ->maxlength(128)
                        ->value($subject_prefix)
                        ->label((new Label(__('E-Mail subject prefix:'), Label::OUTSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->class('form-note')->items([
                    (new Text(null, __('This will be prepend to e-mail subject'))),
                ]),
                (new Para())->items([
                    (new Input('smtp_account'))
                        ->size(64)
                        ->maxlength(512)
                        ->value($smtp_account)
                        ->label((new Label(__('SMTP account (optional):'), Label::OUTSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->class('form-note')->items([
                    (new Text(null, __('This will be use as e-mail sender. Note that the sent e-mails will have a Reply-To filled with your correspondent e-mail.'))),
                ]),
                ...$options,
                (new Text('h3', __('Presentation options'))),
                (new Para())->items([
                    (new Input('page_title'))
                        ->size(30)
                        ->maxlength(256)
                        ->value(Html::escapeHTML($page_title))
                        ->required(true)
                        ->placeholder(__('Title'))
                        ->label((new Label(
                            (new Span('*'))->render() . __('Page title:'),
                            Label::OUTSIDE_TEXT_BEFORE
                        ))->id('page_title_label')->class('required')->title(__('Required field'))),
                ]),
                (new Para())->items([
                    (new Textarea('form_caption'))
                        ->cols(30)
                        ->rows(2)
                        ->lang(App::auth()->getInfo('user_lang'))
                        ->spellcheck(true)
                        ->value(Html::escapeHTML($form_caption))
                        ->label((new Label(__('Form caption:'), Label::OUTSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Textarea('msg_success'))
                        ->cols(30)
                        ->rows(2)
                        ->lang(App::auth()->getInfo('user_lang'))
                        ->spellcheck(true)
                        ->value(Html::escapeHTML($msg_success))
                        ->placeholder(__('Message'))
                        ->label((new Label(
                            (new Span('*'))->render() . __('Confirmation message:'),
                            Label::OUTSIDE_TEXT_BEFORE
                        ))->id('msg_success_label')->class('required')->title(__('Required field'))),
                ]),
                (new Para())->items([
                    (new Textarea('msg_error'))
                        ->cols(30)
                        ->rows(2)
                        ->lang(App::auth()->getInfo('user_lang'))
                        ->spellcheck(true)
                        ->value(Html::escapeHTML($msg_error))
                        ->placeholder(__('Message'))
                        ->label((new Label(
                            (new Span('*'))->render() . __('Error message:'),
                            Label::OUTSIDE_TEXT_BEFORE
                        ))->id('msg_error_label')->class('required')->title(__('Required field'))),
                ]),
                (new Para())->class('form-note')->items([
                    (new Text(null, __('"%s" is the error message.'))),
                ]),
                // Submit
                (new Para())->items([
                    (new Submit(['frmsubmit']))
                        ->value(__('Save')),
                    ... My::hiddenFields(),
                ]),
                (new Para())->class('info')->items([
                    (new Text(null, sprintf(__('Don\'t forget to add a <a href="%s">“Contact Me” widget</a> linking to your contact page.'), App::backend()->url()->get('admin.plugin.widgets')))),
                ]),
            ])
        ->render();

        Page::closeModule();
    }
}

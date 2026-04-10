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
use Dotclear\Helper\Process\TraitProcess;
use Dotclear\Helper\Text as TextHelper;
use Exception;

class Manage
{
    use TraitProcess;

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
                // Post data helpers
                $_Bool = fn (string $name): bool => !empty($_POST[$name]);
                $_Str  = fn (string $name, string $default = ''): string => isset($_POST[$name]) && is_string($val = $_POST[$name]) ? $val : $default;

                $active         = $_Bool('active');
                $recipients     = $_Str('recipients');
                $subject_prefix = $_Str('subject_prefix');
                $page_title     = $_Str('page_title');
                $form_caption   = $_Str('form_caption');
                $msg_success    = $_Str('msg_success');
                $msg_error      = $_Str('msg_error');
                $smtp_account   = $_Str('smtp_account');

                if ($recipients === '') {
                    throw new Exception(__('No recipients.'));
                }

                if ($page_title === '') {
                    throw new Exception(__('No page title.'));
                }

                if ($msg_success === '') {
                    throw new Exception(__('No success message.'));
                }

                if ($msg_error === '') {
                    throw new Exception(__('No error message.'));
                }

                $r  = explode(',', $recipients);
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
                $settings->put('active', $active, App::blogWorkspace()::NS_BOOL);
                $settings->put('recipients', $recipients, App::blogWorkspace()::NS_STRING, 'ContactMe recipients');
                $settings->put('subject_prefix', $subject_prefix, App::blogWorkspace()::NS_STRING, 'ContactMe subject prefix');
                $settings->put('page_title', $page_title, App::blogWorkspace()::NS_STRING, 'ContactMe page title');
                $settings->put('form_caption', $form_caption, App::blogWorkspace()::NS_STRING, 'ContactMe form caption');
                $settings->put('msg_success', $msg_success, App::blogWorkspace()::NS_STRING, 'ContactMe success message');
                $settings->put('msg_error', $msg_error, App::blogWorkspace()::NS_STRING, 'ContactMe error message');
                $settings->put('smtp_account', $smtp_account, App::blogWorkspace()::NS_STRING, 'ContactMe SMTP account');

                if (App::plugins()->moduleExists('antispam')) {
                    $settings->put('use_antispam', $_Bool('use_antispam'), App::blogWorkspace()::NS_BOOL, 'ContactMe should use comments spam filter');
                }

                App::blog()->triggerBlog();
                App::backend()->notices()->addSuccessNotice(__('Setting have been successfully updated.'));
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

        $head      = '';
        $rte_flag  = true;
        $rte_flags = @App::auth()->prefs()->interface->rte_flags;
        if (is_array($rte_flags) && in_array('contactme', $rte_flags)) {
            $rte_flag = $rte_flags['contactme'];
        }

        if ($rte_flag) {
            $rich_editor = App::auth()->getOption('editor');
            if (is_array($rich_editor) && isset($rich_editor['xhtml'])) {
                $head = App::behavior()->callBehavior(
                    'adminPostEditor',
                    $rich_editor['xhtml'],
                    'contactme',
                    ['#form_caption', '#msg_success', '#msg_error'],
                    'xhtml'
                ) .
                My::jsLoad('contactme.js');
            }
        }

        // Variable data helpers
        $_Bool = fn (mixed $var): bool => (bool) $var;
        $_Str  = fn (mixed $var, string $default = ''): string => $var !== null && is_string($val = $var) ? $val : $default;

        $settings = My::settings();

        $active         = $_Bool($settings->active);
        $recipients     = $_Str($settings->recipients);
        $subject_prefix = $_Str($settings->subject_prefix);
        $page_title     = $_Str($settings->page_title);
        $form_caption   = $_Str($settings->form_caption);
        $msg_success    = $_Str($settings->msg_success);
        $msg_error      = $_Str($settings->msg_error);
        $smtp_account   = $_Str($settings->smtp_account);

        $use_antispam     = $_Bool($settings->use_antispam);
        $antispam_enabled = App::plugins()->moduleExists('antispam');

        if ($page_title === '') {
            $page_title = __('Contact me');
        }

        if ($form_caption === '') {
            $form_caption = __('<p>You can use the following form to send me an e-mail.</p>');
        }

        if ($msg_success === '') {
            $msg_success = __('<p style="color:green"><strong>Thank you for your message.</strong></p>');
        }

        if ($msg_error === '') {
            $msg_error = __('<p style="color:red"><strong>An error occured:</strong> %s</p>');
        }

        App::backend()->page()->openModule(My::name(), $head);

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Contact me')                      => '',
            ]
        );
        echo App::backend()->notices()->getNotices();

        // Form

        $user_lang = is_string($user_lang = App::auth()->getInfo('user_lang')) ? $user_lang : 'en';

        // Antispam options
        $options = [];
        if ($antispam_enabled) {
            $options[] = (new Para())->items([
                (new Checkbox('use_antispam', $use_antispam))
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
                    ->class('info')
                    ->text(sprintf(__('The URL for the contact form is: <code>%s</code>'), App::blog()->url() . App::url()->getURLFor('contactme'))),
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
                        ->lang($user_lang)
                        ->spellcheck(true)
                        ->value(Html::escapeHTML($form_caption))
                        ->label((new Label(__('Form caption:'), Label::OUTSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Textarea('msg_success'))
                        ->cols(30)
                        ->rows(2)
                        ->lang($user_lang)
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
                        ->lang($user_lang)
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

        App::backend()->page()->closeModule();
    }
}

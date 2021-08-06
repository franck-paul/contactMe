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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$cm_active         = $core->blog->settings->contactme->active;
$cm_recipients     = $core->blog->settings->contactme->cm_recipients;
$cm_subject_prefix = $core->blog->settings->contactme->cm_subject_prefix;
$cm_page_title     = $core->blog->settings->contactme->cm_page_title;
$cm_form_caption   = $core->blog->settings->contactme->cm_form_caption;
$cm_msg_success    = $core->blog->settings->contactme->cm_msg_success;
$cm_msg_error      = $core->blog->settings->contactme->cm_msg_error;
$cm_use_antispam   = $core->blog->settings->contactme->cm_use_antispam;
$cm_smtp_account   = $core->blog->settings->contactme->cm_smtp_account;

$antispam_enabled = $core->plugins->moduleExists('antispam');

if ($cm_page_title === null) {
    $cm_page_title = __('Contact me');
}

if ($cm_form_caption === null) {
    $cm_form_caption = __('<p>You can use the following form to send me an e-mail.</p>');
}

if ($cm_msg_success === null) {
    $cm_msg_success = __('<p style="color:green"><strong>Thank you for your message.</strong></p>');
}

if ($cm_msg_error === null) {
    $cm_msg_error = __('<p style="color:red"><strong>An error occured:</strong> %s</p>');
}

if (isset($_POST['cm_recipients'])) {
    try {
        $cm_active         = !empty($_POST['cm_active']);
        $cm_recipients     = $_POST['cm_recipients'];
        $cm_subject_prefix = $_POST['cm_subject_prefix'];
        $cm_page_title     = $_POST['cm_page_title'];
        $cm_form_caption   = $_POST['cm_form_caption'];
        $cm_msg_success    = $_POST['cm_msg_success'];
        $cm_msg_error      = $_POST['cm_msg_error'];
        $cm_smtp_account   = $_POST['cm_smtp_account'];

        if (empty($_POST['cm_recipients'])) {
            throw new Exception(__('No recipients.'));
        }

        if (empty($_POST['cm_page_title'])) {
            throw new Exception(__('No page title.'));
        }

        if (empty($_POST['cm_msg_success'])) {
            throw new Exception(__('No success message.'));
        }

        if (empty($_POST['cm_msg_error'])) {
            throw new Exception(__('No error message.'));
        }

        $cm_r  = explode(',', $cm_recipients);
        $cm_r2 = [];

        foreach ($cm_r as $v) {
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            if (!text::isEmail($v)) {
                throw new Exception(sprintf(__('%s is not a valid e-mail address.'), html::escapeHTML($v)));
            }
            $cm_r2[] = $v;
        }
        $cm_recipients = implode(', ', $cm_r2);

        # Everything's fine, save options
        $core->blog->settings->addNamespace('contactme');
        $core->blog->settings->contactme->put('active', $cm_active, 'boolean');
        $core->blog->settings->contactme->put('cm_recipients', $cm_recipients, 'string', 'ContactMe recipients');
        $core->blog->settings->contactme->put('cm_subject_prefix', $cm_subject_prefix, 'string', 'ContactMe subject prefix');
        $core->blog->settings->contactme->put('cm_page_title', $cm_page_title, 'string', 'ContactMe page title');
        $core->blog->settings->contactme->put('cm_form_caption', $cm_form_caption, 'string', 'ContactMe form caption');
        $core->blog->settings->contactme->put('cm_msg_success', $cm_msg_success, 'string', 'ContactMe success message');
        $core->blog->settings->contactme->put('cm_msg_error', $cm_msg_error, 'string', 'ContactMe error message');
        $core->blog->settings->contactme->put('cm_smtp_account', $cm_smtp_account, 'string', 'ContactMe SMTP account');

        if ($antispam_enabled) {
            $core->blog->settings->contactme->put('cm_use_antispam', !empty($_POST['cm_use_antispam']), 'boolean', 'ContactMe should use comments spam filter');
        }

        $core->blog->triggerBlog();
        dcPage::addSuccessNotice(__('Setting have been successfully updated.'));
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

?>
<html>
<head>
  <title><?php echo __('Contact me'); ?></title>
<?php
$rich_editor = $core->auth->getOption('editor');
$rte_flag    = true;
$rte_flags   = @$core->auth->user_prefs->interface->rte_flags;
if (is_array($rte_flags) && in_array('contactme', $rte_flags)) {
    $rte_flag = $rte_flags['contactme'];
}
if ($rte_flag) {
    echo
    $core->callBehavior('adminPostEditor', $rich_editor['xhtml'], 'contactme',
        ['#cm_form_caption', '#cm_msg_success', '#cm_msg_error'], 'xhtml') .
    dcPage::jsLoad(urldecode(dcPage::getPF('contactMe/contactme.js')));
}
?>
</head>

<body>
<?php
echo dcPage::breadcrumb(
    [
        html::escapeHTML($core->blog->name) => '',
        __('Contact me')                    => ''
    ]);
echo dcPage::notices();

echo
'<form action="' . $p_url . '" method="post">' .
'<p>' . form::checkbox('cm_active', 1, $cm_active) . ' ' .
'<label for="cm_active" class="classic">' . __('Activate contactMe on blog') . '</label></p>' .
'<h3>' . __('E-Mail settings') . '</h3>' .
'<p><label for="cm_recipients" class="required" title="' . __('Required field') . '">' . __('Comma separated recipients list:') . '</label> ' .
form::field('cm_recipients', 30, 512, html::escapeHTML($cm_recipients), 'maximal', '', false, 'required placeholder="' . __('Email') . '"') . '</p>' .
'<p><label for="cm_subject_prefix">' . __('E-Mail subject prefix:') . '</label> ' .
form::field('cm_subject_prefix', 30, 128, html::escapeHTML($cm_subject_prefix)) . '</p>' .
'<p class="form-note">' . __('This will be prepend to e-mail subject') . '</p>' .
'<p><label for="cm_smtp_account">' . __('SMTP account (optional):') . '</label> ' .
form::field('cm_smtp_account', 30, 512, html::escapeHTML($cm_smtp_account), 'maximal', '', false) . '</p>' .
'<p class="form-note">' . __('This will be use as e-mail sender. Note that the sent e-mails will have a Reply-To filled with your correspondent e-mail.') . '</p>';

# Antispam options
if ($antispam_enabled) {
    echo
    '<p>' . form::checkbox('cm_use_antispam', 1, (boolean) $cm_use_antispam) .
    ' <label for="cm_use_antispam" class="classic">' . __('Use comments spam filter') . '</label></p>';
}

echo
'<h3>' . __('Presentation options') . '</h3>' .
'<p><label for="cm_page_title" class="required" title="' . __('Required field') . '"><abbr title="' . __('Required field') . '">*</abbr> ' .
__('Page title:') . '</label> ' .
form::field('cm_page_title', 30, 256, html::escapeHTML($cm_page_title), '', '', false, 'required placeholder="' . __('Title') . '"') .
'</p>' .
'<p class="area"><label for="cm_form_caption">' . __('Form caption:') . '</label> ' .
form::textarea('cm_form_caption', 30, 2, html::escapeHTML($cm_form_caption)) .
'</p>' .
'<p class="area"><label for="cm_msg_success" class="required" title="' . __('Required field') . '"><abbr title="' . __('Required field') . '">*</abbr> ' .
__('Confirmation message:') . '</label> ' .
form::textarea('cm_msg_success', 30, 2, html::escapeHTML($cm_msg_success), '', '', false, 'required placeholder="' . __('Message') . '"') .
'</p>' .
'<p class="area"><label for="cm_msg_error" class="required" title="' . __('Required field') . '"><abbr title="' . __('Required field') . '">*</abbr> ' .
__('Error message:') . '</label> ' .
form::textarea('cm_msg_error', 30, 2, html::escapeHTML($cm_msg_error), '', '', false, 'required placeholder="' . __('Message') . '"') .
'</p>' .
'<p class="form-note">' . __('"%s" is the error message.') . '</p>' .

'<p>' . $core->formNonce() . '<input type="submit" value="' . __('Save') . '" /></p>' .
    '</form>';

echo '<p class="info">' . sprintf(__('Don\'t forget to add a <a href="%s">“Contact Me” widget</a> linking to your contact page.'), 'plugin.php?p=widgets') . '</p>';
?>
</body>
</html>

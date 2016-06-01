<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of contactMe, a plugin for Dotclear 2.
#
# Copyright (c) Olivier Meunier and contributors
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) { return; }

# Localized string we find in template
__('Subject');
__('Message');

$core->tpl->addValue('ContactMeURL',array('tplContactMe','ContactMeURL'));
$core->tpl->addBlock('ContactMeIf',array('tplContactMe','ContactMeIf'));
$core->tpl->addValue('ContactMePageTitle',array('tplContactMe','ContactMePageTitle'));
$core->tpl->addValue('ContactMeFormCaption',array('tplContactMe','ContactMeFormCaption'));
$core->tpl->addValue('ContactMeMsgSuccess',array('tplContactMe','ContactMeMsgSuccess'));
$core->tpl->addValue('ContactMeMsgError',array('tplContactMe','ContactMeMsgError'));
$core->tpl->addValue('ContactMeName',array('tplContactMe','ContactMeName'));
$core->tpl->addValue('ContactMeEmail',array('tplContactMe','ContactMeEmail'));
$core->tpl->addValue('ContactMeSite',array('tplContactMe','ContactMeSite'));
$core->tpl->addValue('ContactMeSubject',array('tplContactMe','ContactMeSubject'));
$core->tpl->addValue('ContactMeMessage',array('tplContactMe','ContactMeMessage'));

$core->addBehavior('publicBreadcrumb',array('extContactMe','publicBreadcrumb'));

class extContactMe
{
	public static function publicBreadcrumb($context,$separator)
	{
		if ($context == 'contactme') {
			return __('Contact me');
		}
	}
}

class urlContactMe extends dcUrlHandlers
{
	public static function contact($args)
	{
		global $core, $_ctx;

		if (!$core->blog->settings->contactme->cm_recipients) {
			self::p404();
			exit;
		}

		$_ctx->contactme = new ArrayObject(array(
			'name' => '',
			'email' => '',
			'site' => '',
			'subject' => '',
			'message' => '',
			'sent' => false,
			'error' => false,
			'error_msg' => ''
		));

		$send_msg =
			isset($_POST['c_name']) && isset($_POST['c_mail']) &&
			isset($_POST['c_site']) && isset($_POST['c_message']) &&
			isset($_POST['c_subject']);

		if ($args == 'sent')
		{
			$_ctx->contactme['sent'] = true;
		}
		elseif ($send_msg)
		{
			# Spam trap
			if (!empty($_POST['f_mail'])) {
				http::head(412,'Precondition Failed');
				header('Content-Type: text/plain');
				echo "So Long, and Thanks For All the Fish";
				exit;
			}

			try
			{
				$_ctx->contactme['name'] = preg_replace('/[\n\r]/','',$_POST['c_name']);
				$_ctx->contactme['email'] = preg_replace('/[\n\r]/','',$_POST['c_mail']);
				$_ctx->contactme['site'] = preg_replace('/[\n\r]/','',$_POST['c_site']);
				$_ctx->contactme['subject'] = preg_replace('/[\n\r]/','',$_POST['c_subject']);
				$_ctx->contactme['message'] = $_POST['c_message'];

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
				$recipients = explode(',',$core->blog->settings->contactme->cm_recipients);
				$rc2 = array();
				foreach ($recipients as $v) {
					$v = trim($v);
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
				if ($core->blog->settings->contactme->cm_use_antispam && class_exists('dcAntispam') && isset($core->spamfilters))
				{
					# Fake cursor to check spam
					$cur = $core->con->openCursor('foo');
					$cur->comment_trackback = 0;
					$cur->comment_author = $_ctx->contactme['name'];
					$cur->comment_email = $_ctx->contactme['email'];
					$cur->comment_site = $_ctx->contactme['site'];
					$cur->comment_ip = http::realIP();
					$cur->comment_content = $_ctx->contactme['message'];
					$cur->post_id = 0; // That could break things...
					$cur->comment_status = 1;

					@dcAntispam::isSpam($cur);

					if ($cur->comment_status == -2) {
						unset($cur);
						throw new Exception(__('Message seems to be a spam.'));
					}
					unset($cur);
				}

				# Sending mail
				$headers = array(
					'From: '.mail::B64Header($_ctx->contactme['name']).' <'.$_ctx->contactme['email'].'>',
					'Content-Type: text/plain; charset=UTF-8;',
					'X-Originating-IP: '.http::realIP(),
					'X-Mailer: Dotclear',
					'X-Blog-Id: '.mail::B64Header($core->blog->id),
					'X-Blog-Name: '.mail::B64Header($core->blog->name),
					'X-Blog-Url: '.mail::B64Header($core->blog->url)
				);

				$subject = $_ctx->contactme['subject'];
				if ($core->blog->settings->contactme->cm_subject_prefix) {
					$subject = $core->blog->settings->contactme->cm_subject_prefix.' '.$subject;
				}
				$subject = mail::B64Header($subject);

				$msg =
				__("Hi there!\n\nYou received a message from your blog's contact page.").
				"\n\n".
				sprintf(__('Blog: %s'),$core->blog->name)."\n".
				sprintf(__('Message from: %s <%s>'),$_ctx->contactme['name'],$_ctx->contactme['email'])."\n".
				sprintf(__('Website: %s'),$_ctx->contactme['site'])."\n\n".
				__('Message:')."\n".
				"-----------------------------------------------------------\n".
				$_ctx->contactme['message']."\n\n";

				foreach ($recipients as $email) {
					mail::sendMail($email,$subject,$msg,$headers);
				}
				http::redirect($core->blog->url.$core->url->getBase('contactme').'/sent');
			}
			catch (Exception $e)
			{
				$_ctx->contactme['error'] = true;
				$_ctx->contactme['error_msg'] = $e->getMessage();
			}
		}

		$tplset = $core->themes->moduleInfo($core->blog->settings->system->theme,'tplset');
		if (!empty($tplset) && is_dir(dirname(__FILE__).'/default-templates/'.$tplset)) {
			$core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__).'/default-templates/'.$tplset);
		} else {
			$core->tpl->setPath($core->tpl->getPath(), dirname(__FILE__).'/default-templates/'.DC_DEFAULT_TPLSET);
		}
		self::serveDocument('contact_me.html');
		exit;
	}
}

class tplContactMe
{
	public static function ContactMeURL($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$core->blog->url.$core->url->getBase("contactme")').'; ?>';
	}

	public static function ContactMeIf($attr,$content)
	{
		$if = array();

		$operator = isset($attr['operator']) ? dcTemplate::getOperator($attr['operator']) : '&&';

		if (isset($attr['sent'])) {
			$sign = (boolean) $attr['sent'] ? '' : '!';
			$if[] = $sign."\$_ctx->contactme['sent']";
		}

		if (isset($attr['error'])) {
			$sign = (boolean) $attr['error'] ? '' : '!';
			$if[] = $sign."\$_ctx->contactme['error']";
		}

		if (!empty($if)) {
			return '<?php if('.implode(' '.$operator.' ',$if).') : ?>'.$content.'<?php endif; ?>';
		} else {
			return $content;
		}
	}

	public static function ContactMePageTitle($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$core->blog->settings->contactme->cm_page_title').'; ?>';
	}

	public static function ContactMeFormCaption($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$core->blog->settings->contactme->cm_form_caption').'; ?>';
	}

	public static function ContactMeMsgSuccess($attr)
	{
		return '<?php echo $core->blog->settings->contactme->cm_msg_success; ?>';
	}

	public static function ContactMeMsgError($attr)
	{
		return '<?php echo sprintf($core->blog->settings->contactme->cm_msg_error,html::escapeHTML($_ctx->contactme["error_msg"])); ?>';
	}

	public static function ContactMeName($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$_ctx->contactme["name"]').'; ?>';
	}

	public static function ContactMeEmail($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$_ctx->contactme["email"]').'; ?>';
	}

	public static function ContactMeSite($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$_ctx->contactme["site"]').'; ?>';
	}

	public static function ContactMeSubject($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$_ctx->contactme["subject"]').'; ?>';
	}

	public static function ContactMeMessage($attr)
	{
		$f = $GLOBALS['core']->tpl->getFilters($attr);
		return '<?php echo '.sprintf($f,'$_ctx->contactme["message"]').'; ?>';
	}

	# Widget function
	public static function contactMeWidget($w)
	{
		global $core;

		if ($w->offline)
			return;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default')) {
			return;
		}

		if (!$core->blog->settings->contactme->cm_recipients) {
			return;
		}

		$res =
		($w->title ? $w->renderTitle(html::escapeHTML($w->title)) : '').
		'<p><a href="'.$core->blog->url.$core->url->getBase('contactme').'">'.
		($w->link_title ? html::escapeHTML($w->link_title) : __('Contact me')).
		'</a></p>';

		return $w->renderDiv($w->content_only,'contact-me '.$w->class,'',$res);
	}
}

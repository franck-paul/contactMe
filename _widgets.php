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

$core->addBehavior('initWidgets',array('contactMeWidgets','initWidgets'));

class contactMeWidgets
{
	public static function initWidgets($w)
	{
		$w->create('contactMe',__('Contact me'),array('tplContactMe','contactMeWidget'),null,__('Link to the contact form'));
		$w->contactMe->setting('title',__('Title:'),__('Contact'));
		$w->contactMe->setting('link_title',__('Link title:'),__('Contact me'));
		$w->contactMe->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$w->contactMe->setting('content_only',__('Content only'),0,'check');
		$w->contactMe->setting('class',__('CSS class:'),'');
		$w->contactMe->setting('offline',__('Offline'),0,'check');
	}
}

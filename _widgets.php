<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('initWidgets',array('contactMeWidgets','initWidgets'));

class contactMeWidgets
{
	public static function initWidgets($w)
	{
		$w->create('contactMe',__('Contact me'),array('tplContactMe','contactMeWidget'));
		$w->contactMe->setting('title',__('Title:'),__('Contact'));
		$w->contactMe->setting('link_title',__('Link title:'),__('Contact me'));
		$w->contactMe->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$w->contactMe->setting('class',__('CSS class:'),'');
	}
}
?>
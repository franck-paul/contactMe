<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2011 Olivier Meunier and dcTeam
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }

$_menu['Plugins']->addItem(__('Contact me'),'plugin.php?p=contactMe','index.php?pf=contactMe/icon.png',
		preg_match('/plugin.php\?p=contactMe(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('admin',$core->blog->id));
		
$core->addBehavior('adminSimpleMenuAddType',array('contactMeSimpleMenu','adminSimpleMenuAddType'));
$core->addBehavior('adminSimpleMenuBeforeEdit',array('contactMeSimpleMenu','adminSimpleMenuBeforeEdit'));

class contactMeSimpleMenu {

	public static function adminSimpleMenuAddType($items) {
		$items['contactme'] = array(__('Contact me'),false);
	}

	public static function adminSimpleMenuBeforeEdit($item_type,$item_select,$item_label,$item_descr,$item_url,$item_select_label) {
		if ($item_type == 'contactme') {
			$item_label = __('Contact me');
			$item_descr = __('Mail contact form');
			$item_url .= $core->url->getBase('contactme');
		}
	}

}
?>
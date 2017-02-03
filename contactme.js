/*
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of contactMe, a plugin for Dotclear 2.
#
# Copyright (c) Olivier Meunier and contributors
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------
*/

$(function() {
	// HTML text editor
	if ($.isFunction(jsToolBar)) {
		$('p.area textarea').each(function() {
			var tbWidgetText = new jsToolBar(this);
			tbWidgetText.context = 'contactme';
			tbWidgetText.draw('xhtml');
		});
	}
});

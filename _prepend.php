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

$core->url->register('contactme','contact','^contact(?:/(.+))?$',array('urlContactMe','contact'));

require dirname(__FILE__).'/_widgets.php';

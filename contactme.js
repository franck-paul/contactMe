/*global $, jsToolBar */
'use strict';

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

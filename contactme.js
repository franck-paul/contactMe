/*global $, jsToolBar, dotclear */
'use strict';

$(() => {
  // HTML text editor
  if (typeof jsToolBar === 'function') {
    $('p.area textarea').each(function () {
      dotclear.tbWidgetText = new jsToolBar(this);
      dotclear.tbWidgetText.context = 'contactme';
      dotclear.tbWidgetText.draw('xhtml');
    });
  }
});

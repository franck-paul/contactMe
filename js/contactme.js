/*global jsToolBar, dotclear */
'use strict';

dotclear.ready(() => {
  // HTML text editor
  if (typeof jsToolBar === 'function') {
    for (const elt of document.querySelectorAll('#contactme textarea')) {
      dotclear.tbWidgetText = new jsToolBar(elt);
      dotclear.tbWidgetText.context = 'contactme';
      dotclear.tbWidgetText.draw('xhtml');
    }
  }
});

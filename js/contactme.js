/*global dotclear */
'use strict';

dotclear.ready(() => {
  // HTML text editor
  if (typeof dotclear.ToolBar === 'function') {
    for (const elt of document.querySelectorAll('#contactme textarea')) {
      dotclear.tbWidgetText = new dotclear.ToolBar(elt);
      dotclear.tbWidgetText.context = 'contactme';
      dotclear.tbWidgetText.draw('xhtml');
    }
  }
});

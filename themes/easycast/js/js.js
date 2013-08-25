/**
 * @todo попытка решить проблему загрузки файлов в Safari. Удалить если не пригодится. 
 */

function closeKeepAlive() {
  if (/AppleWebKit|MSIE/.test(navigator.userAgent)) {
    new Ajax.Request("/site/close", { asynchronous:false });
  }
}

var Document = {
  initialize: function(){
    $$('form[enctype="multipart/form-data" ]').each(function(uploadForm) {
      uploadForm.observe('submit', closeKeepAlive);
    });
  }
}

Event.observe(document, 'dom:loaded', Document.initialize);
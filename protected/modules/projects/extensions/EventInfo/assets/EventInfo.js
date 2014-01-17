/**
 * Скрипты для работы с мероприятием
 */

// twitter bootstrap содержит небольшой баг, из-за которого некоторые свойства виджета popover
// не работают как надо. Этот код исправляет эту проблему.
var tmp = $.fn.popover.Constructor.prototype.show
$.fn.popover.Constructor.prototype.show = function () {
      var $e = this.$element
      if (typeof($e.attr('data-html')) != 'undefined')this.options.html = ($e.attr('data-html')==='true') 
      if (typeof($e.attr('data-placement')) != 'undefined')this.options.placement = $e.attr('data-placement');
      if (typeof($e.attr('data-selector')) != 'undefined')this.options.selector = $e.attr('data-selector');
      if (typeof($e.attr('data-toggle')) != 'undefined')this.options.selector = $e.attr('data-toggle');
      /* add other options here */
      tmp.call(this);
}

/**
 * Связать кнопку c popover-элементом
 * @param <string> buttonType - тип кнопки:
 *     join        - участвовать
 *     requests    - мои заявки
 *     projectInfo - о проекте
 * @param <string> buttonId  - id кнопки
 * @param <string> popoverId - id контейнера, с содержимым для popover-элемента
 * @param <string> placement - где располагать подсказку (см. документацию Twitter Bootstrap)
 */
/*jQuery.fn.extend({
    ecEventInfoInitPopover: function(buttonType, buttonId, popoverId, placement)
    {
        $(buttonId).popover({
            content   : $(popoverId).html(),
            html      : true,
            placement : placement,
        });
        $(buttonId).click(function(){
            return false;
        });
        return;
    }
});*/


/**
 * Добавляет новый элемент к стандартным значениям одного поля формы
 * @param selectId
 * @param textFieldId
 * @param buttonId
 */
function q_add_custom_activity(selectId, textFieldId, buttonId)
{
	$(document).ready(function() {

		$("#"+buttonId).click(function() {

			var value = $("#"+textFieldId).val();
			if ( ! $.trim(value) )
		    {// пустые значения в список не добавляем
			    return false;
		    }
			var $option = $("<option></option>").text(value).attr("selected", true); 

			$("#"+selectId).append($option).change();
			$("#"+textFieldId).val('');

			return false; 
		}); 
	});
}

/**
 * Скрыть select-список выбора элемента для тех полей где он не нужен
 * @param selectId
 */
function q_hide_jamselect(selectId)
{	
	$("#"+selectId).hide();
}
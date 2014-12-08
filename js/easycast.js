
//////////////////////////////////////////////
/// Методы для работы с JS API CActiveForm ///
//////////////////////////////////////////////

/**
 * Get attribute name form input 
 * example: from "TemplateOptionsForm[moreInformation][3][name]" i want to  extract "moreInformation"
 * 
 * @param  name
 * @return string
 */
function getAttributeNameFromName(name)
{
    var result = name.match(/^[^\[]+\[(\w+)\].+$/);
    if ( result === null )
    {
        return null;
    } else
    {
        return result[1];
    }
}

/**
 * 
 * @param formObj
 */
function triggerAjaxValidation(formObj)
{
    var delay = 40;
    setTimeout(function()
    {
        var settings = formObj.data('settings');
        $.each(settings.attributes, function()
        {
            this.status = 2; // force ajax validation
        });
        formObj.data('settings', settings);
        
        $.fn.yiiactiveform.validate(formObj, function(data)
        {
            $.each(settings.attributes, function()
            {
                $.fn.yiiactiveform.updateInput(this, data, formObj);
            });
            $.fn.yiiactiveform.updateSummary(formObj, data);
        });
    }, delay);
}

/**
 * 
 * @param formObj
 */
function triggerAjaxValidationEvent(formObj)
{
    var delay = 40;
    setTimeout(function()
    {
        var settings = formObj.data('settings');
        $.each(settings.attributes, function()
        {
            this.status = 2; // force ajax validation
        });
        formObj.data('settings', settings);
        
        $.fn.yiiactiveform.validate(formObj, function(data)
        {
            var hasError = false;
            $.each(settings.attributes, function()
            {
                var result = $.fn.yiiactiveform.updateInput(this, data, formObj);
                if ( result )
                {
                    hasError = true;
                }
            });
            var result = {
                'responce': data,
                'form': formObj,
            }
            $.fn.yiiactiveform.updateSummary(formObj, data);
            if ( hasError )
            {
                $("body").trigger('yiiFormError', result);
            }else
            {
                $("body").trigger('yiiFormSuccess', result);
            }
        });
    }, delay);
}

/**
 * 
 * @param formObj
 * @param attributeName
 */
function triggerAjaxValidationForAttribute(formObj, attributeName)
{
    var delay = 40;
    setTimeout(function()
    {
        var settings = formObj.data('settings');
        $.each(settings.attributes, function()
        {
            if ( this.name === attributeName )
            {
                this.status = 2; // force ajax validation
            }
        });
        formObj.data('settings', settings);
        $.fn.yiiactiveform.validate(formObj, function(data)
        {
            $.each(settings.attributes, function()
            {
                if ( this.name === attributeName )
                {
                    $.fn.yiiactiveform.updateInput(this, data, formObj);
                }
            });
            $.fn.yiiactiveform.updateSummary(formObj, data);
        });
    }, delay);
}
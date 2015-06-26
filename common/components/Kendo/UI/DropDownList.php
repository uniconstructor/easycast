<?php

namespace Kendo\UI;

class DropDownList extends \Kendo\UI\Widget {
    protected function name() {
        return 'DropDownList';
    }
    
    protected function createElement() {
        return new \Kendo\Html\Element('input', true);
    }    
//>> Properties

    /**
    * Configures the opening and closing animations of the suggestion popup. Setting the animation option to false will disable the opening and closing animations. As a result the suggestion popup will open and close instantly.
    * @param \Kendo\UI\DropDownListAnimation|array $value
    * @return \Kendo\UI\DropDownList
    */
    public function animation($value) {
        return $this->setProperty('animation', $value);
    }

    /**
    * Controls whether to bind the widget to the data source on initialization.
    * @param boolean $value
    * @return \Kendo\UI\DropDownList
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Use it to set the Id of the parent DropDownList widget.
Help topic showing how cascading functionality works
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function cascadeFrom($value) {
        return $this->setProperty('cascadeFrom', $value);
    }

    /**
    * Defines the field to be used to filter the data source. If not defined the parent's dataValueField option will be used.
Help topic showing how cascading functionality works
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function cascadeFromField($value) {
        return $this->setProperty('cascadeFromField', $value);
    }

    /**
    * Sets the data source of the DropDownList.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\UI\DropDownList
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * The field of the data item that provides the text content of the list items. The widget will filter the data source based on this field.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function dataTextField($value) {
        return $this->setProperty('dataTextField', $value);
    }

    /**
    * The field of the data item that provides the value of the widget.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function dataValueField($value) {
        return $this->setProperty('dataValueField', $value);
    }

    /**
    * Specifies the delay in milliseconds before the search-text typed by the end user is cleared.
    * @param float $value
    * @return \Kendo\UI\DropDownList
    */
    public function delay($value) {
        return $this->setProperty('delay', $value);
    }

    /**
    * If set to false the widget will be disabled and will not allow user input. The widget is enabled by default and allows user input.
    * @param boolean $value
    * @return \Kendo\UI\DropDownList
    */
    public function enable($value) {
        return $this->setProperty('enable', $value);
    }

    /**
    * The filtering method used to determine the suggestions for the current value. Filtration is turned off by default.
The supported filter values are startswith, endswith and contains.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function filter($value) {
        return $this->setProperty('filter', $value);
    }

    /**
    * Sets the fixedGroupTemplate option of the DropDownList.
    * The template used to render the fixed header group. By default the widget displays only the value of the current group.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function fixedGroupTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('fixedGroupTemplate', $value);
    }

    /**
    * Sets the fixedGroupTemplate option of the DropDownList.
    * The template used to render the fixed header group. By default the widget displays only the value of the current group.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function fixedGroupTemplate($value) {
        return $this->setProperty('fixedGroupTemplate', $value);
    }

    /**
    * Sets the groupTemplate option of the DropDownList.
    * The template used to render the groups. By default the widget displays only the value of the group.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function groupTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('groupTemplate', $value);
    }

    /**
    * Sets the groupTemplate option of the DropDownList.
    * The template used to render the groups. By default the widget displays only the value of the group.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function groupTemplate($value) {
        return $this->setProperty('groupTemplate', $value);
    }

    /**
    * The height of the suggestion popup in pixels. The default value is 200 pixels.
    * @param float $value
    * @return \Kendo\UI\DropDownList
    */
    public function height($value) {
        return $this->setProperty('height', $value);
    }

    /**
    * If set to false case-sensitive search will be performed to find suggestions. The widget performs case-insensitive searching by default.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function ignoreCase($value) {
        return $this->setProperty('ignoreCase', $value);
    }

    /**
    * The index of the initially selected item. The index is 0 based.
    * @param float $value
    * @return \Kendo\UI\DropDownList
    */
    public function index($value) {
        return $this->setProperty('index', $value);
    }

    /**
    * The minimum number of characters the user must type before a filter is performed. Set to higher value than 1 if the search could match a lot of items.
    * @param float $value
    * @return \Kendo\UI\DropDownList
    */
    public function minLength($value) {
        return $this->setProperty('minLength', $value);
    }

    /**
    * The options that will be used for the popup initialization. For more details about the available options
refer to Popup documentation.
    * @param  $value
    * @return \Kendo\UI\DropDownList
    */
    public function popup($value) {
        return $this->setProperty('popup', $value);
    }

    /**
    * Define the text of the default empty item. If the value is an object, then the widget will use it as a valid data item.
 Note that the optionLabel will not be available if the widget is empty.
    * @param string| $value
    * @return \Kendo\UI\DropDownList
    */
    public function optionLabel($value) {
        return $this->setProperty('optionLabel', $value);
    }

    /**
    * Sets the optionLabelTemplate option of the DropDownList.
    * The template used to render the option label.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function optionLabelTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('optionLabelTemplate', $value);
    }

    /**
    * Sets the optionLabelTemplate option of the DropDownList.
    * The template used to render the option label.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function optionLabelTemplate($value) {
        return $this->setProperty('optionLabelTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the DropDownList.
    * Specifies a static HTML content, which will be rendered as a header of the popup element.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function headerTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the headerTemplate option of the DropDownList.
    * Specifies a static HTML content, which will be rendered as a header of the popup element.
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function headerTemplate($value) {
        return $this->setProperty('headerTemplate', $value);
    }

    /**
    * Sets the template option of the DropDownList.
    * The template used to render the items. By default the widget displays only the text of the data item (configured via dataTextField).
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the DropDownList.
    * The template used to render the items. By default the widget displays only the text of the data item (configured via dataTextField).
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * Sets the valueTemplate option of the DropDownList.
    * The valueTemplate used to render the selected value. By default the widget displays only the text of the data item (configured via dataTextField).
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\UI\DropDownList
    */
    public function valueTemplateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('valueTemplate', $value);
    }

    /**
    * Sets the valueTemplate option of the DropDownList.
    * The valueTemplate used to render the selected value. By default the widget displays only the text of the data item (configured via dataTextField).
    * @param string $value The template content.
    * @return \Kendo\UI\DropDownList
    */
    public function valueTemplate($value) {
        return $this->setProperty('valueTemplate', $value);
    }

    /**
    * The text of the widget used when the autoBind is set to false.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function text($value) {
        return $this->setProperty('text', $value);
    }

    /**
    * The value of the widget.
    * @param string $value
    * @return \Kendo\UI\DropDownList
    */
    public function value($value) {
        return $this->setProperty('value', $value);
    }

    /**
    * Specifies the value binding behavior for the widget when the initial model value is null. If set to true, the View-Model field will be updated with the selected item value field. If set to false, the View-Model field will be updated with the selected item.
    * @param boolean $value
    * @return \Kendo\UI\DropDownList
    */
    public function valuePrimitive($value) {
        return $this->setProperty('valuePrimitive', $value);
    }

    /**
    * Enables the virtualization feature of the widget.
    * @param boolean|\Kendo\UI\DropDownListVirtual|array $value
    * @return \Kendo\UI\DropDownList
    */
    public function virtual($value) {
        return $this->setProperty('virtual', $value);
    }

    /**
    * Sets the change event of the DropDownList.
    * Fired when the value of the widget is changed by the user.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the close event of the DropDownList.
    * Fired when the popup of the widget is closed.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function close($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('close', $value);
    }

    /**
    * Sets the dataBound event of the DropDownList.
    * Fired when the widget is bound to data from its data source.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the filtering event of the DropDownList.
    * Fired when the widget is about to filter the data source.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function filtering($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('filtering', $value);
    }

    /**
    * Sets the open event of the DropDownList.
    * Fired when the popup of the widget is opened by the user.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function open($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('open', $value);
    }

    /**
    * Sets the select event of the DropDownList.
    * Fired when an item from the popup is selected by the user either with mouse/tap or with keyboard navigation.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the cascade event of the DropDownList.
    * Fired when the value of the widget is changed via API or user interaction.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\UI\DropDownList
    */
    public function cascade($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cascade', $value);
    }


//<< Properties
}

?>

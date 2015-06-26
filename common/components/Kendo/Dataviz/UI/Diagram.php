<?php

namespace Kendo\Dataviz\UI;

class Diagram extends \Kendo\UI\Widget {
    public function name() {
        return 'Diagram';
    }
//>> Properties

    /**
    * If set to false the widget will not bind to the data source during initialization. In this case data binding will occur when the change event of the
data source is fired. By default the widget will bind to the data source specified in the configuration.
    * @param boolean $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function autoBind($value) {
        return $this->setProperty('autoBind', $value);
    }

    /**
    * Defines the defaults of the connections. Whenever a connection is created, the specified connectionDefaults will be used and merged with the (optional) configuration passed through the connection creation method.
    * @param \Kendo\Dataviz\UI\DiagramConnectionDefaults|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function connectionDefaults($value) {
        return $this->setProperty('connectionDefaults', $value);
    }

    /**
    * Adds DiagramConnection to the Diagram.
    * @param \Kendo\Dataviz\UI\DiagramConnection|array,... $value one or more DiagramConnection to add.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addConnection($value) {
        return $this->add('connections', func_get_args());
    }

    /**
    * Defines the data source of the connections.
    * @param |array| $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function connectionsDataSource($value) {
        return $this->setProperty('connectionsDataSource', $value);
    }

    /**
    * Sets the data source of the Diagram.
    * @param array|\Kendo\Data\DataSource $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dataSource($value) {
        return $this->setProperty('dataSource', $value);
    }

    /**
    * Defines how the diagram behaves when the user attempts to edit shape content, create new connections, edit connection labels and so on.
    * @param boolean|\Kendo\Dataviz\UI\DiagramEditable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function editable($value) {
        return $this->setProperty('editable', $value);
    }

    /**
    * The layout of a diagram consists in arranging the shapes (sometimes also the connections) in some fashion in order to achieve an aesthetically pleasing experience to the user. It aims at giving a more direct insight in the information contained within the diagram and its relational structure.On a technical level, layout consists of a multitude of algorithms and optimizations:and various ad-hoc calculations which depend on the type of layout. The criteria on which an algorithm is based vary but the common denominator is:Kendo diagram includes three of the most used layout algorithms which should cover most of your layout needs - tree layout, force-directed layout and layered layout. Please, check the type property for more details regarding each type.The generic way to apply a layout is by calling the layout() method on the diagram. The method has a single parameter options. It is an object, which can contain parameters which are specific to the layout as well as parameters customizing the global grid layout. Parameters which apply to other layout algorithms can be included but are overlooked if not applicable to the chose layout type. This means that you can define a set of parameters which cover all possible layout types and simply pass it in the method whatever the layout define in the first parameter.
    * @param \Kendo\Dataviz\UI\DiagramLayout|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function layout($value) {
        return $this->setProperty('layout', $value);
    }

    /**
    * Defines the pannable options.
    * @param boolean|\Kendo\Dataviz\UI\DiagramPannable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pannable($value) {
        return $this->setProperty('pannable', $value);
    }

    /**
    * Configures the export settings for the saveAsPDF method.
    * @param \Kendo\Dataviz\UI\DiagramPdf|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pdf($value) {
        return $this->setProperty('pdf', $value);
    }

    /**
    * Defines the selectable options.
    * @param boolean|\Kendo\Dataviz\UI\DiagramSelectable|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function selectable($value) {
        return $this->setProperty('selectable', $value);
    }

    /**
    * Defines the shape options.
    * @param \Kendo\Dataviz\UI\DiagramShapeDefaults|array $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function shapeDefaults($value) {
        return $this->setProperty('shapeDefaults', $value);
    }

    /**
    * Adds DiagramShape to the Diagram.
    * @param \Kendo\Dataviz\UI\DiagramShape|array,... $value one or more DiagramShape to add.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addShape($value) {
        return $this->add('shapes', func_get_args());
    }

    /**
    * Sets the template option of the Diagram.
    * The template which renders the content of the shape when bound to a dataSource. The names you can use in the template correspond to the properties used in the dataSource. See the dataSource topic below for a concrete example.
    * @param string $value The id of the element which represents the kendo template.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function templateId($value) {
        $value = new \Kendo\Template($value);

        return $this->setProperty('template', $value);
    }

    /**
    * Sets the template option of the Diagram.
    * The template which renders the content of the shape when bound to a dataSource. The names you can use in the template correspond to the properties used in the dataSource. See the dataSource topic below for a concrete example.
    * @param string $value The template content.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function template($value) {
        return $this->setProperty('template', $value);
    }

    /**
    * The zoom level in percentages.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoom($value) {
        return $this->setProperty('zoom', $value);
    }

    /**
    * The zoom max level in percentages.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomMax($value) {
        return $this->setProperty('zoomMax', $value);
    }

    /**
    * The zoom min level in percentages.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomMin($value) {
        return $this->setProperty('zoomMin', $value);
    }

    /**
    * The zoom step when using the mouse-wheel to zoom in or out.
    * @param float $value
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomRate($value) {
        return $this->setProperty('zoomRate', $value);
    }

    /**
    * Sets the add event of the Diagram.
    * Fired when the user adds new shape or connection.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function addEvent($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('add', $value);
    }

    /**
    * Sets the cancel event of the Diagram.
    * Fired when the user clicks the "cancel" button in the popup window in case the item was added via a toolbar.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function cancel($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('cancel', $value);
    }

    /**
    * Sets the change event of the Diagram.
    * Fired when an item is added or removed to/from the diagram.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function change($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('change', $value);
    }

    /**
    * Sets the click event of the Diagram.
    * Fired when the user clicks on a shape or a connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function click($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('click', $value);
    }

    /**
    * Sets the dataBound event of the Diagram.
    * Fired when the widget is bound to data from dataDource and connectionsDataSource.The event handler function context (available via the this keyword) will be set to the widget instance.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dataBound($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dataBound', $value);
    }

    /**
    * Sets the drag event of the Diagram.
    * Fired when dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function drag($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('drag', $value);
    }

    /**
    * Sets the dragEnd event of the Diagram.
    * Fired after finishing dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dragEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragEnd', $value);
    }

    /**
    * Sets the dragStart event of the Diagram.
    * Fired before starting dragging shapes or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function dragStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('dragStart', $value);
    }

    /**
    * Sets the edit event of the Diagram.
    * Fired when the user edits a shape or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function edit($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('edit', $value);
    }

    /**
    * Sets the itemBoundsChange event of the Diagram.
    * Fired when the location or size of an item are changed.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function itemBoundsChange($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('itemBoundsChange', $value);
    }

    /**
    * Sets the itemRotate event of the Diagram.
    * Fired when an item is rotated.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function itemRotate($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('itemRotate', $value);
    }

    /**
    * Sets the mouseEnter event of the Diagram.
    * Fired when the mouse enters a shape or a connection.Will not fire for disabled items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function mouseEnter($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('mouseEnter', $value);
    }

    /**
    * Sets the mouseLeave event of the Diagram.
    * Fired when the mouse leaves a shape or a connection.Will not fire for disabled items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function mouseLeave($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('mouseLeave', $value);
    }

    /**
    * Sets the pan event of the Diagram.
    * Fired when the user pans the diagram.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function pan($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('pan', $value);
    }

    /**
    * Sets the remove event of the Diagram.
    * Fired when the user removes a shape or connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function remove($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('remove', $value);
    }

    /**
    * Sets the save event of the Diagram.
    * Fired when the user saved a shape or a connection.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function save($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('save', $value);
    }

    /**
    * Sets the select event of the Diagram.
    * Fired when the user selects one or more items.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function select($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('select', $value);
    }

    /**
    * Sets the zoomEnd event of the Diagram.
    * Fired when the user changes the diagram zoom level.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomEnd($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomEnd', $value);
    }

    /**
    * Sets the zoomStart event of the Diagram.
    * Fired when the user starts changing the diagram zoom level.
    * @param string|\Kendo\JavaScriptFunction $value Can be a JavaScript function definition or name.
    * @return \Kendo\Dataviz\UI\Diagram
    */
    public function zoomStart($value) {
        if (is_string($value)) {
            $value = new \Kendo\JavaScriptFunction($value);
        }

        return $this->setProperty('zoomStart', $value);
    }


//<< Properties
}

?>

<?php

namespace Kendo\Dataviz\UI;

class ChartSeriesDefaultsLabelsFromMargin extends \Kendo\SerializableObject {
//>> Properties

    /**
    * The bottom margin of the from labels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsLabelsFromMargin
    */
    public function bottom($value) {
        return $this->setProperty('bottom', $value);
    }

    /**
    * The left margin of the from labels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsLabelsFromMargin
    */
    public function left($value) {
        return $this->setProperty('left', $value);
    }

    /**
    * The right margin of the from labels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsLabelsFromMargin
    */
    public function right($value) {
        return $this->setProperty('right', $value);
    }

    /**
    * The top margin of the from labels.
    * @param float $value
    * @return \Kendo\Dataviz\UI\ChartSeriesDefaultsLabelsFromMargin
    */
    public function top($value) {
        return $this->setProperty('top', $value);
    }

//<< Properties
}


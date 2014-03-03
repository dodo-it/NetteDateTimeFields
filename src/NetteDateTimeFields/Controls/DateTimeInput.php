<?php

/**
 * Based on Jan Tvrdík DatePicker (http://nette.merxes.cz/date-picker/)
 *
 * @author Livio Ribeiro  
 * @license  BSD-3-Clause
 */

namespace NetteDateTimeFields\Controls;

use Nette;
use Nette\Forms;
use DateTime;

/**
 * Form control for selecting date and time.
 *
 * @author   Livio Ribeiro
 * @version  1.0
 * @link     http://github.com/livioribeiro/NetteDateTimeFields
 */
class DateTimeInput extends DateTimeBase {

    /** @var     string            date format */
    protected $dateFormat;

    /** @var     string            time format */
    protected $timeFormat;

    /**
     * Class constructor.
     *
     * @author   Livio Ribeiro
     * @param    string            label
     * @param    string            date format
     * @param    string            time format
     * @param    string            date time separator
     * @param    bool              html5 input
     */
    public function __construct($label = NULL, $dateFormat = NULL, $timeFormat = NULL, $separator = NULL, $html5 = FALSE) {
        parent::__construct($label, $html5);
        $this->dateFormat = $dateFormat === NULL ? self::DEFAULT_DATE_FORMAT : $dateFormat;
        $this->timeFormat = $timeFormat === NULL ? self::DEFAULT_TIME_FORMAT : $timeFormat;
        $separator = $separator === NULL ? self::DEFAULT_SEPARATOR : $separator;
        $this->format = $this->dateFormat . $separator . $this->timeFormat;
        $this->w3format = self::W3C_DATETIME_FORMAT;
        $this->control->type = $html5 ? 'datetime-local' : 'text';
        $this->className = 'datetime';
    }

    /**
     * Returns class name.
     *
     * @author   Jan Tvrdík
     * @return   string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Sets class name for input element.
     *
     * @author   Jan Tvrdík
     * @param    string
     * @return   self
     */
    public function setClassName($className) {
        $this->className = $className;
        return $this;
    }

    /**
     * Returns date format
     * 
     * @author Livio Ribeiro
     * @return string
     */
    public function getDateFormat() {
        return $this->dateFormat;
    }

    /**
     * Returns time format
     * 
     * @author Livio Ribeiro
     * @return string
     */
    public function getTimeFormat() {
        return $this->timeFormat;
    }

    /**
     * Is entered values within allowed range?
     *
     * @author   Livio Ribeiro
     * @param    DateTimeInput
     * @param    array             0 => minDate, 1 => maxDate, 2 => minTime, 3 => maxTime
     * @return   bool
     */
    public static function validateDateTimeRange(Forms\IControl $control, $range) {
        if (count($range) == 4) { // time range
            $time = clone $control->getValue();

            list($minDate, $maxDate, $minTime, $maxTime) = $range;
            if (is_string($minDate))
                $minDate = DateTime::createFromFormat($control->getFormat(), $minDate);
            if (is_string($maxDate))
                $maxDate = DateTime::createFromFormat($control->getFormat(), $maxDate);
            if (is_string($minTime))
                $minTime = DateTime::createFromFormat($control->getTimeFormat(), $minTime);
            if (is_string($maxTime))
                $maxTime = DateTime::createFromFormat($control->getTimeFormat(), $maxTime);

            $time->setDate(1970, 1, 1);
            $minTime->setDate(1970, 1, 1);
            $maxTime->setDate(1970, 1, 1);

            return Nette\Utils\Validators::isInRange($control->getValue(), array($minDate, $maxDate))
                    && Nette\Utils\Validators::isInRange($time, array($minTime, $maxTime));
        }
        elseif (count($range) == 2) {
            list($minDate, $maxDate) = $range;
            if (is_string($minDate))
                $minDate = DateTime::createFromFormat($control->getDateFormat(), $minDate);
            if (is_string($maxDate))
                $maxDate = DateTime::createFromFormat($control->getDateFormat(), $maxDate);

            return Nette\Utils\Validators::isInRange($control->getValue(), array($minDate, $maxDate));
        }
        else {
            throw new \InvalidArgumentException();
        }
    }

}

<?php

namespace NetteDateTimeFields\Controls;

use Nette;
use Nette\Forms;
use DateTime;

/**
 * Base class for DateInput, TimeInput and DateTimeInput
 *
 * @author  Livio Ribeiro
 * @version 1.0
 * @link    http://github.com/livioribeiro/NetteDateTimeFields
 */
abstract class DateTimeBase extends Forms\Controls\BaseControl {

    const DATETIME_RANGE = ':dateTimeRange';

    /** @link http://dev.w3.org/html5/spec/common-microsyntaxes.html#valid-date-string */
    const W3C_DATE_FORMAT = 'Y-m-d';
    const W3C_TIME_FORMAT = 'H:i';
    const W3C_DATETIME_FORMAT = 'Y-m-dHi';
    
    const DEFAULT_DATE_FORMAT = 'd/m/Y';
    const DEFAULT_TIME_FORMAT = 'H:i';
    const DEFAULT_SEPARATOR = ' ';

    /** @var     string            value entered by user (unfiltered) */
    protected $rawValue;

    /** @var     string            date format */
    protected $format;

    /** @var     string            format for client side validation */
    protected $w3format;

    /** @var     string            class name */
    protected $className;

    /** @var     bool              whether the input should be of html5 date, time and datetime types */
    protected $html5;

    public function __construct($caption = NULL, $html5 = FALSE) {
        parent::__construct($caption);
        $this->html5 = $html5;
    }

    /**
     * Generates control's HTML element.
     *
     * @author   Livio Ribeiro
     * @return   Nette\Utils\Html
     */
    public function getControl() {
        $control = parent::getControl();
        $control->addClass($this->className);

        if ($this->html5) {
            list($min, $max) = $this->extractRangeRule($this->getRules());
            
            if ($min !== NULL) {
                $control->min = $min->format($this->w3format);
            }
            if ($max !== NULL) {
                $control->max = $max->format($this->w3format);
            }
        }
        
        if ($this->value) {
            $control->value = $this->value->format($this->format);
            $control->addAttributes(array('data-format' => $this->format));
        }
        
        return $control;
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
     * Returns unfiltered value.
     *
     * @author   Jan Tvrdík
     * @return   string
     */
    public function getRawValue() {
        return $this->rawValue;
    }

    /**
     * Returns the date format
     * 
     * @return string format
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * Sets DatePicker value.
     *
     * @author   Livio Ribeiro
     * @param    DateTime|int|string
     * @return   self
     */
    public function setValue($value) {
        if ($value instanceof DateTime) {
            $this->value = $value;
        } elseif (is_int($value)) { // timestamp
            $datetime = new DateTime();
            $datetime->setTimestamp($value);
            $this->value = $datetime;
        } elseif (is_string($value)) {
            try {
                $this->value = DateTime::createFromFormat($this->format, $value);
            } catch (\Exception $e) {
                $this->value = NULL;
            }
        } elseif (empty($value)) {
            $this->value = NULL;
        } else {
            throw new \InvalidArgumentException();
        }

        if ($this->value) {
            $this->rawValue = $this->value->format($this->format);
        }

        return $this;
    }

    /**
     * Does user enter anything? (the value doesn't have to be valid)
     *
     * @author   Jan Tvrdík
     * @param    DatePicker
     * @return   bool
     */
    public static function validateFilled(Forms\IControl $control) {
        if (!$control instanceof self)
            throw new Nette\InvalidStateException('Unable to validate ' . get_class($control) . ' instance.');
        $rawValue = $control->rawValue;
        return !empty($rawValue);
    }

    /**
     * Is entered value valid? (empty value is also valid!)
     *
     * @author   Jan Tvrdík
     * @param    DatePicker
     * @return   bool
     */
    public static function validateValid(Forms\IControl $control) {
        if (!$control instanceof self)
            throw new Nette\InvalidStateException('Unable to validate ' . get_class($control) . ' instance.');
        $value = $control->value;
        return (empty($control->rawValue) || $value instanceof DateTime);
    }

    /**
     * Is entered values within allowed range?
     *
     * @author   Livio Ribeiro
     * @param    DatePicker
     * @param    array             0 => minTime, 1 => maxTime
     * @return   bool
     */
    public static function validateDateTimeRange(Forms\IControl $control, $range) {
        list($min, $max) = $range;
        if (is_string($min))
            $min = DateTime::createFromFormat($control->getDateFormat(), $min);
        if (is_string($max))
            $max = DateTime::createFromFormat($control->getDateFormat(), $max);

        return Nette\Utils\Validators::isInRange($control->getValue(), array($min, $max));
    }

    /**
     * Finds minimum and maximum allowed times.
     *
     * @author   Jan Tvrdík
     * @param    Forms\Rules
     * @return   array             0 => DateTime|NULL $minTime, 1 => DateTime|NULL $maxTime
     */
    protected function extractRangeRule(Forms\Rules $rules) {
        $controlMin = $controlMax = NULL;
        foreach ($rules as $rule) {
            if ($rule->type === Forms\Rule::VALIDATOR) {
                if ($rule->operation === DateTimeForm::DATETIME_RANGE && !$rule->isNegative) {
                    $ruleMinMax = $rule->arg;
                }
            } elseif ($rule->type === Forms\Rule::CONDITION) {
                if ($rule->operation === Forms\Form::FILLED && !$rule->isNegative && $rule->control === $this) {
                    $ruleMinMax = $this->extractRangeRule($rule->subRules);
                }
            }

            if (isset($ruleMinMax)) {
                list($ruleMin, $ruleMax) = $ruleMinMax;
                if ($ruleMin !== NULL && ($controlMin === NULL || $ruleMin > $controlMin))
                    $controlMin = $ruleMin;
                if ($ruleMax !== NULL && ($controlMax === NULL || $ruleMax < $controlMax))
                    $controlMax = $ruleMax;
                $ruleMinMax = NULL;
            }
        }

        if (is_string($controlMin))
            $controlMin = DateTime::createFromFormat($this->format, $controlMin);
        if (is_string($controlMax))
            $controlMax = DateTime::createFromFormat($this->format, $controlMax);

        return array($controlMin, $controlMax);
    }

}
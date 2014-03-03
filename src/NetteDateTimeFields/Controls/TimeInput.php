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
 * Form control for selecting time.
 *
 * @author   Livio Ribeiro
 * @version  1.0
 * @link     http://github.com/livioribeiro/NetteDateTimeFields
 */
class TimeInput extends DateTimeBase {

    /**
     * Class constructor.
     *
     * @author   Livio Ribeiro
     * @param    string            caption
     * @param    string            format
     * @param    bool              html5 input
     */
    public function __construct($caption = NULL, $format = NULL, $html5 = FALSE) {
        parent::__construct($caption, $html5);
        $this->format = $format === NULL ? self::DEFAULT_TIME_FORMAT : $format;
        $this->w3format = self::W3C_TIME_FORMAT;
        $this->control->type = $html5 ? 'time' : 'text';
        $this->className = 'time';
    }

}

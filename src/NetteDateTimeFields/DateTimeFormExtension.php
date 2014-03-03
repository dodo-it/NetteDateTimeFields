<?php

namespace NetteDateTimeFields;

use Nette\DI;
use Nette\PhpGenerator;
use Nette\Forms\Container;

if (!class_exists('Nette\DI\CompilerExtension')) {
    class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
}
if (!class_exists('Nette\PhpGenerator\ClassType')) {
    class_alias('Nette\Utils\PhpGenerator\ClassType', 'Nette\PhpGenerator\ClassType');
}

/**
 * Compiler extension for date and time inputs
 *
 * @author Livio Ribeiro
 * @version 1.0
 * @link http://github.com/livioribeiro/NetteDateTimeFields
 */
class DateTimeFormExtension extends DI\CompilerExtension
{
    public function afterCompile(PhpGenerator\ClassType $class) {
        $initialize = $class->methods['initialize'];
        $initialize->addBody('\NetteDateTimeFields\DateTimeFormExtension::register();');
    }
    
    public static function register() {
        Container::extensionMethod('addDate', function (Container $container, $name, $label = NULL, $format = NULL, $html5 = FALSE) {
            return $container[$name] = new DateInput($label, $format, $html5);
        });
        Container::extensionMethod('addTime', function (Container $container, $name, $label = NULL, $format = NULL, $html5 = FALSE) {
            return $container[$name] = new TimeInput($label, $format, $html5);
        });
        Container::extensionMethod('addDateTime', function (Container $container, $name, $label = NULL, $dateFormat = NULL, $timeFormat = NULL, $separator = NULL, $html5 = FALSE) {
            return $container[$name] = new DateTimeInput($label, $dateFormat, $timeFormat, $separator, $html5);
        });
    }
}

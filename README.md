NetteDateTimeFields
===================

Form component for date, time and datetime inputs with automatic conversion to DateTime.

Setup
-----

Add the DateTimeFormExtension to your config.neon

```yaml
extensions:
  - NetteDateTimeFields\DateTimeFormExtension
```

or call `register` method in bootstrap.php

```php
use NetteDateTimeFields\DateTimeFormExtension
DateTimeFormExtension::register();
```

Usage
-----

Simply call `addDate()`, `addTime()` or `addDateTime()` on the Form object:

```php
$form = new Nette\Application\UI\Form();
$form->addDate('date', 'date', $format = 'd/m/Y');
$form->addTime('time', 'time', $format = 'H:i');
$form->addDateTime('dateTime', 'dateTime', $dateFormat = 'd/m/Y', $timeFormat = 'H:i', $separator = ' ')
```

You can also add a range validation:

```php
use NetteDateTimeFields\Controls\DateTimeBase;

$form = new Nette\Application\UI\Form();
$form->addDate('date', 'date', 'Y-m-d')
    ->addRule(DateTimeBase::DATETIME_RANGE, 'message',
        ['1970-01-01', '2070-01-01']); // \DateTime or string 
$form->addTime('time', 'time')
    ->addRule(DateTimeBase::DATETIME_RANGE, 'message',
        ['08:00', '16:30']);
$form->addDateTime('dateTime', 'dateTime')
    ->addRule(DateTimeBase::DATETIME_RANGE, 'message',
        ['01/01/1970', '01/01/2070']);
```

The parameters can be either a \DateTime object or a string formatted accondingly to the format specified.

DateTime range validation can receive 4 parameters, which the third and fourth validates the time range of the given date.
The following will not validate, for example, a date and time like '01/01/2010 07:00'.

```php
$form->addDateTime('dateTime', 'dateTime')
    ->addRule(DateTimeBase::DATETIME_RANGE, 'message',
        ['01/01/1970', '01/01/2070', '08:00', '16:30']);
```

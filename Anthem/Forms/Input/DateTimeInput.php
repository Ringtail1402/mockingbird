<?php

namespace Anthem\Forms\Input;

use Anthem\Forms\Input\BaseInput;

/**
 * A datepicker input.  Requrest bootstrap-datepicker.js.
 * Options:
 * - with_time (boolean) Show selects for time (hours/minutes).  Default is true.
 */
class DateTimeInput extends BaseInput
{
  /**
   * Sets input value.  Normalizes it if necessary.
   * Accepts unix timestamp, strings and DateTime objects.
   *
   * @param integer|\DateTime|string|array|null $value
   * @return void
   */
  public function setValue($value)
  {
    if (!$value || (is_array($value) && empty($value['date']) &&
                    ((isset($this->options['with_time']) && !$this->options['with_time']) ||
                     (empty($value['hours']) && empty($value['minutes'])))))
    {
      parent::setValue(array('date' => null, 'hours' => null, 'minutes' => null));
      return;
    }
    if (!$value['date']) $value['date'] = date('Y-m-d');

    $_value = array();
    if (is_array($value))
      $value = strtotime(sprintf('%s %02d:%02d:00', $value['date'],
                                                    isset($value['hours']) ? $value['hours'] : 0,
                                                    isset($value['minutes']) ? $value['minutes'] : 0));
    if (is_string($value))
      $value = strtotime($value);
    if ($value instanceof \DateTime)
      $value = $value->getTimestamp();

    $_value['date']    = date(_t('Forms.DATE_FORMAT'), $value);
    $_value['hours']   = date('H', $value);
    $_value['minutes'] = date('i', $value);
    parent::setValue($_value);
  }

  /**
   * Returns input value, as a string in Y-m-d H:i:s format.
   *
   * @param  none
   * @return mixed
   */
  public function getValue()
  {
    if (!$this->value['date'] &&
        ((isset($this->options['with_time']) && !$this->options['with_time']) ||
         (!$this->value['hours'] && !$this->value['minutes']))) return null;

    if ($this->value['date'])
      $value = date('Y-m-d', strtotime($this->value['date']));
    else
      $value = date('Y-m-d');
    if (isset($this->options['with_time']) && !$this->options['with_time'])
      $value .= ' 00:00:00';
    else
      $value .= sprintf(' %02d:%02d:00', $this->value['hours'], $this->value['minutes']);
    return $value;
  }

 /**
  * Returns template used.
  *
  * @return string
  */
  protected function getDefaultTemplate()
  {
    return 'Anthem/Forms:datetime.php';
  }
}
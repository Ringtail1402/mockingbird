<?php

namespace Mockingbird\View;

use Silex\Application;
use Mockingbird\Model\Currency;

/**
 * Mockingbird helpers.
 */
class MockingbirdHelpers
{
  /**
   * @var \Silex\Application
   */
  protected $app;

  /**
   * The constructor.
   *
   * @param \Silex\Application $app
   */
  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  /**
   * Formats cash according to currency.  For currencies other
   * than primary, returns <abbr> tag with value in primary currency as hint.
   *
   * @param float         $amount
   * @param Currency|null $currency
   * @return string
   */
  public function c($amount, $currency = null, $sign = false)
  {
    $default_currency = $this->app['mockingbird.model.currency']->getDefaultCurrency();
    if (!$currency) $currency = $default_currency;

    if (!$sign) $amount = abs($amount);
    $result = sprintf(str_replace('#', '%.2f', $currency->getFormat()), $amount);
    if ($sign)
    {
      $result = preg_replace('/^(.*)-/', '&ndash;\1', $result);
      if ($amount > 0) $result = '+' . $result;
    }

    if ($currency != $default_currency)
    {
      $result = '<abbr title="' .
          sprintf(str_replace('#', '%.2f', $default_currency->getFormat()),
              abs($amount) * $currency->getRateToPrimary() / $default_currency->getRateToPrimary()) .
          '">' . $result . '</abbr>';
    }

    return $result;
  }

  /**
   * Same as c(), but with fancy formatting (green/red label).  Link optional.
   *
   * @param float       $amount
   * @param Currency    $currency
   * @param string|null $link
   * @return string
   */
  public function cc($amount, $currency = null, $link = null)
  {
    $result = ($link ? '<a href="' . $link . '" ' : '<span ');
    $result .= 'class="cc ' . ($amount >= 0 ? 'pos' : 'neg') . '">';
    $result .= $this->c($amount, $currency, true);
    $result .= ($link ? '</a>' : '</span>');
    return $result;
  }

  /**
   * Renders <option> tags with available accounts.
   *
   * @param integer $selectedid Id of account that should be marked selected.
   * @return string
   */
  public function renderAccountOptionTags($selectedid = null)
  {
    $accounts = $this->app['mockingbird.model.account']->getAllAccounts();
    $result = '';
    $lasttype = null;
    foreach ($accounts as $_account)
    {
      if ($_account->getIsdebt() && $_account->getIsCredit() && $lasttype != 'credit')
      {
        if ($lasttype) $result .= '</optgroup>' . "\n";
        $result .= '<optgroup label="' . _t('CREDIT_ACCOUNTS') . '">' . "\n";
        $lasttype = 'credit';
      }
      if ($_account->getIsdebt() && !$_account->getIsCredit() && $lasttype != 'debit')
      {
        if ($lasttype) $result .= '</optgroup>' . "\n";
        $result .= '<optgroup label="' . _t('DEBIT_ACCOUNTS') . '">' . "\n";
        $lasttype = 'debit';
      }
      $result .= '<option value="' . $_account->getId() . '"' . ($_account->getId() == $selectedid ? ' selected' : '') .
          '>' . htmlspecialchars($_account->getTitle()) . '</option>' . "\n";
    }
    if ($lasttype) $result .= '</optgroup>' . "\n";
    return $result;
  }

  /**
   * Renders <option> tags with available categories.
   *
   * @param integer $selectedid Id of category that should be marked selected.
   * @return string
   */
  public function renderCategoryOptionTags($selectedid = null, $user = null)
  {
    $categories = $this->app['mockingbird.model.category']->getAll($user);
    $result = '';
    foreach ($categories as $_category)
    {
      $result .= '<option value="' . $_category->getId() . '"' . ($_category->getId() == $selectedid ? ' selected' : '') .
          '>' . htmlspecialchars($_category->getTitle()) . '</option>' . "\n";
    }
    return $result;
  }

  /**
   * Renders <option> tags with available currencied.
   *
   * @param integer $selectedid Id of category that should be marked selected.
   * @return string
   */
  public function renderCurrencyOptionTags($selectedid = null)
  {
    $currencies = $this->app['mockingbird.model.currency']->getAll();
    $result = '';
    foreach ($currencies as $_currency)
    {
      $result .= '<option value="' . $_currency->getId() . '"' . ($_currency->getId() == $selectedid ? ' selected' : '') .
          '>' . htmlspecialchars($_currency->getTitle()) . '</option>' . "\n";
    }
    return $result;
  }

  /**
   * PHP Calendar (version 2.3), written by Keith Devens
   * http://keithdevens.com/software/php_calendar
   * License: http://keithdevens.com/software/license
   * Doesn't include <table> tag.
   *
   * @param integer $year
   * @param integer $month
   * @param array   $days   Optional, content of day cells.
   *                        $day => $content, or $day => array('content' => $content, 'class' => $td_class)
   * @param boolean $day_names_show
   * @param integer $first_day  First day of week.
   * @return string
   */
  public function calendar($year, $month, $days = array(), $day_names_show = true, $first_day = 1)
  {
    $first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
    // remember that mktime will automatically correct if invalid dates are entered
    // for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
    // this provides a built in "rounding" feature

    $day_names = array();  // generate all the day names according to the current locale
    for ($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t += 86400) // January 4, 1970 was a Sunday
      $day_names[$n] = trim(gmstrftime('%a', $t), '.'); // %a means abbreviated textual day name

    list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w', $first_of_month));
    $weekday = ($weekday + 7 - $first_day) % 7;  // adjust for $first_day

    $calendar = '';

    // if the day names should be shown ($day_name_length > 0)
    if ($day_names_show)
    {
      $calendar .= '<thead>';
      foreach ($day_names as $d) $calendar .= '<th>' . $d . '</th>';
      $calendar .= "</thead>\n<tr>";
    }

    if ($weekday > 0) for ($i = 0; $i < $weekday; $i++) $calendar .= '<td>&nbsp;</td>';  // initial 'empty' days
    for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++)
    {
      if ($weekday == 7)
      {
	$weekday   = 0; // start a new week
	$calendar .= "</tr>\n<tr>";
      }
      if (isset($days[$day]))
      {
        if (!is_array($days[$day])) $days[$day] = array('content' => $days[$day]);
        $content = empty($days[$day]['content']) ? $day : $days[$day]['content'];
	$calendar .= '<td'. (!empty($days[$day]['class']) ? ' class="' . htmlspecialchars($days[$day]['class']) . '">' : '>') .
			$content .
                     '</td>';
      }
      else
        $calendar .= "<td>$day</td>";
    }
    if ($weekday != 7) for ($i = $weekday; $i < 7; $i++) $calendar .= '<td>&nbsp;</td>';  // remaining "empty" days

    return $calendar . "</tr>\n";
  }
}
<?php

namespace Anthem\Admin\Admin\TableColumn;

use BasePeer;
use ModelCriteria;
use Anthem\Admin\Admin\TableColumn\BaseColumn;

/**
 * Base class for Propel-based table fields.
 */
abstract class PropelColumn extends BaseColumn
{
 /**
  * Translates column name into format accepted by Propel methods like $query->add(...).
  *
  * @param  \ModelCriteria $query
  * @return string
  */
  protected function getPropelColumnName($query)
  {
    if (!empty($this->options['is_virtual'])) return $this->field;

    $peer_class = $query->getModelPeerName();
    return call_user_func(array($peer_class, 'translateFieldName'),
                          $this->field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
  }

 /**
  * Adds sorting criteria for this field to the query.
  *
  * @param  \ModelCriteria $query
  * @param  string         $dir
  * @return \ModelCriteria
  */
  public function addSortCriteria($query, $dir)
  {
    $column = $this->getPropelColumnName($query);

    if (strtolower($dir) == 'asc')
      $query->addAscendingOrderByColumn($column);
    else
      $query->addDescendingOrderByColumn($column);

    return $query;
  }

 /**
  * Adds filtering criteria for this field to the query.
  *
  * @param  \ModelCriteria $query
  * @param  mixed          $filter
  * @return \ModelCriteria
  */
  public function addFilter($query, &$filter)
  {
    $filter = trim($filter);
    $column = $this->getPropelColumnName($query);
    $query->add($column, $filter);
    return $query;
  }
}
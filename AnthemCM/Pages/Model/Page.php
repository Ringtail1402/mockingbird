<?php

namespace AnthemCM\Pages\Model;

use AnthemCM\Pages\Model\om\BasePage;


/**
 * Skeleton subclass for representing a row from the 'pages' table.
 */
class Page extends BasePage
{
  public function setUrl($v)
  {
    // XXX Force modified flag, otherwise if page is re-saved with unchanged URL it reverts to automatic slug
    $this->modifiedColumns[] = PagePeer::URL;
    return parent::setUrl($v);
  }
}

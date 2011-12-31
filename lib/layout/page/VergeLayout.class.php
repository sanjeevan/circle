<?php

class VergeLayout extends BasePageLayout
{
  public function initialize()
  {
    $this->template = "layout/verge_layout";

    $this->bins = array();
    $this->bins[0] = array( "type" => self::WIDE );
    $this->bins[1] = array( "type" => self::SQUARE );
    $this->bins[2] = array( "type" => self::SQUARE );
    $this->bins[3] = array( "type" => self::WIDE );
  }
}

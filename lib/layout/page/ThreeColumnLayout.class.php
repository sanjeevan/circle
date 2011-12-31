<?php

class ThreeColumnLayout extends BasePageLayout
{
  public function initialize()
  {
    $this->template = "layout/three_column";
    $size = 18;
    
    $this->bins[] = array("type" => self::WIDE );
    foreach (range(0, $size - 1) as $idx) {
      $this->bins[$idx] = array("type" => self::SQUARE);
    }
  }
}

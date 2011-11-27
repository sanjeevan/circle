<?php

class MediaContentFilterList
{
  protected $file = null;
  protected $filters = array();

  public function __construct()
  {
    $this->loadFilters();
  }

  public function setFile(File $file)
  {
    $this->file = $file;
  }

  public function loadFilters()
  {
    $this->filters = array(
      new MediaImageSizeFilter(),
      //new MediaEasyListBlockFilter(),
    );
  }

  public function isNeeded()
  {
    foreach ($this->filters as $filter) {
      $keep = $filter->canKeep($this->file);
      if (!$keep) {
        return false;
      }
    }

    return true;
  }
}

<?php

class MediaDownloaderConfiguration extends ParameterHolder
{
  public function __construct()
  {
    $this->setParameter("urls", array());
    $this->setParameter("postFilters", array(
      new MediaImageSizeFilter(),
      new MediaKeywordsFilter()
    ));
  }
}

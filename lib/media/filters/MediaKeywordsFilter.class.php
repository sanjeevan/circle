<?php

class MediaKeywordsFilter extends MediaFilter
{
  protected $backlist_keywords = array(
    'logo',
    'advert',
    'facebook',
    'twitter',
    'rss',
    'gravatar'
  );

  public function __construct()
  {
  }

  public function canKeep(File $file)
  {
    foreach ($this->backlist_keywords as $keyword) {
      if (strpos($file->getFilename(), $keyword) !== false) {
        return false;
      }
      
      $file_to_url =  $file->getFileToUrl()->getFirst();
      if (strpos($file_to_url->getUrl(), $keyword) !== false) {
        return false;
      }
    }

    return true;
  }
}

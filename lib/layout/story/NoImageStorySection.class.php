<?php

class NoImageStorySection extends BaseStorySection
{
  protected $min_size = array(
    "w" => 280,
    "h" => 280
  );

  public function getScore()
  {
    if ($this->getTotalImages() == 0 && 
        $this->getReadabilityContentLength() >= 100) {
      return 100;
    }

    $collection = $this->getStory()->getFileToStory();
    foreach ($collection as $fts) {
      $file = $fts->getFile();
      if ($file->getMetaWidth() >= $this->min_size["w"]
          /*&& $file->getMetaHeight() >= $this->min_size["h"]*/) {
        return 0;
      }
    }

    return 10;
  }

  public function getHtmlFragment()
  {
    $params = array(
      "story" => $this->story
    );

    $partial = $this->getPartial("section/no_image", $params);
    return $partial;
  }
}

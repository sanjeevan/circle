<?php

class LargeStorySection extends BaseStorySection
{
  public function getScore()
  {
    $score = 0;
   
    if ($this->getReadabilityContentLength() >= 500) {
      $score += 90;
    }

    $images = $this->getStory()->getFiles();
    if ($images->count() > 0) {
      $score += 11;
    }

    return $score;
  }

  public function getHtmlFragment($template = null)
  {
    $template = $template ? $template : "section/wide";
    $params = array(
      "images"  => $this->getStory()->getFiles(),
      "story"   => $this->getStory()
    );

    $partial = $this->getPartial($template, $params);
    return $partial;
  }
}

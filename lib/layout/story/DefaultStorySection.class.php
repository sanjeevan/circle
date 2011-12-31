<?php 

class DefaultStorySection extends BaseStorySection
{
  public function getScore()
  {
    $score = 0;

    if ($this->getReadabilityContentLength() == 0) {
      return 0;
    }

    if ($this->getReadabilityContentLength() >= 300) {
      $score += 100;
    }

    return $score;
  }

  public function getHtmlFragment($template = null)
  {
    $template = $template ? $template : "section/default";
    $params = array(
      "images"  => $this->story->getFiles(),
      "story"   => $this->story
    );

    $partial = $this->getPartial($template, $params);
    return $partial;
  }
}

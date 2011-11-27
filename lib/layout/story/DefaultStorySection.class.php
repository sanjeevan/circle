<?php 

class DefaultStorySection extends BaseStorySection
{
  protected function analyze()
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

  public function getHtmlFragment()
  {
    $params = array(
      "images"  => $this->story->getFiles(),
      "story"   => $this->story
    );

    $partial = $this->getPartial("section/default", $params);
    return $partial;
  }

  public function getScore()
  {
    return $this->analyze();
  }
}

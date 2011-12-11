<?php

class ImageOnlyStorySection extends BaseStorySection
{
  public function getScore()
  {
    if ($this->getStory()->getFlavour() == "image") {
      return 100;
    }

    return 0;
  }

  public function getHtmlFragment()
  {
    $files = $this->getStory()->getFiles();

    $params = array(
      "story" => $this->getStory(),
      "image" => $files->getFirst()
    );
    
    return $this->getPartial("section/image", $params);
  }
}

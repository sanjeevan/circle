<?php

class ImageOnlyStorySection extends BaseStorySection
{
  protected $special_domains = array(
    "imgur.com",
    "quickmeme.com",
    "qkme.me"
  );

  public function getScore()
  {
    if ($this->getStory()->getFlavour() == "image") {
      return 101;
    }
    
    foreach ($this->special_domains as $domain) {
      $pattern = "/.*" . preg_quote($domain) . "$/";
      if (preg_match($pattern, $this->getStory()->getHost())) {
        return 101;
      }
    }

    return 0;
  }

  public function getHtmlFragment($template = null)
  {
    $template = $template ? $template : "section/image";
    $params = array(
      "story" => $this->getStory(),
      "image" => $this->getStory()->getBiggestImage()
    );
    
    return $this->getPartial($template, $params);
  }
}

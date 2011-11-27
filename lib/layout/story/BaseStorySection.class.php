<?php

abstract class BaseStorySection
{
  protected $sf_context = null;
  protected $story = null;

  public function __construct(Story $story = null)
  {
    $this->sf_context = sfContext::getInstance();
    if ($story) {
      $this->story = $story;
    }
    
    $helpers = array(
      "Partial",
      "Text",
      "General",
    );

    $this->sf_context->getConfiguration()->loadHelpers($helpers);
  }

  public function getStory()
  {
    return $this->story;
  }

  public function getPartial($name, $params = array())
  {
    return get_partial($name, $params);
  }

  public function getTotalImages()
  {
    return $this->story->getTotalImages();
  }

  public function getReadabilityContentLength()
  {
    return strlen($this->story->getReadabilityContent());
  }

  public function isReadabilityContentEmpty()
  {
    return $this->getReadabilityContentLength() == 0;
  }

  public function setStory(Story $story)
  {
    $this->story = $story;
  }

  public abstract function getHtmlFragment();

  public abstract function getScore();
}

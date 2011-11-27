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

  /**
  * Get story object
  *
  * @return Story
  */
  public function getStory()
  {
    return $this->story;
  }

  /** 
  * Get partial template
  *
  * @param string $name
  * @param array $params
  */
  public function getPartial($name, $params = array())
  {
    return get_partial($name, $params);
  }

  /**
  * Returns total images associated with this story
  *
  * @return integer
  */
  public function getTotalImages()
  {
    return $this->story->getTotalImages();
  }

  /**
  * Returns the length of the text extracted
  *
  * @return integer
  */
  public function getReadabilityContentLength()
  {
    return strlen($this->story->getReadabilityContent());
  }

  /**
  * Returns true if we couldn't extract the article
  *
  * @return boolean
  */
  public function isReadabilityContentEmpty()
  {
    return $this->getReadabilityContentLength() == 0;
  }

  /**
  * Set the story object
  *
  * @param Story $story
  */
  public function setStory(Story $story)
  {
    $this->story = $story;
  }

  /**
  * Get rendered html content for this story
  *
  * @return string
  */
  public abstract function getHtmlFragment();

  /**
  * Returns a score which indicates whether this Section is appropiate for
  * rendering this story. The higher the score, the better the fit.
  *
  * @return integer
  */
  public abstract function getScore();
}

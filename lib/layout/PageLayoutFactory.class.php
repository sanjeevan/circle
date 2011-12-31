<?php

class PageLayoutFactory 
{
  /**
  * The array of sections we want to render
  *
  * @param array
  */
  protected $sections = array();

  /**
  * The layouts we're going to be using
  *
  * @param array
  */
  protected $layouts = array();

  /**
  * Instance of this class
  *
  * @param PageLayoutFactory
  */
  protected static $instance = null;
  
  public function __construct($sections = array())
  {
    $this->sections = $sections;
    $this->setupDefaults();
  }
 
  protected function setupDefaults()
  {
    $this->setLayouts(array(
      new ThreeColumnLayout($this->sections),
    ));
  }

  /**
  * Returns the best layout for the sections we want to render
  *
  * @return BasePageLayout
  */
  public function getBestLayout()
  {
    $high = 0;
    $best_layout = null;

    foreach ($this->layouts as $layout) {
      $fit = $layout->getFitness();
      if ($fit >= $high) {
        $best_layout = $layout;
      }
    }

    return $best_layout;
  }

  /**
  * Set the available layouts that we can use
  *
  * @param array $layouts
  */
  public function setLayouts($layouts = array())
  {
    $this->layouts = $layouts;
  }

  /**
  * Set sections
  *
  * @param array $sections
  */
  public function setSections($sections = array())
  {
    $this->sections = $sections;
  }

  /**
  * Get instance of this class
  *
  * @return PageLayoutFactory
  */
  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new PageLayoutFactory();
    }

    return self::$instance;
  }
}

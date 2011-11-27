<?php 

class StorySectionFactory 
{
  public static $instance = null;
  private $sections = array();

  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new StorySectionFactory();
    } 

    return self::$instance;
  }

  protected function initialize()
  {
    $this->sections = array(
      new DefaultStorySection(),
      new FeaturedStorySection()
    );
  }

  public function getSectionForStory(Story $story)
  {
    $selected_section = null;
    $highest_score    = 0;
    $this->initialize();

    foreach ($this->sections as $section) {
      $section->setStory($story);
      $score = $section->getScore();

      if ($score > $highest_score) {
        $highest_score = $score;
        $selected_section = $section;
      }
    }

    return $selected_section;
  }
}


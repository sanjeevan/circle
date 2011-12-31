<?php

/**
 * home actions.
 *
 * @package    Circle
 * @subpackage home
 * @author     Sanjeevan Ambalavanar
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homeActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $stories = Doctrine_Query::create()
      ->select("s.*, fts.*, f.*")
      ->from("Story s, s.FileToStory fts, fts.File f")
      ->orderBy("s.created_at DESC")
      ->limit(36)
      ->execute();
    
    $story_section_factory = StorySectionFactory::getInstance(); 
    $sections = array();
    
    // select the appropiate section for each story
    foreach ($stories as $story) {
      $section = $story_section_factory->getSectionForStory($story);
      if ($section instanceof BaseStorySection) {
        $sections[] = $section;
      }  
    }

    //$layout_factory = new PageLayoutFactory($sections);
    //$page_layout = $layout_factory->getBestLayout();
    $page_layout = new MasonLayout($sections);
    $this->page_content = $page_layout->getContent();
  }
}

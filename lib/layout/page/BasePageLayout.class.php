<?php

abstract class BasePageLayout 
{
  // BLOCKS
  const WIDE    = 1;
  const SQUARE  = 2;
  const TALL    = 3;

  protected $section_to_size = array(
    "DefaultStorySection"   => 1,
    "LargeStorySection"     => 2,
    "ImageOnlyStorySection" => 1,
    "NoImageStorySection"   => 1,
  );

  protected $block_to_size = array(
    self::SQUARE  => 1,
    self::WIDE    => 2,
    self::TALL    => 2,
  );

  protected $template   = null;
  protected $bins       = array();
  protected $sections   = array();
  protected $sf_context = null;
  protected $packed     = false;

  public function __construct($sections = array())
  {
    $this->sections = $sections;
    $this->initialize();
  }

  public function initialize()
  {
  }

  /**
  * Get all bins in this layout
  *
  * @return array
  */
  public function getBins()
  {
    return $this->bins;
  }

  /**
  * Returns the class name
  */
  public function getClassName()
  {
    return get_class($this);
  }

  /**
  * Returns rendered page content
  *
  * @return string
  */
  public function getContent()
  {
    $this->sf_context = sfContext::getInstance();
    $this->sf_context->getConfiguration()->loadHelpers(array("Partial", "Text", "General"));

    if (!$this->packed) {
      $this->packStorySections();
    }

    $partial = get_partial($this->template, array(
      "sections"  => $this->sections,
      "bins"      => $this->bins
    ));

    return $partial;
  }

  /**
  * Get the size of this layout
  *
  * @return integer
  */
  public function getLayoutSize()
  {
    $size = 0;
    foreach ($this->bins as $bin) {
      $size += $this->getBlockSize($bin['type']);
    }
    return $size;
  }

  /**
  * Get the total combined size of the sections
  * we're trying to render
  *
  * @return integer
  */
  public function getTotalSectionSize()
  {
    $size = 0;
    foreach ($this->sections as $section) {
      $size += $this->section_to_size[$section->getClassName()];
    }
    return $size;
  }
  
  /**
  * Get the size of the block
  *
  * @return integer
  */
  public function getBlockSize($block)
  {
    return $this->block_to_size[$block];
  }
  
  /**
  * Returns the fittness of the layout for rendering
  * the sections. The higher the number the better.
  *
  * @return integer
  */
  public function getFitness()
  {
    if (!$this->packed) {
      $this->packStorySections();
    }

    // Calculate the fitness of this layout
    $score = $this->getLayoutSize();
    foreach ($this->bins as $bin) {
      $block_size = $this->getBlockSize( $bin['type'] );
      if (!isset($bin['section'])) {
        $score -= $block_size;
      } else {
        $section_size = $this->section_to_size[$bin['section']->getClassName()];
        $score -= ($block_size - $section_size);
      }
    }
   
    //var_dump($this->bins);
    //var_dump($score);
    //var_dump($this->getLayoutSize());

    return (float) $score / $this->getLayoutSize() * 100;
  }
  
  public function debugPacking()
  {
    if (!$this->packed) {
      $this->packStorySections();
    }

    foreach ($this->bins as $idx => $bin) {
      echo "{$idx} Block type: {$bin['type']}\n";
      echo "{$idx} Section: " . $bin['section']->getClassName() . "\n";
      echo "{$idx} Story " . "(" . $bin['section']->getStory()->getHost() . ") " .  $bin['section']->getStory()->getTitle() . "\n";
    }
  }

  protected function packStorySections()
  {
    $placed = array(); 

    // Place it in the best possible position first
    foreach ($this->sections as $section) {
      $section_size = $this->section_to_size[$section->getClassName()]; 
      for ($i = 0; $i < count($this->bins); $i++) {
        $bin = $this->bins[$i];
        $r = $this->getBlockSize($bin['type']) - $section_size;
        if ($r == 0 && !isset($bin['section'])) {
          $this->bins[$i]['section'] = $section;
          $placed[] = $section->getStory()->getId();
          break;
        }
      }      
    }

    // If there's no suitable position, try placing in a generic block, where 
    // the placement block will be smaller than the actual content itself.
    foreach ($this->sections as $section) {
      // Skip section if it's already been placed
      if (in_array($section->getStory()->getId(), $placed)) {
        continue;
      }
      
      $section_size = $this->section_to_size[$section->getClassName()];
      
      for ($i = 0; $i < count($this->bins); $i++) {
        $bin = $this->bins[$i];
        $r = $this->getBlockSize($bin['type']) - $section_size;
        if ($r < 0 && !isset($bin['section'])) {
          $this->bins[$i]['section'] = $section;
          $placed[] = $section->getStory()->getId();
          break;
        }
      }      
    }

    // At this stage, place the sections in blocks that are larger than the 
    // content, hopefully only a few stories will fall into this category!
    foreach ($this->sections as $section) {
      if (in_array($section->getStory()->getId(), $placed)) {
        continue;
      }

      for ($i = 0; $i < count($this->bins); $i++) {
        $bin = $this->bins[$i];
        if (isset($bin['section'])) {
          continue;
        }

        $this->bins[$i]['section'] = $section;
        $placed[] = $section->getStory()->getId();
        break;
      }
    }
  }
}

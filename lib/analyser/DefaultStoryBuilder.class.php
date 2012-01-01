<?php

class DefaultStoryBuilder extends StoryBuilder
{
  protected $mime_types = array(
    "text/html", "text/xml", "application/xhtml+xml"
  );

  public function __construct()
  { 
    $vendor_dir = sfConfig::get("sf_root_dir") . "/vendor/";

    // load Readability class
    if (!class_exists("Readability")) {
      require_once($vendor_dir . "readability/Readability.php");
    }
    
    // load HTML purifier
    if (!class_exists("HTMLPurifier")) {
      require_once $vendor_dir . '/htmlpurifier/library/HTMLPurifier.auto.php';
    }
  }

  public function getScore()
  {
    $score = 0;
    $body = $this->getParameter(self::BODY);
    $url = $this->getParameter(self::URL);

    $mime =  $this->getParameter(self::MIME_TYPE);
    if (in_array($mime, $this->mime_types)) {
      $score += 10;
    }

    $readability = new Readability($body, $url);
    $readability->converLinksToFootnotes = true;
    $result = $readability->init();

    // if there is content then set the readability content field
    if ($result) {
      $content = $readability->getContent()->innerHTML;
      $tidy = tidy_parse_string($content, array(
          "indent" => true, 
          "show-body-only" => true), "UTF8"
      );

      $tidy->cleanRepair();
      $score += 50;

      if (strlen($tidy->value) > 400) {
        $score += 20;
      }
    }
    /*
    if ($score == 0) {
      echo "{$url}, Score: {$score}\n";
      //echo $body;
    }
    */

    return $score;
  }

  protected function getSummary($content)
  {
    $forbidden_elements = array(
      "img", "h1", "h2", "h3", "h4", "a"
    );
    $config = HTMLPurifier_Config::createDefault();
    $config->set("HTML.ForbiddenElements", $forbidden_elements);

    $purifier = new HTMLPurifier($config);
    return $purifier->purify($content);
  }

  public function createStoryObject()
  {
    $story = new Story();
    $story->setUrl($this->getParameter(self::URL));
    $story->setTitle($this->getParameter(self::TITLE));
    $story->setFlavour("article");
    $story->setReadabilityContentFromHtml($this->getParameter(self::BODY));
    $story->setVia($this->getParameter(self::VIA));
    $story->setHost($this->getHost());
    $story->setSummaryHtml($this->getSummary($story->getReadabilityContent()));
    $story->save();

    return $story;
  }
  
  public function getMediaDownloaderConfiguration()
  {
    $image_urls = $this->getImageUrls();

    $config = new MediaDownloaderConfiguration();
    $config->setParameter("urls", $image_urls);
    $config->setParameter("postFilters", array(
      new MediaImageSizeFilter(),
      new MediaKeywordsFilter()
    ));

    return $config;
  }
}

<?php

abstract class StoryBuilder extends ParameterHolder
{
  const TITLE     = "title";
  const URL       = "url";
  const BODY      = "body";
  const MIME_TYPE = "mime-type";
  const VIA       = "via";
 
  protected $image_types = array(
    "jpg", "gif", "png", "jpeg", "bmp", "tiff"
  );

  protected function getHost()
  {
    $uri = parse_url($this->getParameter(self::URL));
    return $uri["host"];
  }
  
  protected function getTitle()
  {
    return $this->getParameter(self::TITLE);
  }

  protected function getBody()
  {
    return $this->getParameter(self::BODY);
  }

  protected function getMimeType()
  {
    return $this->getParameter(self::MIME_TYPE);
  }

  /**
  * Convert relative urls to absolute urls
  *
  * @param string $rel
  * @param string $base
  * @return string
  */
  private function relativeUrlToAbs($rel, $base)
  {
    // return if already absolute URL
    if (parse_url($rel, PHP_URL_SCHEME) != ''){
      return $rel;
    }

    // queries and anchors
    if ($rel[0]=='#' || $rel[0]=='?'){
      return $base . $rel;
    }

    extract(parse_url($base));
   
    // remove non-directory element from path
    $path = preg_replace('#/[^/]*$#', '', $path);

    // destroy path if relative url points to root
    if ($rel[0] == '/'){
      $path = '';
    }

    // dirty absolute URL
    $abs = "$host$path/$rel";

    // replace '//' or '/./' or '/foo/../' with '/'
    $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');

    for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)){ 
    }

    return $scheme . '://' . $abs;
  }

  /**
   * Parses out image links found in body
   *
   * @return array
   */
  protected function getImageUrls()
  {
    $html = $this->getParameter(self::BODY);
    $base_path = $this->getParameter(self::URL);
    
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // check if a base url has been defined in the page
    $x1 = new DOMXPath($dom);
    foreach ($x1->evaluate('/html/head/base') as $bp){
      if ($bp->hasAttribute('href')){
        $base_path = (string) $bp->getAttribute('href');
        break;
      }
    }
    
    // get img tags from page
    $x2 = new DOMXPath($dom);
    $image_urls = array();
    
    foreach ($x2->evaluate('/html/body//img') as $node){
      $src = (string) $node->getAttribute('src');
      $ext = myUtil::getFileExtension($src);
      if (in_array($ext, $this->image_types)){
        $url = $this->relativeUrlToAbs($src, $base_path);
        $image_urls[] = str_replace(' ', '%20', $url);
      }
    }

    unset($x1);
    unset($xpath);
    unset($dom);

    return array_unique($image_urls);
  }

  /**
  * Returns the score for this url, content based on this builder. The score
  * is a value from 0 - 100. 
  *
  * @return integer
  */
  abstract function getScore();

  /**
  * Creates the story object
  *
  * @return Story
  */
  abstract function createStoryObject();

  /**
  * Returns the configration for the media downloader
  *
  * @return MediaDownloaderConfiguration
  */
  abstract function getMediaDownloaderConfiguration();
}

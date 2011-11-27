<?php

/**
* This class will take a story object as it's input, then will preceed to 
* download all images present in the story's source url.
*
* It will detect image type, and will populate the images width & height 
* in the database
*
*/
class MediaDownloader
{
  protected $user_agent_string = "Circle Media Bot (+circle)";
  protected $story = null;
  protected $current_page_url = "";
  protected $html = null;
  protected $tmp_path = "/tmp/circle";

  protected $image_types = array(
    "jpg", "gif", "png", "jpeg", "bmp", "tiff"
  );

  public function __construct(Story $story = null)
  {
    if (!class_exists('HTTP_Request')){
      require_once('HTTP/Request.php');
    }

    $this->story = $story;
    $this->rolling_curl = new RollingCurl(array($this, "onDownloadComplete"));

    if  (!is_dir($this->tmp_path)) {
      mkdir($this->tmp_path, 0777, true);
    }
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
   * Parses out image links found in the html string passed
   *
   * @param string $html
   * @return array
   */
  private function getPageImageUrls($html = '')
  {
    $base_path = $this->current_page_url;
    
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
      if (!$this->ignoreThisImage($src)){
        $ext = myUtil::getFileExtension($src);
        if (in_array($ext, $this->image_types)){
          $url = $this->relativeUrlToAbs($src, $base_path);
          $image_urls[] = str_replace(' ', '%20', $url);
        }
      }
    }

    unset($x1);
    unset($xpath);
    unset($dom);

    return array_unique($image_urls);
  }

  /** 
  * Used to filter out unwanted images
  */
  private function ignoreThisImage($url)
  {
    return false;
  }

  /**
   * Fetch a single page
   *
   * @param string $url
   * @return string
   */
  private function getUrl($url)
  {
    $req = @new HTTP_Request($url);
    $req->setMethod(HTTP_REQUEST_METHOD_GET);
    $req->addHeader('User-Agent', $this->user_agent_string);
    $req->sendRequest();

    $html = $req->getResponseBody();
    unset($req);
    return $html;
  }
  
  /**
  * Get the html of the page from which we're mining media
  *
  * @return string
  */
  public function getPageHtml()
  {
    return $this->html;
  }

  public function execute()
  {
    $this->current_page_url = $this->story->getUrl();
    $this->html = @$this->getUrl($this->current_page_url);
    $image_urls = $this->getPageImageUrls($this->html);

    if (count($image_urls) == 0) {
      return false;
    }
    
    foreach ($image_urls as $url) {
      $request = new Request($url);
      $request->headers = array(
        "Referer" => $this->current_page_url,
        "User-Agent" => $this->user_agent_string
      );
      $this->rolling_curl->add($request);
    }

    // block until all request finish up
    $this->rolling_curl->execute();
  }

  public function onDownloadComplete($response, $info)
  {
    $location = $this->saveToDisk($info, $response);
    if ($location == false) {
      return;
    }
    
    $filename = basename($info['url']);

    $file = new File();
    $file->setFilename($filename);
    $file->setFilesize(filesize($location));
    $file->setExtension(myUtil::getFileExtension($filename));
    $file->setMimetype(myUtil::getMimeType($location));
    $file->setHash(sha1_file($location));
    $file->useTempFile($location, false);
    $file->save();

    $fts = new FileToStory();
    $fts->setStory($this->story);
    $fts->setFile($file);
    $fts->save();

    $file_to_url = new FileToUrl();
    $file_to_url->setFile($file);
    $file_to_url->setUrl($info["url"]);
    $file_to_url->save();
  }

  public function saveToDisk($info, $response)
  {
    $http_code = $info['http_code'];

    $valid_http_codes = array(200, 201, 202, 203, 204, 204, 206);
    if (!in_array($http_code, $valid_http_codes)){
      return false;
    }
    
    $filename = basename($info['url']);
    $location = $this->tmp_path . '/' . $filename;

    $fp = fopen($location, 'w+');
    fwrite($fp, $response);
    fclose($fp);

    return $location;
  }
}

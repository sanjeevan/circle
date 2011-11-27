<?php

class MediaAssetDownloader
{
  /**
  * Stores configuration information for this downloader
  *
  * @param MediaDownloaderConfiguration
  */
  protected $config = null;

  protected $tmp_path = "/tmp/circle";

  protected $ua = "Circle Media Downloader";
  
  public function __construct(MediaDownloaderConfiguration $configuration)
  {
    if (!class_exists('HTTP_Request2')){
      require_once('HTTP/Request2.php');
    }

    $this->config = $configuration;
    $this->rolling_curl = new RollingCurl(array($this, "onDownloadComplete"));

    // Override user agent
    if ($this->config->hasParameter("user-agent")) {
      $this->ua = $this->config->getParameter("user-agent");
    }
    
    // Create temp path to store downloaded files
    if  (!is_dir($this->tmp_path)) {
      mkdir($this->tmp_path, 0777, true);
    }
  }

  /**
  * Download all images present in the story url
  * 
  * @return void
  */
  public function execute()
  {
    $urls = $this->config->getParameter("urls");

    if (count($urls) == 0) {
      return false;
    } 

    foreach ($urls as $url) {
      $uri = parse_url($url);
      $request = new Request($url);
      $request->headers = array(
        "Referer"     => $uri["host"],
        "User-Agent"  => $this->ua
      );
      $this->rolling_curl->add($request);
    }

    // block until all request finish up
    $this->rolling_curl->execute();
  }

  /**
  * This is a callback, that is run when an image is downloaded
  *
  * @param array $response
  * @param array $info
  */
  public function onDownloadComplete($response, $info)
  {
    // Save the file to disk
    $location = $this->saveToDisk($info, $response);
    if ($location == false) {
      return;
    }

    $filename = basename($info['url']);

    // Create the file object and associated relationships
    $file = new File();
    $file->setFilename($filename);
    $file->setFilesize(filesize($location));
    $file->setExtension(myUtil::getFileExtension($filename));
    $file->setMimetype(myUtil::getMimeType($location));
    $file->setHash(sha1_file($location));
    $file->useTempFile($location, false);
    
    // Set image meta information
    $img = new sfImage($file->getLocation(), $file->getMimeType());
    $file->setMetaWidth($img->getWidth());
    $file->setMetaHeight($img->getHeight());
    
    $file->save();

    $fts = new FileToStory();
    $fts->setStory($this->config->getParameter("story"));
    $fts->setFile($file);
    $fts->save();

    $file_to_url = new FileToUrl();
    $file_to_url->setFile($file);
    $file_to_url->setUrl($info["url"]);
    $file_to_url->save();

    // Run any filters against this file
    if ($this->config->hasParameter("postFilter") &&
        count($this->config->getParameter("postFilter")) > 0) {
      
      $filter_list = $this->config->getParameter("postFilter");

      foreach ($filter_list as $media_filter) {
        if (!$media_filter->canKeep($file)) {
          $file->delete();
        }
      }
    }
  }

  /**
  * Save response from server to disk
  *
  * @param array $info
  * @param array $response
  * @return string
  */
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

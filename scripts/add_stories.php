#!/usr/bin/php
<?php

// This script will index all events within the database. Will take a long time
// if there is a lot of event data

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

require_once('HTTP/Request2.php');

// Add story from url
function add_story_from_url($url, $title = null)
{
  $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);

  // text/html page mime-types
  $document_content_types = array(
    "text/html", "text/xml", "application/xhtml+xml"
  );

  // image mime-types
  $image_content_types = array(
    "image/gif", "image/jpeg", "image/png", "image/tiff",
    "image/pjpeg"
  );

  // temp path for downloading files
  $tmp_path = "/tmp/circle";
  if (!is_dir($tmp_path)) {
    mkdir($tmp_path, 0775, true);
  }

  try {
    $response = $request->send();

    if ($response->getStatus() != 200) {
      throw new Exception("Response status is not 200");
    }
    
    // create new Story
    $story = new Story();
    $story->setUrl($url);
    $story->setTitle($title);
    $story->save();
   
    // get content-type and normalize
    $content_type = $response->getHeader("content-type");
    if (strpos($content_type, ";")) {
      $parts = explode(";", $content_type);
      $content_type = trim($parts[0]);
    }
    echo "Content-type: {$content_type} \n";

    // if the url is an image and there is title set
    if (in_array($content_type, $image_content_types)) {
      // download image and save to disk
      $filename = basename($url);
      $location = $tmp_path . '/' . $filename;

      $fp = fopen($location, 'w+');
      fwrite($fp, $response->getBody());
      fclose($fp);
      
      // create file, file_to_url, and file_to_story objects
      $file = new File();
      $file->setFilename($filename);
      $file->setFilesize(filesize($location));
      $file->setExtension(myUtil::getFileExtension($filename));
      $file->setMimetype(myUtil::getMimeType($location));
      $file->setHash(sha1_file($location));
      $file->useTempFile($location, false);
      $file->save();

      $fts = new FileToStory();
      $fts->setStory($story);
      $fts->setFile($file);
      $fts->save();

      $file_to_url = new FileToUrl();
      $file_to_url->setFile($file);
      $file_to_url->setUrl($url);
      $file_to_url->save();
      
      return $story;
    }
    
    // if the url is pointing to a webpage
    if (in_array($content_type, $document_content_types)) {
      $media_downloader = new MediaDownloader($story);
      $media_downloader->execute();
      

      $story->setReadabilityContentFromHtml($response->getBody());
      $story->save();
    }

  } catch (Exception $e) {
    print $e->getMessage();
  }
}


function test()
{
  $url = "http://www.guardian.co.uk/world/2011/nov/25/us-cluster-bombs-bid-blocked";
  //$url = "http://www.newyorker.com/reporting/2011/11/14/111114fa_fact_gladwell?currentPage=all";
  //$url = "http://www.guardian.co.uk/commentisfree/2011/oct/18/booker-prize-readability-test-literature";
  $title = "Britain unites with smaller countries to block US bid to legalise cluster bombs";

  add_story_from_url($url, $title);
}


test();

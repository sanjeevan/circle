<?php

class ImageOnlyStoryBuilder extends StoryBuilder
{
  protected $mime_types = array(
   "image/gif", "image/jpeg", "image/png", "image/tiff", "image/pjpeg"
  );

  protected $tmp_path = "/tmp/circle";

  public function __construct()
  {
    if (!is_dir($this->tmp_path)) {
      mkdir($this->tmp_path, 0775, true);
    }
  }

  public function getScore()
  {
    $mime_type = $this->getParameter(self::MIME_TYPE);
    if (in_array($mime_type, $this->mime_types)) {
      return 100;
    }

    return 0;
  }

  public function getContent()
  {
    return false;
  }

  public function createStoryObject()
  {
    $story = new Story();
    $story->setUrl($this->getParameter(self::URL));
    $story->setTitle($this->getParameter(self::TITLE));
    $story->setFlavour("image");
    $story->setVia($this->getParameter(self::VIA));
    $story->setHost($this->getHost());
    $story->save();

    // write the body of the response to a file
    $filename = basename($this->getParameter(self::URL));
    $location = $this->tmp_path . "/" . myUtil::UUID();
    $fp = fopen($location, "w+");
    fwrite($fp, $this->getParameter(self::BODY));
    fclose($fp);

    // create file, file_to_url, and file_to_story objects
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
    $fts->setStory($story);
    $fts->setFile($file);
    $fts->save();

    $file_to_url = new FileToUrl();
    $file_to_url->setFile($file);
    $file_to_url->setUrl($this->getParameter(self::URL));
    $file_to_url->save();
    
    return $story;
  }

  public function getMediaDownloaderConfiguration()
  {
    return false;
  }
}

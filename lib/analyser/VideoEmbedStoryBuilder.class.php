<?php

class VideoEmbedStoryBuilder extends StoryBuilder
{
  protected $video_hosts = array(
    "youtube.com"
  );

  public function __construct()
  {
  }

  public function getScore()
  {
    $url = $this->getParameter(self::URL);

    foreach ($this->video_hosts as $host) {
      $pattern = "/.*" . preg_quote($host) . "$/";
      if (preg_match($pattern, $url)) {
        return 100;
      }
    }

    return 0;
  }

  public function createStoryObject()
  {
  }

  /**
  * This will return an array of related thumbnails (different sizes) for the
  * video we want to embed
  *
  * @return array
  */
  protected function getYoutubeThumbnail()
  {
    
  }

  public function getMediaDownloaderConfiguration()
  {
    // Create a configuration which will only download the thumbnail of the video
    $config = new MediaDownloaderConfiguration();
    $config->setParameter("urls", array());
    $config->setParameter("postFilters", array());
    

    return $config;
  }
}

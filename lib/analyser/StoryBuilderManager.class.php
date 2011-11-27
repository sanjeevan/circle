<?php 

class StoryBuilderManager
{
  protected $url;
  protected $uri;
  protected $title;
  protected $via;

  protected $builders = array();

  public function __construct($url, $title, $via)
  {
    $this->url = $url;
    $this->title = $title;
    $this->via = $via;
    $this->uri = parse_url($url);
  }

  /**
  * Add builder
  */
  public function addBuilder($builder = null) 
  {
    $this->builders[] = $builder;
  }

  /**
  * Return all builders bound to this generator
  *
  * @return array
  */
  public function getBuilders()
  {
    return $this->builders;
  }

  /**
  * Choose the appropiate builder to use to extract the article from
  * the passed url
  *
  * @return StoryExtractor
  */
  public function getBuilder()
  {
    $body = null;

    $fetcher = new WebFetcher();
    $fetcher->setUrl($this->url);
    $fetcher->useCache(true);
    $fetcher->setCacheTimeout(3600);

    $response = $fetcher->send();
    
    // if response if ok
    if ($response->getStatus() == 200) {
      $body = $response->getBody();
    }
    
    $mime_type = $this->getMimeType($response);

    if (count($this->builders) == 0) {
      throw new Exception("No builders to choose from");
    }
    
    $score = 0;
    $builder = null;
    
    foreach ($this->builders as $candidate_builder) {
      
      $candidate_builder->setParameter("title", $this->title);
      $candidate_builder->setParameter("url", $this->url);
      $candidate_builder->setParameter("mime-type", $mime_type);
      $candidate_builder->setParameter("body", $body);
      $candidate_builder->setParameter("via", $this->via);

      $temp_score = $candidate_builder->getScore();

      if ($temp_score > $score) {
        $score = $temp_score;
        $builder = $candidate_builder;
      }
    }

    if (!$builder) {
      $msg = "Could not find builder to use for url: {$this->url}";
      throw new Exception($msg);
    }

    return $builder;
  }

  /**
  * Get the mime type of content sent by the server
  *
  * @param HTTP_Response $request
  * @return string
  */
  protected function getMimeType($response)
  {
    $mime_type = "unknown";
    $header = $response->getHeader("content-type");
    if (strpos(";", $header)) {
      $parts = explode(";", $header);
      $mime_type = trim($parts[0]);
    }
    return $mime_type;
  }
}

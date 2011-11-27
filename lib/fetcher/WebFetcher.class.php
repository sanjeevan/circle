<?php 

class WebFetcher
{
  protected $ua             = "Circle Feed Reader (+circle)";
  protected $request        = null;

  protected $use_cache      = true;
  protected $cache_ns       = "webfetch";
  protected $cache_timeout  = 1800;

  protected $redis          = null;

  public function __construct()
  {
    if (!class_exists("HTTP_Request2")) {
      require_once("HTTP/Request2.php");
      require_once("HTTP/Request2/Response.php");
    }

    $this->request = new HTTP_Request2();
    $this->request->setAdapter('HTTP_Request2_Adapter_Curl');
    $this->request->setConfig(array(
      "follow_redirects" => true
    ));

    $this->request->setHeader("User-Agent", $this->ua);
    $this->redis = new Redis();
    
    // connect to redis
    $connect = $this->redis->connect("192.168.1.201", 6379);
    if (!$connect) {
      throw new Exception("Could not connect to redis");
    }
    
    // namespace all keys
    $this->redis->setOption(Redis::OPT_PREFIX, $this->cache_ns);

    // use php serializer
    $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
  }
  
  /**
  * Set url
  *
  * @param string $url
  */
  public function setUrl($url)
  {
    $this->request->setUrl($url);
  }

  /**
  * Set the number of seconds that the cache is valid for
  *
  * @param integer $seconds
  */
  public function setCacheTimeout($seconds = 1800)
  {
    $this->cache_timeout = $seconds;
  }

  /**
  * Returns the underlying HTTP_Request2 object
  *
  * @return HTTP_Request2
  */
  public function getRequest()
  {
    return $this->request;
  }

  /*
  * Whether to use cache or not for this request
  *
  */
  public function useCache($cache = true)
  {
    $this->use_cache = $cache;
  }

  /**
  * Send HTTP Request
  *
  * @return HTTP_Response
  */
  public function send()
  {
    $cache_id = $this->request->getUrl();
    if ($this->use_cache) {
      $object_str = $this->redis->get($cache_id);

      if ($object_str === false) {
        $response = $this->request->send();
        $body = $response->getBody();
        if (!empty($body)) {
          $this->redis->set($cache_id, $response);
          $this->redis->setTimeout($cache_id, $this->cache_timeout);
        }
        return $response;
      } else {
        return $object_str;
      }
    } else {
      return $this->request->send();
    }
  }
}

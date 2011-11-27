#!/usr/bin/php
<?php

require_once "add_stories.php";

// simple method to fetch a webpage, and save it to cache
function fetch_and_cache($url)
{
  $cache_dir = sfConfig::get("sf_root_dir") . "/scripts/cache/";
  if (!is_dir($cache_dir)) {
    mkdir($cache_dir);
  }

  $filename = $cache_dir . md5($url) . ".cache";
  if (is_readable($filename)) {
    return file_get_contents($filename);
  }

  $content = file_get_contents($url);
  $fp = fopen($filename, "w+");
  fwrite($fp, $content);
  fclose($fp);
  
  return $content;
}

function main()
{
  $url = "http://www.reddit.com/r/technology/.json";
  $result = fetch_and_cache($url);

  $body = json_decode($result, true);

  foreach ($body["data"]["children"] as $item) {
    $self_text = $item["data"]["selftext_html"];
    if ($self_text != null) {
      continue;
    }

    $title = $item["data"]["title"];
    $url = $item["data"]["url"];

    echo "Fetching: {$url} \n";
    add_story_from_url($url, $title);
  }
}

main();

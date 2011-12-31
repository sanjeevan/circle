#!/usr/bin/php
<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

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
  $url = "http://www.reddit.com/.json";
  $result = fetch_and_cache($url);

  $body = json_decode($result, true);

  foreach ($body["data"]["children"] as $item) {
    $self_text = $item["data"]["selftext_html"];
    if ($self_text != null) {
      continue;
    }

    $title = $item["data"]["title"];
    $url = $item["data"]["url"];

    $story = Doctrine::getTable("Story")->findOneByUrl($url);
    if ($story) {
      echo "Skipping {$story} [exists] \n";
      continue;
    }

    $manager = new StoryBuilderManager($url, $title, "reddit");
    $manager->addBuilder( new ImageOnlyStoryBuilder() );
    $manager->addBuilder( new DefaultStoryBuilder() );

    try {
      $builder = $manager->getBuilder();
      if (!$builder) {
        echo "Error creating builder for {$url}. See error log\n";
        continue;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      continue;
    }

    $story = $builder->createStoryObject();
    $configuration = $builder->getMediaDownloaderConfiguration();
    if ($configuration) {
      $configuration->setParameter("story", $story);
      $asset_downloader = new MediaAssetDownloader($configuration);
      $asset_downloader->execute();
    }
  }
}

main();

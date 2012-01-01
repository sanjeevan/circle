#!/usr/bin/php
<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

function process_item_list($items = array(), $via = "rss")
{
  $stories = array();

  foreach ($items as $item) {
    $title = $item['title'];
    $url = $item['url'];
    
    // Don't process a duplicate story
    $story = Doctrine::getTable("Story")->findOneByUrl($url);
    if ($story) {
      echo "Skipping {$story} [exists] \n";
      continue;
    } else {
      echo "Processing {$url} \n";
    }

    // Setup the story manager
    $manager = new StoryBuilderManager($url, $title, $via);
    $manager->addBuilder( new ImageOnlyStoryBuilder() );
    $manager->addBuilder( new DefaultStoryBuilder() );
    
    // Try creating a story from this rss item
    try {
      $builder = $manager->getBuilder();
      if (!$builder) {
        echo "Error creating builder for {$url}. See error log\n";
        continue;
      } else {
        echo "Created story for {$title} ({$url}) \n";
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      continue;
    }

    // Download any additional assets for this story
    $story = $builder->createStoryObject();
    $configuration = $builder->getMediaDownloaderConfiguration();
    if ($configuration) {
      $configuration->setParameter("story", $story);
      $asset_downloader = new MediaAssetDownloader($configuration);
      $asset_downloader->execute();
    }
    $stories[] = $story;
  }
}

function main()
{ 
  $items = array();
  $feed = "http://news.ycombinator.com/rss";

  $xml = file_get_contents($feed);
  $dom = simplexml_load_string($xml);

  $nodes = $dom->xpath("//item");
  
  foreach ($nodes as $node) {
    $title = (string) $node->title;
    $url = (string) $node->link;
    echo "Added {$title} \n";
    $items[] = array("title" => $title, "url" => $url);
  }

  $stories = process_item_list($items, "hackernews");
}

main();

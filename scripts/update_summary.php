#!/usr/bin/php
<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

require_once sfConfig::get("sf_root_dir") . '/vendor/htmlpurifier/library/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();
$config->set("HTML.ForbiddenElements", array("img", "h1", "h2", "h3", "h4", "a"));

$purifier = new HTMLPurifier($config);

$stories = Doctrine::getTable("Story")->findAll();

foreach ($stories as $story) {
  $html = $story->getReadabilityContent();
  
  if ($html == null) {
    continue;
  }

  echo "Updating {$story->getTitle()} \n";

  $story->setSummaryHtml($purifier->purify($story->getReadabilityContent()));
  $story->save();

}


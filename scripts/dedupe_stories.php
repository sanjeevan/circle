#!/usr/bin/php
<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

$stories = Doctrine::getTable("Story")->findAll();
$seen = array();

foreach ($stories as $story) {
  if (!isset($seen[$story->getUrl()])) {
    $seen[$story->getUrl()] = 1;
  } else {
    $story->delete(); 
    echo "Removed {$story->getTitle()} \n";
  }
}

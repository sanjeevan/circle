#!/usr/bin/php
<?php

// This script will index all events within the database. Will take a long time
// if there is a lot of event data

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

$files = Doctrine::getTable("File")->findAll();
$filter_chain = new MediaContentFilterList();

foreach ($files as $file) {
  if (!is_readable($file->getLocation())) {
    unlink($file->getLocation());
    $file->delete();
    echo "Could not read: {$file->getLocation()}\n";
    continue;
  }

  $img = new sfImage($file->getLocation(), $file->getMimeType());

  $file->setMetaWidth($img->getWidth());
  $file->setMetaHeight($img->getHeight());
  $file->save();
  
  $filter_chain->setFile($file);

  if (!$filter_chain->isNeeded()) {
    $file->delete();    
    echo "removed {$file->getFilename()} \n";
  } else {
    echo "updated {$file->getFilename()} \n";
  }
}



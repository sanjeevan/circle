#!/usr/bin/php
<?php

// This script will index all events within the database. Will take a long time
// if there is a lot of event data

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration);

$files = Doctrine::getTable("File")->findAll();
$filters = array(
  new MediaKeywordsFilter(),
  new MediaImageSizeFilter()
);


foreach ($files as $file) {
  if (!is_readable($file->getLocation())) {
    unlink($file->getLocation());
    $file->delete();
    echo "Could not read: {$file->getLocation()}\n";
    continue;
  }

  // Set image meta information
  if ($file->getMetaWidth() == null) {
    $img = new sfImage($file->getLocation(), $file->getMimeType());
    $file->setMetaWidth($img->getWidth());
    $file->setMetaHeight($img->getHeight());
    $file->save();
  }

  foreach ($filters as $filter) {
    if (!$filter->canKeep($file)) {
      $klass = get_class($filter);
      echo "[{$klass}] Removed: {$file->getFilename()} \n";
      $file->delete();
    }
  }
}



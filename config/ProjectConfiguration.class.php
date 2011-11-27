<?php

require_once '/usr/local/src/symfony/RELEASE_1_4_15/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfDoctrinePlugin');
    $this->enablePlugins('sfDoctrineGuardPlugin');
    $this->enablePlugins('sfThumbnailPlugin');
    $this->enablePlugins('sfImageTransformPlugin');
  }
}

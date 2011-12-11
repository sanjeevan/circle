<?php

abstract class MediaFilter
{
  abstract function canKeep(File $file);
}

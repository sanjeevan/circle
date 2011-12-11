<?php 

class MediaImageSizeFilter extends MediaFilter
{
  // standard IAB ad sizes
  protected $ad_sizes = array(
    "336x280",
    "300x250",
    "250x250",
    "240x400",
    "180x150",
    "728x90",
    "468x60",
    "234x60",
    "120x90",
    "120x60",
    "88x31",
    "80x15",
    "120x240",
    "125x125",
    "160x600",
    "300x600",
  );


  public function __construct()
  {
    
  }

  public function canKeep(File $file)
  {
    // don't keep files smaller than 32px
    if ($file->getMetaWidth() <= 32) {
      return false;
    }

    // don't keep files that are the same dimensions as IAB media ad sizes
    $size = $file->getMetaWidth() . "x" . $file->getMetaHeight();
    if (in_array($size, $this->ad_sizes)) {
      return false;
    }

    return true;
  }
}

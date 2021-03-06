<?php

/**
 * File
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    Circle
 * @subpackage model
 * @author     Sanjeevan Ambalavanar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class File extends BaseFile
{
  public function getIconTag()
  {
    return myFileIcon::getIconTagForExtension($this->getExtension());
  }

  /**
   * Returns true if file is an image
   *
   * @return boolean
   */
  public function isImage()
  {
    $img = array('jpg', 'jpeg', 'png', 'bmp', 'gif');
    
    if (in_array($this->extension, $img)){
      return true;
    }

    return false;
  }

  /**
   * Returns a formatted size of the file
   *
   * @param integer $precision
   * @return string
   */
  public function getFormattedFilesize($precision = 2)
  {
    $bytes = $this->getFilesize();
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
  }

  /**
   * Returns where the thumbnail is stored based on the created_at date, and the
   * hash of the file
   *
   * @param integer $size
   * @return string
   */
  public function getThumbnailFilename($w = 80, $h = 80, $method = 'normal')
  {
    $base = sfConfig::get('sf_upload_dir') . '/thumbnails';
    $created_at_ts = $this->getDateTimeObject('created_at')->format('U');
    $hash = $this->getHash();
    $f1 = sprintf('/%s/%s/%s', date('Y', $created_at_ts),
                               date('F', $created_at_ts),
                               date('d', $created_at_ts));

    $f2 = sprintf('/%s/%s/%s', substr($hash, 0, 3),
                               substr($hash, 3, 3),
                               substr($hash, 6, 3));

    $f3 = substr($hash, 9, 31);
    $filename = "{$base}{$f1}{$f2}/{$f3}/{$w}-{$h}-px-{$method}-thumb.png";
    return $filename;
  }

  /**
   * Get web accessible url to thumbnail
   *
   * @param integer $size
   * @return string
   */
  public function getThumbnailUrl($w = 80, $h = 80, $method = 'normal')
  {
    $filename = $this->getThumbnailFilename($w, $h, $method);
    if (is_readable($filename)){
      $url = str_replace(sfConfig::get('sf_web_dir') , myUtil::getWebRoot(), $filename);
      return $url;
    } else {
      return url_for("file/thumbnail?fileid={$this->getId()}&w={$w}&h={$h}&method={$method}");
    }
  }

  /**
   * Get web url for file
   * 
   * @return string
   */
  public function getUrl()
  {
    $filename = $this->getLocation();
    return str_replace(sfConfig::get('sf_web_dir'), myUtil::getWebRoot(), $filename);
  }

  /**
   * Creates the correct dir path to store the file and moves the temp file there
   * then updates the location to point to that file.
   * 
   * location format:
   *   /2010/October/16/3fg/532/fd1/[file-hash]/file.ext
   *
   * @param string $tmp_filepath
   * @param boolean $uploaded If file has been uploaded via browser
   * @return boolean
   */
  public function useTempFile($tmp_filepath, $uploaded = true)
  {
    $hash = $this->getHash();
    $base_folder = sfConfig::get('sf_upload_dir') . '/files';
    
    $f1 = sprintf("/%s/%s/%s", date('Y'),
                               date('F'),
                               date('d'));
    
    $f2 = sprintf('/%s/%s/%s', substr($hash, 0, 3),
                               substr($hash, 3, 3),
                               substr($hash, 6, 3));
                                          
    $f3 = substr($hash, 9, 31);
    $dest_folder = $base_folder . $f1 . $f2 . $f3;

    if (!is_dir($dest_folder)){
      mkdir($dest_folder, 0775, true);
    }

    $dest_filepath = $dest_folder . '/' . time() . 't-' . $this->getFilename();
    
    if ($uploaded){
      move_uploaded_file($tmp_filepath, $dest_filepath);
    } else {
      rename($tmp_filepath, $dest_filepath);
    }

    $this->setLocation($dest_filepath);

    return $dest_filepath;
  }

  /**
   * Delete file and database record
   *
   * @param Doctrine_Connection $conn
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    if (is_readable($this->getLocation())){
      unlink($this->getLocation());
    }
    
    parent::delete($conn);
  }
  
  public static function factoryFromSfValidatedFile(sfValidatedFile $vfile, $src = 'generic')
  {
    $file = new File();
    $file->setSource($src);
    $file->setFilename($vfile->getOriginalName());
    $file->setFilesize($vfile->getSize());
    $file->setMimetype($vfile->getType());
    $file->setExtension(str_replace('.', '', $vfile->getOriginalExtension()));
    $file->setHash(sha1_file($vfile->getTempName()));
    $file->useTempFile($vfile->getTempName());
    $file->save();
    
    return $file;
  }
  
  /**
   * Create file from request
   * 
   * @param sfWebRequest $request
   * @param string $name
   * @return File
   * @throws Exception
   */
  public static function factoryFromRequest(sfWebRequest $request, $name, $src = 'generic')
  {
    $file_info = $request->getFiles($name);
        
    if (is_array($file_info)){
      $file = new File();
      $file->setSource($src);
      $file->setFilename($file_info['name']);
      $file->setFilesize($file_info['size']);
      $file->setMimetype($file_info['type']);
      $file->setExtension(myUtil::getFileExtension($file_info['name']));
      $file->setHash(sha1_file($file_info['tmp_name']));
      $file->useTempFile($file_info['tmp_name']);
      $file->save();

      return $file;
    }
    
    throw new Exception('Could not find file information in parameter ' . $name);
  }
}

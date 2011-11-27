<?php

/**
 * file actions.
 *
 * @package    codelovely
 * @subpackage file
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class fileActions extends sfActions
{ 
  public function executeDownload(sfWebRequest $request)
  {
    $file = Doctrine::getTable('File')->find($request->getParameter('fileid'));

    if (!$file){
      $this->forward404('File not found');
    }
    
    header('Cache-control: private');
    header('Pragma: public');
    
    if (!$request->hasParameter('embed')){
      header('Content-disposition: attachment; filename="' . $file->getFilename() . '"');
    }
    
    header('Content-type: ' . $file->getMimetype());
    
    readfile($file->getLocation());
    
    exit(0);
  }
  
  public function executeThumbnail(sfWebRequest $request)
  {
    $file = Doctrine::getTable('File')->find($request->getParameter('fileid'));
    $this->forward404Unless($file, "File not found");
    
    $w = $request->getParameter('w', 80);
    $h = $request->getParameter('h', 80);
    
    // size overrides custom w,h parameters
    if ($request->hasParameter('size')){
      $w = $request->getParameter('size', 80);
      $h = $request->getParameter('size', 80);
    }
    
    $method = $request->getParameter('method', 'normal');
    $savefile = $file->getThumbnailFilename($w, $h, $method);
    
    $path = dirname($savefile);
    if (!is_readable($savefile)){
      if (!is_dir($path)){
        mkdir($path, 0775, true);
      }
      
      $options = array();
      
      switch ($method){
        case 'normal':
          $thumb = new sfThumbnail($w, $h, true, true, 100, 'sfImageMagickAdapter', $options);
          break;
        case 'adaptive':
          $options['method'] = 'shave_all';
          $thumb = new sfThumbnail($w, $h, false, true, 100, 'sfImageMagickAdapter', $options);
          break;
        default:
          $thumb = new sfThumbnail($w, $h, true, true, 100, 'sfImageMagickAdapter', $options);
          break;
      }
            
      $thumb->loadFile($file->getLocation());
      $thumb->save($savefile, 'image/png');
      
      header('Content-type: image/png');
      readfile($savefile);
      exit(0);
    } else {
      header('Content-type: image/png');
      readfile($savefile);
      exit(0);
    }
  }
    
  public function executeDeleteFromArticle(sfWebRequest $request)
  {
    
  }
}

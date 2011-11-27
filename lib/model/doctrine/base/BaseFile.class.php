<?php

/**
 * BaseFile
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $filename
 * @property integer $filesize
 * @property string $extension
 * @property string $mimetype
 * @property string $location
 * @property integer $meta_width
 * @property integer $meta_height
 * @property string $hash
 * @property string $source
 * @property Doctrine_Collection $FileToUrl
 * @property Doctrine_Collection $FileToStory
 * 
 * @method string              getFilename()    Returns the current record's "filename" value
 * @method integer             getFilesize()    Returns the current record's "filesize" value
 * @method string              getExtension()   Returns the current record's "extension" value
 * @method string              getMimetype()    Returns the current record's "mimetype" value
 * @method string              getLocation()    Returns the current record's "location" value
 * @method integer             getMetaWidth()   Returns the current record's "meta_width" value
 * @method integer             getMetaHeight()  Returns the current record's "meta_height" value
 * @method string              getHash()        Returns the current record's "hash" value
 * @method string              getSource()      Returns the current record's "source" value
 * @method Doctrine_Collection getFileToUrl()   Returns the current record's "FileToUrl" collection
 * @method Doctrine_Collection getFileToStory() Returns the current record's "FileToStory" collection
 * @method File                setFilename()    Sets the current record's "filename" value
 * @method File                setFilesize()    Sets the current record's "filesize" value
 * @method File                setExtension()   Sets the current record's "extension" value
 * @method File                setMimetype()    Sets the current record's "mimetype" value
 * @method File                setLocation()    Sets the current record's "location" value
 * @method File                setMetaWidth()   Sets the current record's "meta_width" value
 * @method File                setMetaHeight()  Sets the current record's "meta_height" value
 * @method File                setHash()        Sets the current record's "hash" value
 * @method File                setSource()      Sets the current record's "source" value
 * @method File                setFileToUrl()   Sets the current record's "FileToUrl" collection
 * @method File                setFileToStory() Sets the current record's "FileToStory" collection
 * 
 * @package    Circle
 * @subpackage model
 * @author     Sanjeevan Ambalavanar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseFile extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('file');
        $this->hasColumn('filename', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('filesize', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('extension', 'string', 25, array(
             'type' => 'string',
             'length' => 25,
             ));
        $this->hasColumn('mimetype', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('location', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('meta_width', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('meta_height', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             ));
        $this->hasColumn('hash', 'string', 32, array(
             'type' => 'string',
             'length' => 32,
             ));
        $this->hasColumn('source', 'string', 25, array(
             'type' => 'string',
             'length' => 25,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('FileToUrl', array(
             'local' => 'id',
             'foreign' => 'file_id'));

        $this->hasMany('FileToStory', array(
             'local' => 'id',
             'foreign' => 'file_id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}
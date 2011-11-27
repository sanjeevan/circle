<?php

/**
 * BaseVote
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $thing_id
 * @property integer $user_id
 * @property string $type
 * @property Thing $Thing
 * @property sfGuardUser $sfGuardUser
 * 
 * @method integer     getThingId()     Returns the current record's "thing_id" value
 * @method integer     getUserId()      Returns the current record's "user_id" value
 * @method string      getType()        Returns the current record's "type" value
 * @method Thing       getThing()       Returns the current record's "Thing" value
 * @method sfGuardUser getSfGuardUser() Returns the current record's "sfGuardUser" value
 * @method Vote        setThingId()     Sets the current record's "thing_id" value
 * @method Vote        setUserId()      Sets the current record's "user_id" value
 * @method Vote        setType()        Sets the current record's "type" value
 * @method Vote        setThing()       Sets the current record's "Thing" value
 * @method Vote        setSfGuardUser() Sets the current record's "sfGuardUser" value
 * 
 * @package    Circle
 * @subpackage model
 * @author     Sanjeevan Ambalavanar
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseVote extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('vote');
        $this->hasColumn('thing_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('type', 'string', 4, array(
             'type' => 'string',
             'length' => 4,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Thing', array(
             'local' => 'thing_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}
<?php

/**
 * ThingTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ThingTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object ThingTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Thing');
    }
}
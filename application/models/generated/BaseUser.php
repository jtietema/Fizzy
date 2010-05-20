<?php

/**
 * BaseUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $username
 * @property string $displayname
 * @property string $password
 * @property string $encryption
 * @property Doctrine_Collection $BlogPosts
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseUser extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('username', 'string', 150, array(
             'type' => 'string',
             'length' => '150',
             ));
        $this->hasColumn('displayname', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('password', 'string', 150, array(
             'type' => 'string',
             'length' => '150',
             ));
        $this->hasColumn('encryption', 'string', 10, array(
             'type' => 'string',
             'length' => '10',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Post as BlogPosts', array(
             'local' => 'id',
             'foreign' => 'author'));
    }
}
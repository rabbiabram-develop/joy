<?php

/**
 * BasePosts
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property timestamp $created_on
 * @property string $created_by
 * @property enum $status
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5318 2008-12-19 20:44:54Z jwage $
 */
abstract class BasePosts extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('posts');
    $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
    $this->hasColumn('title', 'string', 256, array('type' => 'string', 'length' => 256, 'notnull' => true));
    $this->hasColumn('slug', 'string', 256, array('type' => 'string', 'length' => 256, 'notnull' => true));
    $this->hasColumn('body', 'string', null, array('type' => 'string', 'notnull' => true));
    $this->hasColumn('created_on', 'timestamp', null, array('type' => 'timestamp', 'notnull' => true));
    $this->hasColumn('created_by', 'string', 32, array('type' => 'string', 'length' => 32, 'default' => 'HasanOzgan', 'notnull' => true));
    $this->hasColumn('status', 'enum', 7, array('type' => 'enum', 'length' => 7, 'values' => array(0 => 'draft', 1 => 'publish', 2 => 'private', 3 => 'deleted'), 'notnull' => true));
  }

}
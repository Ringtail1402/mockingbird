<?php

namespace Anthem\Auth\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'group_policies' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Anthem.Auth.Model.map
 */
class GroupPolicyTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Anthem.Auth.Model.map.GroupPolicyTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('group_policies');
        $this->setPhpName('GroupPolicy');
        $this->setClassname('Anthem\\Auth\\Model\\GroupPolicy');
        $this->setPackage('Anthem.Auth.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('group_id', 'GroupId', 'INTEGER', 'groups', 'id', true, null, null);
        $this->addColumn('policy', 'Policy', 'VARCHAR', true, 160, null);
        $this->getColumn('policy', false)->setPrimaryString(true);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Group', 'Anthem\\Auth\\Model\\Group', RelationMap::MANY_TO_ONE, array('group_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'alternative_coding_standards' =>  array (
  'brackets_newline' => 'true',
  'remove_closing_comments' => 'true',
  'use_whitespace' => 'true',
  'tab_size' => 2,
  'strip_comments' => 'false',
),
        );
    } // getBehaviors()

}

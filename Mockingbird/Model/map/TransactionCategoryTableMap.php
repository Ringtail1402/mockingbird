<?php

namespace Mockingbird\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'transaction_categories' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Mockingbird.Model.map
 */
class TransactionCategoryTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Mockingbird.Model.map.TransactionCategoryTableMap';

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
        $this->setName('transaction_categories');
        $this->setPhpName('TransactionCategory');
        $this->setClassname('Mockingbird\\Model\\TransactionCategory');
        $this->setPackage('Mockingbird.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title', false)->setPrimaryString(true);
        $this->addColumn('color', 'Color', 'VARCHAR', false, 10, '#000000');
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'Anthem\\Auth\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Transactions', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), null, null, 'Transactionss');
        $this->addRelation('BudgetEntry', 'Mockingbird\\Model\\BudgetEntry', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', null, 'BudgetEntrys');
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

<?php

namespace Mockingbird\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'ref_transactions_tags' table.
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
class RefTransactionTagTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Mockingbird.Model.map.RefTransactionTagTableMap';

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
        $this->setName('ref_transactions_tags');
        $this->setPhpName('RefTransactionTag');
        $this->setClassname('Mockingbird\\Model\\RefTransactionTag');
        $this->setPackage('Mockingbird.Model');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addForeignPrimaryKey('transaction_id', 'TransactionId', 'INTEGER' , 'transactions', 'id', true, null, null);
        $this->addForeignPrimaryKey('tag_id', 'TagId', 'INTEGER' , 'transaction_tags', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Transaction', 'Mockingbird\\Model\\Transaction', RelationMap::MANY_TO_ONE, array('transaction_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('TransactionTag', 'Mockingbird\\Model\\TransactionTag', RelationMap::MANY_TO_ONE, array('tag_id' => 'id', ), 'CASCADE', null);
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

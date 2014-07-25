<?php

namespace Mockingbird\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'transactions' table.
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
class TransactionTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Mockingbird.Model.map.TransactionTableMap';

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
        $this->setName('transactions');
        $this->setPhpName('Transaction');
        $this->setClassname('Mockingbird\\Model\\Transaction');
        $this->setPackage('Mockingbird.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title', false)->setPrimaryString(true);
        $this->addForeignKey('category_id', 'CategoryId', 'INTEGER', 'transaction_categories', 'id', false, null, null);
        $this->addForeignKey('account_id', 'AccountId', 'INTEGER', 'accounts', 'id', true, null, null);
        $this->addForeignKey('target_account_id', 'TargetAccountId', 'INTEGER', 'accounts', 'id', false, null, null);
        $this->addForeignKey('counter_transaction_id', 'CounterTransactionId', 'INTEGER', 'transactions', 'id', false, null, null);
        $this->addForeignKey('counter_party_id', 'CounterPartyId', 'INTEGER', 'counter_parties', 'id', false, null, null);
        $this->addForeignKey('parent_transaction_id', 'ParentTransactionId', 'INTEGER', 'transactions', 'id', false, null, null);
        $this->addColumn('amount', 'Amount', 'DECIMAL', true, 10, 0);
        $this->addColumn('isprojected', 'Isprojected', 'BOOLEAN', true, 1, false);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'Anthem\\Auth\\Model\\User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Category', 'Mockingbird\\Model\\TransactionCategory', RelationMap::MANY_TO_ONE, array('category_id' => 'id', ), null, null);
        $this->addRelation('Account', 'Mockingbird\\Model\\Account', RelationMap::MANY_TO_ONE, array('account_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('TargetAccount', 'Mockingbird\\Model\\Account', RelationMap::MANY_TO_ONE, array('target_account_id' => 'id', ), null, null);
        $this->addRelation('CounterTransaction', 'Mockingbird\\Model\\Transaction', RelationMap::MANY_TO_ONE, array('counter_transaction_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('CounterParty', 'Mockingbird\\Model\\CounterParty', RelationMap::MANY_TO_ONE, array('counter_party_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('ParentTransaction', 'Mockingbird\\Model\\Transaction', RelationMap::MANY_TO_ONE, array('parent_transaction_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('BackCounterTransactions', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'counter_transaction_id', ), 'CASCADE', null, 'BackCounterTransactionss');
        $this->addRelation('SubTransactions', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'parent_transaction_id', ), 'CASCADE', null, 'SubTransactionss');
        $this->addRelation('RefTransactionTag', 'Mockingbird\\Model\\RefTransactionTag', RelationMap::ONE_TO_MANY, array('id' => 'transaction_id', ), 'CASCADE', null, 'RefTransactionTags');
        $this->addRelation('TransactionTag', 'Mockingbird\\Model\\TransactionTag', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'TransactionTags');
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
            'timestampable' =>  array (
  'create_column' => 'created_at',
  'update_column' => 'updated_at',
  'disable_updated_at' => 'false',
),
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

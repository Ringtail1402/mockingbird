<?php

namespace Mockingbird\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'accounts' table.
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
class AccountTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Mockingbird.Model.map.AccountTableMap';

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
        $this->setName('accounts');
        $this->setPhpName('Account');
        $this->setClassname('Mockingbird\\Model\\Account');
        $this->setPackage('Mockingbird.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('user_id', 'UserId', 'INTEGER', 'users', 'id', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title', false)->setPrimaryString(true);
        $this->addForeignKey('currency_id', 'CurrencyId', 'INTEGER', 'currencies', 'id', true, null, null);
        $this->addColumn('initial_amount', 'InitialAmount', 'DECIMAL', true, 10, 0);
        $this->addColumn('isclosed', 'Isclosed', 'BOOLEAN', true, 1, false);
        $this->addColumn('isdebt', 'Isdebt', 'BOOLEAN', true, 1, false);
        $this->addColumn('iscredit', 'Iscredit', 'BOOLEAN', true, 1, false);
        $this->addColumn('color', 'Color', 'VARCHAR', false, 10, '#000000');
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
        $this->addRelation('Currency', 'Mockingbird\\Model\\Currency', RelationMap::MANY_TO_ONE, array('currency_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Transactions', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'account_id', ), 'CASCADE', null, 'Transactionss');
        $this->addRelation('TargetTransactions', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'target_account_id', ), null, null, 'TargetTransactionss');
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

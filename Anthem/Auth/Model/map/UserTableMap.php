<?php

namespace Anthem\Auth\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'users' table.
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
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Anthem.Auth.Model.map.UserTableMap';

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
        $this->setName('users');
        $this->setPhpName('User');
        $this->setClassname('Anthem\\Auth\\Model\\User');
        $this->setPackage('Anthem.Auth.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('email', 'Email', 'VARCHAR', true, 160, null);
        $this->getColumn('email', false)->setPrimaryString(true);
        $this->addColumn('algorithm', 'Algorithm', 'VARCHAR', false, 16, null);
        $this->addColumn('salt', 'Salt', 'VARCHAR', false, 160, null);
        $this->addColumn('password', 'Password', 'VARCHAR', false, 160, null);
        $this->addColumn('locked', 'Locked', 'VARCHAR', false, 255, null);
        $this->addColumn('is_superuser', 'IsSuperuser', 'BOOLEAN', true, 1, false);
        $this->addColumn('last_login', 'LastLogin', 'TIMESTAMP', false, null, null);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Feedback', 'AnthemCM\\Feedback\\Model\\Feedback', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Feedbacks');
        $this->addRelation('UserProfile', 'AnthemCM\\UserProfile\\Model\\UserProfile', RelationMap::ONE_TO_ONE, array('id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Key', 'Anthem\\Auth\\Model\\UserKey', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Keys');
        $this->addRelation('SocialAccount', 'Anthem\\Auth\\Model\\UserSocialAccount', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'SocialAccounts');
        $this->addRelation('Policy', 'Anthem\\Auth\\Model\\UserPolicy', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Policys');
        $this->addRelation('RefGroup', 'Anthem\\Auth\\Model\\RefUserGroup', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'RefGroups');
        $this->addRelation('Notification', 'Anthem\\Notify\\Model\\Notification', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Notifications');
        $this->addRelation('Setting', 'Anthem\\Settings\\Model\\Setting', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Settings');
        $this->addRelation('Account', 'Mockingbird\\Model\\Account', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Accounts');
        $this->addRelation('Transaction', 'Mockingbird\\Model\\Transaction', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Transactions');
        $this->addRelation('Category', 'Mockingbird\\Model\\TransactionCategory', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Categorys');
        $this->addRelation('Tag', 'Mockingbird\\Model\\TransactionTag', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Tags');
        $this->addRelation('CounterParty', 'Mockingbird\\Model\\CounterParty', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'CounterPartys');
        $this->addRelation('Budget', 'Mockingbird\\Model\\Budget', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), 'CASCADE', null, 'Budgets');
        $this->addRelation('Group', 'Anthem\\Auth\\Model\\Group', RelationMap::MANY_TO_MANY, array(), 'CASCADE', null, 'Groups');
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

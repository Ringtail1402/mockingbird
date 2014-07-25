<?php

namespace Mockingbird\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Anthem\Auth\Model\UserPeer;
use Mockingbird\Model\AccountPeer;
use Mockingbird\Model\CounterPartyPeer;
use Mockingbird\Model\RefTransactionTagPeer;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategoryPeer;
use Mockingbird\Model\TransactionPeer;
use Mockingbird\Model\map\TransactionTableMap;

/**
 * Base static class for performing query and update operations on the 'transactions' table.
 *
 *
 *
 * @package propel.generator.Mockingbird.Model.om
 */
abstract class BaseTransactionPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'transactions';

    /** the related Propel class for this table */
    const OM_CLASS = 'Mockingbird\\Model\\Transaction';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Mockingbird\\Model\\map\\TransactionTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 13;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 13;

    /** the column name for the id field */
    const ID = 'transactions.id';

    /** the column name for the user_id field */
    const USER_ID = 'transactions.user_id';

    /** the column name for the title field */
    const TITLE = 'transactions.title';

    /** the column name for the category_id field */
    const CATEGORY_ID = 'transactions.category_id';

    /** the column name for the account_id field */
    const ACCOUNT_ID = 'transactions.account_id';

    /** the column name for the target_account_id field */
    const TARGET_ACCOUNT_ID = 'transactions.target_account_id';

    /** the column name for the counter_transaction_id field */
    const COUNTER_TRANSACTION_ID = 'transactions.counter_transaction_id';

    /** the column name for the counter_party_id field */
    const COUNTER_PARTY_ID = 'transactions.counter_party_id';

    /** the column name for the parent_transaction_id field */
    const PARENT_TRANSACTION_ID = 'transactions.parent_transaction_id';

    /** the column name for the amount field */
    const AMOUNT = 'transactions.amount';

    /** the column name for the isprojected field */
    const ISPROJECTED = 'transactions.isprojected';

    /** the column name for the created_at field */
    const CREATED_AT = 'transactions.created_at';

    /** the column name for the updated_at field */
    const UPDATED_AT = 'transactions.updated_at';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Transaction objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Transaction[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. TransactionPeer::$fieldNames[TransactionPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'UserId', 'Title', 'CategoryId', 'AccountId', 'TargetAccountId', 'CounterTransactionId', 'CounterPartyId', 'ParentTransactionId', 'Amount', 'Isprojected', 'CreatedAt', 'UpdatedAt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'userId', 'title', 'categoryId', 'accountId', 'targetAccountId', 'counterTransactionId', 'counterPartyId', 'parentTransactionId', 'amount', 'isprojected', 'createdAt', 'updatedAt', ),
        BasePeer::TYPE_COLNAME => array (TransactionPeer::ID, TransactionPeer::USER_ID, TransactionPeer::TITLE, TransactionPeer::CATEGORY_ID, TransactionPeer::ACCOUNT_ID, TransactionPeer::TARGET_ACCOUNT_ID, TransactionPeer::COUNTER_TRANSACTION_ID, TransactionPeer::COUNTER_PARTY_ID, TransactionPeer::PARENT_TRANSACTION_ID, TransactionPeer::AMOUNT, TransactionPeer::ISPROJECTED, TransactionPeer::CREATED_AT, TransactionPeer::UPDATED_AT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'USER_ID', 'TITLE', 'CATEGORY_ID', 'ACCOUNT_ID', 'TARGET_ACCOUNT_ID', 'COUNTER_TRANSACTION_ID', 'COUNTER_PARTY_ID', 'PARENT_TRANSACTION_ID', 'AMOUNT', 'ISPROJECTED', 'CREATED_AT', 'UPDATED_AT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'user_id', 'title', 'category_id', 'account_id', 'target_account_id', 'counter_transaction_id', 'counter_party_id', 'parent_transaction_id', 'amount', 'isprojected', 'created_at', 'updated_at', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. TransactionPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'UserId' => 1, 'Title' => 2, 'CategoryId' => 3, 'AccountId' => 4, 'TargetAccountId' => 5, 'CounterTransactionId' => 6, 'CounterPartyId' => 7, 'ParentTransactionId' => 8, 'Amount' => 9, 'Isprojected' => 10, 'CreatedAt' => 11, 'UpdatedAt' => 12, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'userId' => 1, 'title' => 2, 'categoryId' => 3, 'accountId' => 4, 'targetAccountId' => 5, 'counterTransactionId' => 6, 'counterPartyId' => 7, 'parentTransactionId' => 8, 'amount' => 9, 'isprojected' => 10, 'createdAt' => 11, 'updatedAt' => 12, ),
        BasePeer::TYPE_COLNAME => array (TransactionPeer::ID => 0, TransactionPeer::USER_ID => 1, TransactionPeer::TITLE => 2, TransactionPeer::CATEGORY_ID => 3, TransactionPeer::ACCOUNT_ID => 4, TransactionPeer::TARGET_ACCOUNT_ID => 5, TransactionPeer::COUNTER_TRANSACTION_ID => 6, TransactionPeer::COUNTER_PARTY_ID => 7, TransactionPeer::PARENT_TRANSACTION_ID => 8, TransactionPeer::AMOUNT => 9, TransactionPeer::ISPROJECTED => 10, TransactionPeer::CREATED_AT => 11, TransactionPeer::UPDATED_AT => 12, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'USER_ID' => 1, 'TITLE' => 2, 'CATEGORY_ID' => 3, 'ACCOUNT_ID' => 4, 'TARGET_ACCOUNT_ID' => 5, 'COUNTER_TRANSACTION_ID' => 6, 'COUNTER_PARTY_ID' => 7, 'PARENT_TRANSACTION_ID' => 8, 'AMOUNT' => 9, 'ISPROJECTED' => 10, 'CREATED_AT' => 11, 'UPDATED_AT' => 12, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'user_id' => 1, 'title' => 2, 'category_id' => 3, 'account_id' => 4, 'target_account_id' => 5, 'counter_transaction_id' => 6, 'counter_party_id' => 7, 'parent_transaction_id' => 8, 'amount' => 9, 'isprojected' => 10, 'created_at' => 11, 'updated_at' => 12, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = TransactionPeer::getFieldNames($toType);
        $key = isset(TransactionPeer::$fieldKeys[$fromType][$name]) ? TransactionPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(TransactionPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, TransactionPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return TransactionPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *    $c->addAlias("alias1", TablePeer::TABLE_NAME);
     *    $c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. TransactionPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(TransactionPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(TransactionPeer::ID);
            $criteria->addSelectColumn(TransactionPeer::USER_ID);
            $criteria->addSelectColumn(TransactionPeer::TITLE);
            $criteria->addSelectColumn(TransactionPeer::CATEGORY_ID);
            $criteria->addSelectColumn(TransactionPeer::ACCOUNT_ID);
            $criteria->addSelectColumn(TransactionPeer::TARGET_ACCOUNT_ID);
            $criteria->addSelectColumn(TransactionPeer::COUNTER_TRANSACTION_ID);
            $criteria->addSelectColumn(TransactionPeer::COUNTER_PARTY_ID);
            $criteria->addSelectColumn(TransactionPeer::PARENT_TRANSACTION_ID);
            $criteria->addSelectColumn(TransactionPeer::AMOUNT);
            $criteria->addSelectColumn(TransactionPeer::ISPROJECTED);
            $criteria->addSelectColumn(TransactionPeer::CREATED_AT);
            $criteria->addSelectColumn(TransactionPeer::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.user_id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.category_id');
            $criteria->addSelectColumn($alias . '.account_id');
            $criteria->addSelectColumn($alias . '.target_account_id');
            $criteria->addSelectColumn($alias . '.counter_transaction_id');
            $criteria->addSelectColumn($alias . '.counter_party_id');
            $criteria->addSelectColumn($alias . '.parent_transaction_id');
            $criteria->addSelectColumn($alias . '.amount');
            $criteria->addSelectColumn($alias . '.isprojected');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(TransactionPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Transaction
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = TransactionPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return TransactionPeer::populateObjects(TransactionPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            TransactionPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Transaction $obj A Transaction object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            TransactionPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Transaction object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Transaction) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Transaction object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(TransactionPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Transaction Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(TransactionPeer::$instances[$key])) {
                return TransactionPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (TransactionPeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        TransactionPeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to transactions
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in TransactionPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TransactionPeer::clearInstancePool();
        // Invalidate objects in TransactionPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        TransactionPeer::clearInstancePool();
        // Invalidate objects in RefTransactionTagPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        RefTransactionTagPeer::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = TransactionPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = TransactionPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                TransactionPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     * @return array (Transaction object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = TransactionPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = TransactionPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + TransactionPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = TransactionPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            TransactionPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related User table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinUser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Category table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCategory(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Account table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAccount(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related TargetAccount table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinTargetAccount(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CounterParty table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCounterParty(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with their User objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinUser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol = TransactionPeer::NUM_HYDRATE_COLUMNS;
        UserPeer::addSelectColumns($criteria);

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = UserPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Transaction) to $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with their TransactionCategory objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCategory(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol = TransactionPeer::NUM_HYDRATE_COLUMNS;
        TransactionCategoryPeer::addSelectColumns($criteria);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = TransactionCategoryPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TransactionCategoryPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    TransactionCategoryPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Transaction) to $obj2 (TransactionCategory)
                $obj2->addTransactions($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with their Account objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAccount(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol = TransactionPeer::NUM_HYDRATE_COLUMNS;
        AccountPeer::addSelectColumns($criteria);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = AccountPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = AccountPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    AccountPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Transaction) to $obj2 (Account)
                $obj2->addTransactions($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with their Account objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinTargetAccount(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol = TransactionPeer::NUM_HYDRATE_COLUMNS;
        AccountPeer::addSelectColumns($criteria);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = AccountPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = AccountPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    AccountPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Transaction) to $obj2 (Account)
                $obj2->addTargetTransactions($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with their CounterParty objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCounterParty(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol = TransactionPeer::NUM_HYDRATE_COLUMNS;
        CounterPartyPeer::addSelectColumns($criteria);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CounterPartyPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CounterPartyPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CounterPartyPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Transaction) to $obj2 (CounterParty)
                $obj2->addTransactions($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Transaction objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + AccountPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol7 = $startcol6 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined User rows

            $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = UserPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);
            } // if joined row not null

            // Add objects for joined TransactionCategory rows

            $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);
            } // if joined row not null

            // Add objects for joined Account rows

            $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = AccountPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTransactions($obj1);
            } // if joined row not null

            // Add objects for joined Account rows

            $key5 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol5);
            if ($key5 !== null) {
                $obj5 = AccountPeer::getInstanceFromPool($key5);
                if (!$obj5) {

                    $cls = AccountPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    AccountPeer::addInstanceToPool($obj5, $key5);
                } // if obj5 loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (Account)
                $obj5->addTargetTransactions($obj1);
            } // if joined row not null

            // Add objects for joined CounterParty rows

            $key6 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol6);
            if ($key6 !== null) {
                $obj6 = CounterPartyPeer::getInstanceFromPool($key6);
                if (!$obj6) {

                    $cls = CounterPartyPeer::getOMClass();

                    $obj6 = new $cls();
                    $obj6->hydrate($row, $startcol6);
                    CounterPartyPeer::addInstanceToPool($obj6, $key6);
                } // if obj6 loaded

                // Add the $obj1 (Transaction) to the collection in $obj6 (CounterParty)
                $obj6->addTransactions($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related User table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptUser(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Category table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCategory(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related Account table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptAccount(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related TargetAccount table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptTargetAccount(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CounterTransaction table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCounterTransaction(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CounterParty table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCounterParty(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related ParentTransaction table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptParentTransaction(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            TransactionPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except User.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptUser(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined TransactionCategory rows

                $key2 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TransactionCategoryPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TransactionCategoryPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (TransactionCategory)
                $obj2->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key3 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = AccountPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = AccountPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    AccountPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (Account)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = AccountPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTargetTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key5 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = CounterPartyPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    CounterPartyPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (CounterParty)
                $obj5->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except Category.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCategory(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key3 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = AccountPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = AccountPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    AccountPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (Account)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = AccountPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTargetTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key5 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = CounterPartyPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    CounterPartyPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (CounterParty)
                $obj5->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except Account.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptAccount(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined TransactionCategory rows

                $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key4 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = CounterPartyPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CounterPartyPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (CounterParty)
                $obj4->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except TargetAccount.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptTargetAccount(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined TransactionCategory rows

                $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key4 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = CounterPartyPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    CounterPartyPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (CounterParty)
                $obj4->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except CounterTransaction.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCounterTransaction(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + AccountPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol7 = $startcol6 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined TransactionCategory rows

                $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = AccountPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key5 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = AccountPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = AccountPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    AccountPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (Account)
                $obj5->addTargetTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key6 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol6);
                if ($key6 !== null) {
                    $obj6 = CounterPartyPeer::getInstanceFromPool($key6);
                    if (!$obj6) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj6 = new $cls();
                    $obj6->hydrate($row, $startcol6);
                    CounterPartyPeer::addInstanceToPool($obj6, $key6);
                } // if $obj6 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj6 (CounterParty)
                $obj6->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except CounterParty.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCounterParty(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + AccountPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined TransactionCategory rows

                $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = AccountPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key5 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = AccountPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = AccountPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    AccountPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (Account)
                $obj5->addTargetTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Transaction objects pre-filled with all related objects except ParentTransaction.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Transaction objects.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptParentTransaction(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(TransactionPeer::DATABASE_NAME);
        }

        TransactionPeer::addSelectColumns($criteria);
        $startcol2 = TransactionPeer::NUM_HYDRATE_COLUMNS;

        UserPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + UserPeer::NUM_HYDRATE_COLUMNS;

        TransactionCategoryPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + TransactionCategoryPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + AccountPeer::NUM_HYDRATE_COLUMNS;

        AccountPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + AccountPeer::NUM_HYDRATE_COLUMNS;

        CounterPartyPeer::addSelectColumns($criteria);
        $startcol7 = $startcol6 + CounterPartyPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(TransactionPeer::USER_ID, UserPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::CATEGORY_ID, TransactionCategoryPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::TARGET_ACCOUNT_ID, AccountPeer::ID, $join_behavior);

        $criteria->addJoin(TransactionPeer::COUNTER_PARTY_ID, CounterPartyPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = TransactionPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = TransactionPeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = TransactionPeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                TransactionPeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined User rows

                $key2 = UserPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = UserPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = UserPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    UserPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj2 (User)
                $obj2->addTransaction($obj1);

            } // if joined row is not null

                // Add objects for joined TransactionCategory rows

                $key3 = TransactionCategoryPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = TransactionCategoryPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = TransactionCategoryPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    TransactionCategoryPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj3 (TransactionCategory)
                $obj3->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key4 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = AccountPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = AccountPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    AccountPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj4 (Account)
                $obj4->addTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined Account rows

                $key5 = AccountPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = AccountPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = AccountPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    AccountPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj5 (Account)
                $obj5->addTargetTransactions($obj1);

            } // if joined row is not null

                // Add objects for joined CounterParty rows

                $key6 = CounterPartyPeer::getPrimaryKeyHashFromRow($row, $startcol6);
                if ($key6 !== null) {
                    $obj6 = CounterPartyPeer::getInstanceFromPool($key6);
                    if (!$obj6) {

                        $cls = CounterPartyPeer::getOMClass();

                    $obj6 = new $cls();
                    $obj6->hydrate($row, $startcol6);
                    CounterPartyPeer::addInstanceToPool($obj6, $key6);
                } // if $obj6 already loaded

                // Add the $obj1 (Transaction) to the collection in $obj6 (CounterParty)
                $obj6->addTransactions($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(TransactionPeer::DATABASE_NAME)->getTable(TransactionPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseTransactionPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseTransactionPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Mockingbird\Model\map\TransactionTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return TransactionPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Transaction or Criteria object.
     *
     * @param      mixed $values Criteria or Transaction object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Transaction object
        }

        if ($criteria->containsKey(TransactionPeer::ID) && $criteria->keyContainsValue(TransactionPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.TransactionPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Transaction or Criteria object.
     *
     * @param      mixed $values Criteria or Transaction object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(TransactionPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(TransactionPeer::ID);
            $value = $criteria->remove(TransactionPeer::ID);
            if ($value) {
                $selectCriteria->add(TransactionPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(TransactionPeer::TABLE_NAME);
            }

        } else { // $values is Transaction object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the transactions table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(TransactionPeer::TABLE_NAME, $con, TransactionPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            TransactionPeer::clearInstancePool();
            TransactionPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Transaction or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Transaction object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *        if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            TransactionPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Transaction) { // it's a model object
            // invalidate the cache for this single object
            TransactionPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(TransactionPeer::DATABASE_NAME);
            $criteria->add(TransactionPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                TransactionPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(TransactionPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            TransactionPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Transaction object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Transaction $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(TransactionPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(TransactionPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(TransactionPeer::DATABASE_NAME, TransactionPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Transaction
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = TransactionPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(TransactionPeer::DATABASE_NAME);
        $criteria->add(TransactionPeer::ID, $pk);

        $v = TransactionPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Transaction[]
     * @throws PropelException Any exceptions caught during processing will be
     *     rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(TransactionPeer::DATABASE_NAME);
            $criteria->add(TransactionPeer::ID, $pks, Criteria::IN);
            $objs = TransactionPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

}

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseTransactionPeer::buildTableMap();


<?php

namespace Mockingbird\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \DateTime;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelDateTime;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserQuery;
use Mockingbird\Model\Account;
use Mockingbird\Model\AccountQuery;
use Mockingbird\Model\CounterParty;
use Mockingbird\Model\CounterPartyQuery;
use Mockingbird\Model\RefTransactionTag;
use Mockingbird\Model\RefTransactionTagQuery;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategory;
use Mockingbird\Model\TransactionCategoryQuery;
use Mockingbird\Model\TransactionPeer;
use Mockingbird\Model\TransactionQuery;
use Mockingbird\Model\TransactionTag;
use Mockingbird\Model\TransactionTagQuery;

/**
 * Base class that represents a row from the 'transactions' table.
 *
 *
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseTransaction extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Mockingbird\\Model\\TransactionPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TransactionPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the user_id field.
     * @var        int
     */
    protected $user_id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the category_id field.
     * @var        int
     */
    protected $category_id;

    /**
     * The value for the account_id field.
     * @var        int
     */
    protected $account_id;

    /**
     * The value for the target_account_id field.
     * @var        int
     */
    protected $target_account_id;

    /**
     * The value for the counter_transaction_id field.
     * @var        int
     */
    protected $counter_transaction_id;

    /**
     * The value for the counter_party_id field.
     * @var        int
     */
    protected $counter_party_id;

    /**
     * The value for the parent_transaction_id field.
     * @var        int
     */
    protected $parent_transaction_id;

    /**
     * The value for the amount field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $amount;

    /**
     * The value for the isprojected field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $isprojected;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        User
     */
    protected $aUser;

    /**
     * @var        TransactionCategory
     */
    protected $aCategory;

    /**
     * @var        Account
     */
    protected $aAccount;

    /**
     * @var        Account
     */
    protected $aTargetAccount;

    /**
     * @var        Transaction
     */
    protected $aCounterTransaction;

    /**
     * @var        CounterParty
     */
    protected $aCounterParty;

    /**
     * @var        Transaction
     */
    protected $aParentTransaction;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collBackCounterTransactionss;
    protected $collBackCounterTransactionssPartial;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collSubTransactionss;
    protected $collSubTransactionssPartial;

    /**
     * @var        PropelObjectCollection|RefTransactionTag[] Collection to store aggregation of RefTransactionTag objects.
     */
    protected $collRefTransactionTags;
    protected $collRefTransactionTagsPartial;

    /**
     * @var        PropelObjectCollection|TransactionTag[] Collection to store aggregation of TransactionTag objects.
     */
    protected $collTransactionTags;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $transactionTagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $backCounterTransactionssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $subTransactionssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $refTransactionTagsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->amount = '0';
        $this->isprojected = false;
    }

    /**
     * Initializes internal state of BaseTransaction object.
     * @see        applyDefaults()
     */
    public function __construct()
    {
        parent::__construct();
        $this->applyDefaultValues();
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [user_id] column value.
     *
     * @return int
     */
    public function getUserId()
    {

        return $this->user_id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [category_id] column value.
     *
     * @return int
     */
    public function getCategoryId()
    {

        return $this->category_id;
    }

    /**
     * Get the [account_id] column value.
     *
     * @return int
     */
    public function getAccountId()
    {

        return $this->account_id;
    }

    /**
     * Get the [target_account_id] column value.
     *
     * @return int
     */
    public function getTargetAccountId()
    {

        return $this->target_account_id;
    }

    /**
     * Get the [counter_transaction_id] column value.
     *
     * @return int
     */
    public function getCounterTransactionId()
    {

        return $this->counter_transaction_id;
    }

    /**
     * Get the [counter_party_id] column value.
     *
     * @return int
     */
    public function getCounterPartyId()
    {

        return $this->counter_party_id;
    }

    /**
     * Get the [parent_transaction_id] column value.
     *
     * @return int
     */
    public function getParentTransactionId()
    {

        return $this->parent_transaction_id;
    }

    /**
     * Get the [amount] column value.
     *
     * @return string
     */
    public function getAmount()
    {

        return $this->amount;
    }

    /**
     * Get the [isprojected] column value.
     *
     * @return boolean
     */
    public function getIsprojected()
    {

        return $this->isprojected;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *         If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->created_at === null) {
            return null;
        }

        if ($this->created_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->created_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *         If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = 'Y-m-d H:i:s')
    {
        if ($this->updated_at === null) {
            return null;
        }

        if ($this->updated_at === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->updated_at);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
        }

        if ($format === null) {
            // Because propel.useDateTimeClass is true, we return a DateTime object.
            return $dt;
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $dt->format('U'));
        }

        return $dt->format($format);

    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = TransactionPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = TransactionPeer::USER_ID;
        }

        if ($this->aUser !== null && $this->aUser->getId() !== $v) {
            $this->aUser = null;
        }


        return $this;
    } // setUserId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = TransactionPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [category_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setCategoryId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->category_id !== $v) {
            $this->category_id = $v;
            $this->modifiedColumns[] = TransactionPeer::CATEGORY_ID;
        }

        if ($this->aCategory !== null && $this->aCategory->getId() !== $v) {
            $this->aCategory = null;
        }


        return $this;
    } // setCategoryId()

    /**
     * Set the value of [account_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setAccountId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->account_id !== $v) {
            $this->account_id = $v;
            $this->modifiedColumns[] = TransactionPeer::ACCOUNT_ID;
        }

        if ($this->aAccount !== null && $this->aAccount->getId() !== $v) {
            $this->aAccount = null;
        }


        return $this;
    } // setAccountId()

    /**
     * Set the value of [target_account_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setTargetAccountId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->target_account_id !== $v) {
            $this->target_account_id = $v;
            $this->modifiedColumns[] = TransactionPeer::TARGET_ACCOUNT_ID;
        }

        if ($this->aTargetAccount !== null && $this->aTargetAccount->getId() !== $v) {
            $this->aTargetAccount = null;
        }


        return $this;
    } // setTargetAccountId()

    /**
     * Set the value of [counter_transaction_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setCounterTransactionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->counter_transaction_id !== $v) {
            $this->counter_transaction_id = $v;
            $this->modifiedColumns[] = TransactionPeer::COUNTER_TRANSACTION_ID;
        }

        if ($this->aCounterTransaction !== null && $this->aCounterTransaction->getId() !== $v) {
            $this->aCounterTransaction = null;
        }


        return $this;
    } // setCounterTransactionId()

    /**
     * Set the value of [counter_party_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setCounterPartyId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->counter_party_id !== $v) {
            $this->counter_party_id = $v;
            $this->modifiedColumns[] = TransactionPeer::COUNTER_PARTY_ID;
        }

        if ($this->aCounterParty !== null && $this->aCounterParty->getId() !== $v) {
            $this->aCounterParty = null;
        }


        return $this;
    } // setCounterPartyId()

    /**
     * Set the value of [parent_transaction_id] column.
     *
     * @param  int $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setParentTransactionId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->parent_transaction_id !== $v) {
            $this->parent_transaction_id = $v;
            $this->modifiedColumns[] = TransactionPeer::PARENT_TRANSACTION_ID;
        }

        if ($this->aParentTransaction !== null && $this->aParentTransaction->getId() !== $v) {
            $this->aParentTransaction = null;
        }


        return $this;
    } // setParentTransactionId()

    /**
     * Set the value of [amount] column.
     *
     * @param  string $v new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setAmount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->amount !== $v) {
            $this->amount = $v;
            $this->modifiedColumns[] = TransactionPeer::AMOUNT;
        }


        return $this;
    } // setAmount()

    /**
     * Sets the value of the [isprojected] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Transaction The current object (for fluent API support)
     */
    public function setIsprojected($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->isprojected !== $v) {
            $this->isprojected = $v;
            $this->modifiedColumns[] = TransactionPeer::ISPROJECTED;
        }


        return $this;
    } // setIsprojected()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Transaction The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = TransactionPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Transaction The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = TransactionPeer::UPDATED_AT;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->amount !== '0') {
                return false;
            }

            if ($this->isprojected !== false) {
                return false;
            }

        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->user_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->title = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->category_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->account_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->target_account_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->counter_transaction_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->counter_party_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->parent_transaction_id = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->amount = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->isprojected = ($row[$startcol + 10] !== null) ? (boolean) $row[$startcol + 10] : null;
            $this->created_at = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->updated_at = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 13; // 13 = TransactionPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Transaction object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aUser !== null && $this->user_id !== $this->aUser->getId()) {
            $this->aUser = null;
        }
        if ($this->aCategory !== null && $this->category_id !== $this->aCategory->getId()) {
            $this->aCategory = null;
        }
        if ($this->aAccount !== null && $this->account_id !== $this->aAccount->getId()) {
            $this->aAccount = null;
        }
        if ($this->aTargetAccount !== null && $this->target_account_id !== $this->aTargetAccount->getId()) {
            $this->aTargetAccount = null;
        }
        if ($this->aCounterTransaction !== null && $this->counter_transaction_id !== $this->aCounterTransaction->getId()) {
            $this->aCounterTransaction = null;
        }
        if ($this->aCounterParty !== null && $this->counter_party_id !== $this->aCounterParty->getId()) {
            $this->aCounterParty = null;
        }
        if ($this->aParentTransaction !== null && $this->parent_transaction_id !== $this->aParentTransaction->getId()) {
            $this->aParentTransaction = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TransactionPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->aCategory = null;
            $this->aAccount = null;
            $this->aTargetAccount = null;
            $this->aCounterTransaction = null;
            $this->aCounterParty = null;
            $this->aParentTransaction = null;
            $this->collBackCounterTransactionss = null;

            $this->collSubTransactionss = null;

            $this->collRefTransactionTags = null;

            $this->collTransactionTags = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TransactionQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
        // timestampable behavior
        if (!$this->isColumnModified(TransactionPeer::CREATED_AT))
        {
            $this->setCreatedAt(time());
        }
        if (!$this->isColumnModified(TransactionPeer::UPDATED_AT))
        {
            $this->setUpdatedAt(time());
        }
            } else {
                $ret = $ret && $this->preUpdate($con);
        // timestampable behavior
        if ($this->isModified() && !$this->isColumnModified(TransactionPeer::UPDATED_AT))
        {
            $this->setUpdatedAt(time());
        }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                TransactionPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUser !== null) {
                if ($this->aUser->isModified() || $this->aUser->isNew()) {
                    $affectedRows += $this->aUser->save($con);
                }
                $this->setUser($this->aUser);
            }

            if ($this->aCategory !== null) {
                if ($this->aCategory->isModified() || $this->aCategory->isNew()) {
                    $affectedRows += $this->aCategory->save($con);
                }
                $this->setCategory($this->aCategory);
            }

            if ($this->aAccount !== null) {
                if ($this->aAccount->isModified() || $this->aAccount->isNew()) {
                    $affectedRows += $this->aAccount->save($con);
                }
                $this->setAccount($this->aAccount);
            }

            if ($this->aTargetAccount !== null) {
                if ($this->aTargetAccount->isModified() || $this->aTargetAccount->isNew()) {
                    $affectedRows += $this->aTargetAccount->save($con);
                }
                $this->setTargetAccount($this->aTargetAccount);
            }

            if ($this->aCounterTransaction !== null) {
                if ($this->aCounterTransaction->isModified() || $this->aCounterTransaction->isNew()) {
                    $affectedRows += $this->aCounterTransaction->save($con);
                }
                $this->setCounterTransaction($this->aCounterTransaction);
            }

            if ($this->aCounterParty !== null) {
                if ($this->aCounterParty->isModified() || $this->aCounterParty->isNew()) {
                    $affectedRows += $this->aCounterParty->save($con);
                }
                $this->setCounterParty($this->aCounterParty);
            }

            if ($this->aParentTransaction !== null) {
                if ($this->aParentTransaction->isModified() || $this->aParentTransaction->isNew()) {
                    $affectedRows += $this->aParentTransaction->save($con);
                }
                $this->setParentTransaction($this->aParentTransaction);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->transactionTagsScheduledForDeletion !== null) {
                if (!$this->transactionTagsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->transactionTagsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    RefTransactionTagQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->transactionTagsScheduledForDeletion = null;
                }

                foreach ($this->getTransactionTags() as $transactionTag) {
                    if ($transactionTag->isModified()) {
                        $transactionTag->save($con);
                    }
                }
            } elseif ($this->collTransactionTags) {
                foreach ($this->collTransactionTags as $transactionTag) {
                    if ($transactionTag->isModified()) {
                        $transactionTag->save($con);
                    }
                }
            }

            if ($this->backCounterTransactionssScheduledForDeletion !== null) {
                if (!$this->backCounterTransactionssScheduledForDeletion->isEmpty()) {
                    TransactionQuery::create()
                        ->filterByPrimaryKeys($this->backCounterTransactionssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->backCounterTransactionssScheduledForDeletion = null;
                }
            }

            if ($this->collBackCounterTransactionss !== null) {
                foreach ($this->collBackCounterTransactionss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->subTransactionssScheduledForDeletion !== null) {
                if (!$this->subTransactionssScheduledForDeletion->isEmpty()) {
                    TransactionQuery::create()
                        ->filterByPrimaryKeys($this->subTransactionssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->subTransactionssScheduledForDeletion = null;
                }
            }

            if ($this->collSubTransactionss !== null) {
                foreach ($this->collSubTransactionss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->refTransactionTagsScheduledForDeletion !== null) {
                if (!$this->refTransactionTagsScheduledForDeletion->isEmpty()) {
                    RefTransactionTagQuery::create()
                        ->filterByPrimaryKeys($this->refTransactionTagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->refTransactionTagsScheduledForDeletion = null;
                }
            }

            if ($this->collRefTransactionTags !== null) {
                foreach ($this->collRefTransactionTags as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = TransactionPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TransactionPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TransactionPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(TransactionPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(TransactionPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(TransactionPeer::CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`category_id`';
        }
        if ($this->isColumnModified(TransactionPeer::ACCOUNT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`account_id`';
        }
        if ($this->isColumnModified(TransactionPeer::TARGET_ACCOUNT_ID)) {
            $modifiedColumns[':p' . $index++]  = '`target_account_id`';
        }
        if ($this->isColumnModified(TransactionPeer::COUNTER_TRANSACTION_ID)) {
            $modifiedColumns[':p' . $index++]  = '`counter_transaction_id`';
        }
        if ($this->isColumnModified(TransactionPeer::COUNTER_PARTY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`counter_party_id`';
        }
        if ($this->isColumnModified(TransactionPeer::PARENT_TRANSACTION_ID)) {
            $modifiedColumns[':p' . $index++]  = '`parent_transaction_id`';
        }
        if ($this->isColumnModified(TransactionPeer::AMOUNT)) {
            $modifiedColumns[':p' . $index++]  = '`amount`';
        }
        if ($this->isColumnModified(TransactionPeer::ISPROJECTED)) {
            $modifiedColumns[':p' . $index++]  = '`isprojected`';
        }
        if ($this->isColumnModified(TransactionPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(TransactionPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `transactions` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
            $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`user_id`':
            $stmt->bindValue($identifier, $this->user_id, PDO::PARAM_INT);
                        break;
                    case '`title`':
            $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`category_id`':
            $stmt->bindValue($identifier, $this->category_id, PDO::PARAM_INT);
                        break;
                    case '`account_id`':
            $stmt->bindValue($identifier, $this->account_id, PDO::PARAM_INT);
                        break;
                    case '`target_account_id`':
            $stmt->bindValue($identifier, $this->target_account_id, PDO::PARAM_INT);
                        break;
                    case '`counter_transaction_id`':
            $stmt->bindValue($identifier, $this->counter_transaction_id, PDO::PARAM_INT);
                        break;
                    case '`counter_party_id`':
            $stmt->bindValue($identifier, $this->counter_party_id, PDO::PARAM_INT);
                        break;
                    case '`parent_transaction_id`':
            $stmt->bindValue($identifier, $this->parent_transaction_id, PDO::PARAM_INT);
                        break;
                    case '`amount`':
            $stmt->bindValue($identifier, $this->amount, PDO::PARAM_STR);
                        break;
                    case '`isprojected`':
            $stmt->bindValue($identifier, (int) $this->isprojected, PDO::PARAM_INT);
                        break;
                    case '`created_at`':
            $stmt->bindValue($identifier, $this->created_at, PDO::PARAM_STR);
                        break;
                    case '`updated_at`':
            $stmt->bindValue($identifier, $this->updated_at, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
      $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aUser !== null) {
                if (!$this->aUser->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aUser->getValidationFailures());
                }
            }

            if ($this->aCategory !== null) {
                if (!$this->aCategory->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCategory->getValidationFailures());
                }
            }

            if ($this->aAccount !== null) {
                if (!$this->aAccount->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aAccount->getValidationFailures());
                }
            }

            if ($this->aTargetAccount !== null) {
                if (!$this->aTargetAccount->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aTargetAccount->getValidationFailures());
                }
            }

            if ($this->aCounterTransaction !== null) {
                if (!$this->aCounterTransaction->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCounterTransaction->getValidationFailures());
                }
            }

            if ($this->aCounterParty !== null) {
                if (!$this->aCounterParty->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCounterParty->getValidationFailures());
                }
            }

            if ($this->aParentTransaction !== null) {
                if (!$this->aParentTransaction->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aParentTransaction->getValidationFailures());
                }
            }


            if (($retval = TransactionPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collBackCounterTransactionss !== null) {
                    foreach ($this->collBackCounterTransactionss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSubTransactionss !== null) {
                    foreach ($this->collSubTransactionss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collRefTransactionTags !== null) {
                    foreach ($this->collRefTransactionTags as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = TransactionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUserId();
                break;
            case 2:
                return $this->getTitle();
                break;
            case 3:
                return $this->getCategoryId();
                break;
            case 4:
                return $this->getAccountId();
                break;
            case 5:
                return $this->getTargetAccountId();
                break;
            case 6:
                return $this->getCounterTransactionId();
                break;
            case 7:
                return $this->getCounterPartyId();
                break;
            case 8:
                return $this->getParentTransactionId();
                break;
            case 9:
                return $this->getAmount();
                break;
            case 10:
                return $this->getIsprojected();
                break;
            case 11:
                return $this->getCreatedAt();
                break;
            case 12:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Transaction'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Transaction'][$this->getPrimaryKey()] = true;
        $keys = TransactionPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getCategoryId(),
            $keys[4] => $this->getAccountId(),
            $keys[5] => $this->getTargetAccountId(),
            $keys[6] => $this->getCounterTransactionId(),
            $keys[7] => $this->getCounterPartyId(),
            $keys[8] => $this->getParentTransactionId(),
            $keys[9] => $this->getAmount(),
            $keys[10] => $this->getIsprojected(),
            $keys[11] => $this->getCreatedAt(),
            $keys[12] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCategory) {
                $result['Category'] = $this->aCategory->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aAccount) {
                $result['Account'] = $this->aAccount->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTargetAccount) {
                $result['TargetAccount'] = $this->aTargetAccount->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCounterTransaction) {
                $result['CounterTransaction'] = $this->aCounterTransaction->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCounterParty) {
                $result['CounterParty'] = $this->aCounterParty->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aParentTransaction) {
                $result['ParentTransaction'] = $this->aParentTransaction->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collBackCounterTransactionss) {
                $result['BackCounterTransactionss'] = $this->collBackCounterTransactionss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSubTransactionss) {
                $result['SubTransactionss'] = $this->collSubTransactionss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRefTransactionTags) {
                $result['RefTransactionTags'] = $this->collRefTransactionTags->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = TransactionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUserId($value);
                break;
            case 2:
                $this->setTitle($value);
                break;
            case 3:
                $this->setCategoryId($value);
                break;
            case 4:
                $this->setAccountId($value);
                break;
            case 5:
                $this->setTargetAccountId($value);
                break;
            case 6:
                $this->setCounterTransactionId($value);
                break;
            case 7:
                $this->setCounterPartyId($value);
                break;
            case 8:
                $this->setParentTransactionId($value);
                break;
            case 9:
                $this->setAmount($value);
                break;
            case 10:
                $this->setIsprojected($value);
                break;
            case 11:
                $this->setCreatedAt($value);
                break;
            case 12:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = TransactionPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCategoryId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setAccountId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setTargetAccountId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCounterTransactionId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCounterPartyId($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setParentTransactionId($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setAmount($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setIsprojected($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setCreatedAt($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setUpdatedAt($arr[$keys[12]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TransactionPeer::DATABASE_NAME);

        if ($this->isColumnModified(TransactionPeer::ID)) $criteria->add(TransactionPeer::ID, $this->id);
        if ($this->isColumnModified(TransactionPeer::USER_ID)) $criteria->add(TransactionPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(TransactionPeer::TITLE)) $criteria->add(TransactionPeer::TITLE, $this->title);
        if ($this->isColumnModified(TransactionPeer::CATEGORY_ID)) $criteria->add(TransactionPeer::CATEGORY_ID, $this->category_id);
        if ($this->isColumnModified(TransactionPeer::ACCOUNT_ID)) $criteria->add(TransactionPeer::ACCOUNT_ID, $this->account_id);
        if ($this->isColumnModified(TransactionPeer::TARGET_ACCOUNT_ID)) $criteria->add(TransactionPeer::TARGET_ACCOUNT_ID, $this->target_account_id);
        if ($this->isColumnModified(TransactionPeer::COUNTER_TRANSACTION_ID)) $criteria->add(TransactionPeer::COUNTER_TRANSACTION_ID, $this->counter_transaction_id);
        if ($this->isColumnModified(TransactionPeer::COUNTER_PARTY_ID)) $criteria->add(TransactionPeer::COUNTER_PARTY_ID, $this->counter_party_id);
        if ($this->isColumnModified(TransactionPeer::PARENT_TRANSACTION_ID)) $criteria->add(TransactionPeer::PARENT_TRANSACTION_ID, $this->parent_transaction_id);
        if ($this->isColumnModified(TransactionPeer::AMOUNT)) $criteria->add(TransactionPeer::AMOUNT, $this->amount);
        if ($this->isColumnModified(TransactionPeer::ISPROJECTED)) $criteria->add(TransactionPeer::ISPROJECTED, $this->isprojected);
        if ($this->isColumnModified(TransactionPeer::CREATED_AT)) $criteria->add(TransactionPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(TransactionPeer::UPDATED_AT)) $criteria->add(TransactionPeer::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(TransactionPeer::DATABASE_NAME);
        $criteria->add(TransactionPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Transaction (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setCategoryId($this->getCategoryId());
        $copyObj->setAccountId($this->getAccountId());
        $copyObj->setTargetAccountId($this->getTargetAccountId());
        $copyObj->setCounterTransactionId($this->getCounterTransactionId());
        $copyObj->setCounterPartyId($this->getCounterPartyId());
        $copyObj->setParentTransactionId($this->getParentTransactionId());
        $copyObj->setAmount($this->getAmount());
        $copyObj->setIsprojected($this->getIsprojected());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getBackCounterTransactionss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBackCounterTransactions($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSubTransactionss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSubTransactions($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRefTransactionTags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRefTransactionTag($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Transaction Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return TransactionPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TransactionPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param                  User $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUser(User $v = null)
    {
        if ($v === null) {
            $this->setUserId(NULL);
        } else {
            $this->setUserId($v->getId());
        }

        $this->aUser = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the User object, it will not be re-added.
        if ($v !== null) {
            $v->addTransaction($this);
        }


        return $this;
    }


    /**
     * Get the associated User object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return User The associated User object.
     * @throws PropelException
     */
    public function getUser(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aUser === null && ($this->user_id !== null) && $doQuery) {
            $this->aUser = UserQuery::create()->findPk($this->user_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUser->addTransactions($this);
             */
        }

        return $this->aUser;
    }

    /**
     * Declares an association between this object and a TransactionCategory object.
     *
     * @param                  TransactionCategory $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategory(TransactionCategory $v = null)
    {
        if ($v === null) {
            $this->setCategoryId(NULL);
        } else {
            $this->setCategoryId($v->getId());
        }

        $this->aCategory = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the TransactionCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated TransactionCategory object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return TransactionCategory The associated TransactionCategory object.
     * @throws PropelException
     */
    public function getCategory(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCategory === null && ($this->category_id !== null) && $doQuery) {
            $this->aCategory = TransactionCategoryQuery::create()->findPk($this->category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategory->addTransactionss($this);
             */
        }

        return $this->aCategory;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAccount(Account $v = null)
    {
        if ($v === null) {
            $this->setAccountId(NULL);
        } else {
            $this->setAccountId($v->getId());
        }

        $this->aAccount = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated Account object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Account The associated Account object.
     * @throws PropelException
     */
    public function getAccount(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aAccount === null && ($this->account_id !== null) && $doQuery) {
            $this->aAccount = AccountQuery::create()->findPk($this->account_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAccount->addTransactionss($this);
             */
        }

        return $this->aAccount;
    }

    /**
     * Declares an association between this object and a Account object.
     *
     * @param                  Account $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTargetAccount(Account $v = null)
    {
        if ($v === null) {
            $this->setTargetAccountId(NULL);
        } else {
            $this->setTargetAccountId($v->getId());
        }

        $this->aTargetAccount = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Account object, it will not be re-added.
        if ($v !== null) {
            $v->addTargetTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated Account object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Account The associated Account object.
     * @throws PropelException
     */
    public function getTargetAccount(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aTargetAccount === null && ($this->target_account_id !== null) && $doQuery) {
            $this->aTargetAccount = AccountQuery::create()->findPk($this->target_account_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTargetAccount->addTargetTransactionss($this);
             */
        }

        return $this->aTargetAccount;
    }

    /**
     * Declares an association between this object and a Transaction object.
     *
     * @param                  Transaction $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCounterTransaction(Transaction $v = null)
    {
        if ($v === null) {
            $this->setCounterTransactionId(NULL);
        } else {
            $this->setCounterTransactionId($v->getId());
        }

        $this->aCounterTransaction = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Transaction object, it will not be re-added.
        if ($v !== null) {
            $v->addBackCounterTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated Transaction object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Transaction The associated Transaction object.
     * @throws PropelException
     */
    public function getCounterTransaction(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCounterTransaction === null && ($this->counter_transaction_id !== null) && $doQuery) {
            $this->aCounterTransaction = TransactionQuery::create()->findPk($this->counter_transaction_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCounterTransaction->addBackCounterTransactionss($this);
             */
        }

        return $this->aCounterTransaction;
    }

    /**
     * Declares an association between this object and a CounterParty object.
     *
     * @param                  CounterParty $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCounterParty(CounterParty $v = null)
    {
        if ($v === null) {
            $this->setCounterPartyId(NULL);
        } else {
            $this->setCounterPartyId($v->getId());
        }

        $this->aCounterParty = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the CounterParty object, it will not be re-added.
        if ($v !== null) {
            $v->addTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated CounterParty object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return CounterParty The associated CounterParty object.
     * @throws PropelException
     */
    public function getCounterParty(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCounterParty === null && ($this->counter_party_id !== null) && $doQuery) {
            $this->aCounterParty = CounterPartyQuery::create()->findPk($this->counter_party_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCounterParty->addTransactionss($this);
             */
        }

        return $this->aCounterParty;
    }

    /**
     * Declares an association between this object and a Transaction object.
     *
     * @param                  Transaction $v
     * @return Transaction The current object (for fluent API support)
     * @throws PropelException
     */
    public function setParentTransaction(Transaction $v = null)
    {
        if ($v === null) {
            $this->setParentTransactionId(NULL);
        } else {
            $this->setParentTransactionId($v->getId());
        }

        $this->aParentTransaction = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Transaction object, it will not be re-added.
        if ($v !== null) {
            $v->addSubTransactions($this);
        }


        return $this;
    }


    /**
     * Get the associated Transaction object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Transaction The associated Transaction object.
     * @throws PropelException
     */
    public function getParentTransaction(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aParentTransaction === null && ($this->parent_transaction_id !== null) && $doQuery) {
            $this->aParentTransaction = TransactionQuery::create()->findPk($this->parent_transaction_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aParentTransaction->addSubTransactionss($this);
             */
        }

        return $this->aParentTransaction;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('BackCounterTransactions' == $relationName) {
            $this->initBackCounterTransactionss();
        }
        if ('SubTransactions' == $relationName) {
            $this->initSubTransactionss();
        }
        if ('RefTransactionTag' == $relationName) {
            $this->initRefTransactionTags();
        }
    }

    /**
     * Clears out the collBackCounterTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Transaction The current object (for fluent API support)
     * @see        addBackCounterTransactionss()
     */
    public function clearBackCounterTransactionss()
    {
        $this->collBackCounterTransactionss = null; // important to set this to null since that means it is uninitialized
        $this->collBackCounterTransactionssPartial = null;

        return $this;
    }

    /**
     * reset is the collBackCounterTransactionss collection loaded partially
     *
     * @return void
     */
    public function resetPartialBackCounterTransactionss($v = true)
    {
        $this->collBackCounterTransactionssPartial = $v;
    }

    /**
     * Initializes the collBackCounterTransactionss collection.
     *
     * By default this just sets the collBackCounterTransactionss collection to an empty array (like clearcollBackCounterTransactionss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBackCounterTransactionss($overrideExisting = true)
    {
        if (null !== $this->collBackCounterTransactionss && !$overrideExisting) {
            return;
        }
        $this->collBackCounterTransactionss = new PropelObjectCollection();
        $this->collBackCounterTransactionss->setModel('Transaction');
    }

    /**
     * Gets an array of Transaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Transaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     * @throws PropelException
     */
    public function getBackCounterTransactionss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collBackCounterTransactionssPartial && !$this->isNew();
        if (null === $this->collBackCounterTransactionss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBackCounterTransactionss) {
                // return empty collection
                $this->initBackCounterTransactionss();
            } else {
                $collBackCounterTransactionss = TransactionQuery::create(null, $criteria)
                    ->filterByCounterTransaction($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collBackCounterTransactionssPartial && count($collBackCounterTransactionss)) {
                      $this->initBackCounterTransactionss(false);

                      foreach ($collBackCounterTransactionss as $obj) {
                        if (false == $this->collBackCounterTransactionss->contains($obj)) {
                          $this->collBackCounterTransactionss->append($obj);
                        }
                      }

                      $this->collBackCounterTransactionssPartial = true;
                    }

                    $collBackCounterTransactionss->getInternalIterator()->rewind();

                    return $collBackCounterTransactionss;
                }

                if ($partial && $this->collBackCounterTransactionss) {
                    foreach ($this->collBackCounterTransactionss as $obj) {
                        if ($obj->isNew()) {
                            $collBackCounterTransactionss[] = $obj;
                        }
                    }
                }

                $this->collBackCounterTransactionss = $collBackCounterTransactionss;
                $this->collBackCounterTransactionssPartial = false;
            }
        }

        return $this->collBackCounterTransactionss;
    }

    /**
     * Sets a collection of BackCounterTransactions objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $backCounterTransactionss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Transaction The current object (for fluent API support)
     */
    public function setBackCounterTransactionss(PropelCollection $backCounterTransactionss, PropelPDO $con = null)
    {
        $backCounterTransactionssToDelete = $this->getBackCounterTransactionss(new Criteria(), $con)->diff($backCounterTransactionss);


        $this->backCounterTransactionssScheduledForDeletion = $backCounterTransactionssToDelete;

        foreach ($backCounterTransactionssToDelete as $backCounterTransactionsRemoved) {
            $backCounterTransactionsRemoved->setCounterTransaction(null);
        }

        $this->collBackCounterTransactionss = null;
        foreach ($backCounterTransactionss as $backCounterTransactions) {
            $this->addBackCounterTransactions($backCounterTransactions);
        }

        $this->collBackCounterTransactionss = $backCounterTransactionss;
        $this->collBackCounterTransactionssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Transaction objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Transaction objects.
     * @throws PropelException
     */
    public function countBackCounterTransactionss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collBackCounterTransactionssPartial && !$this->isNew();
        if (null === $this->collBackCounterTransactionss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBackCounterTransactionss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBackCounterTransactionss());
            }
            $query = TransactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCounterTransaction($this)
                ->count($con);
        }

        return count($this->collBackCounterTransactionss);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return Transaction The current object (for fluent API support)
     */
    public function addBackCounterTransactions(Transaction $l)
    {
        if ($this->collBackCounterTransactionss === null) {
            $this->initBackCounterTransactionss();
            $this->collBackCounterTransactionssPartial = true;
        }

        if (!in_array($l, $this->collBackCounterTransactionss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBackCounterTransactions($l);

            if ($this->backCounterTransactionssScheduledForDeletion and $this->backCounterTransactionssScheduledForDeletion->contains($l)) {
                $this->backCounterTransactionssScheduledForDeletion->remove($this->backCounterTransactionssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  BackCounterTransactions $backCounterTransactions The backCounterTransactions object to add.
     */
    protected function doAddBackCounterTransactions($backCounterTransactions)
    {
        $this->collBackCounterTransactionss[]= $backCounterTransactions;
        $backCounterTransactions->setCounterTransaction($this);
    }

    /**
     * @param  BackCounterTransactions $backCounterTransactions The backCounterTransactions object to remove.
     * @return Transaction The current object (for fluent API support)
     */
    public function removeBackCounterTransactions($backCounterTransactions)
    {
        if ($this->getBackCounterTransactionss()->contains($backCounterTransactions)) {
            $this->collBackCounterTransactionss->remove($this->collBackCounterTransactionss->search($backCounterTransactions));
            if (null === $this->backCounterTransactionssScheduledForDeletion) {
                $this->backCounterTransactionssScheduledForDeletion = clone $this->collBackCounterTransactionss;
                $this->backCounterTransactionssScheduledForDeletion->clear();
            }
            $this->backCounterTransactionssScheduledForDeletion[]= $backCounterTransactions;
            $backCounterTransactions->setCounterTransaction(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related BackCounterTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getBackCounterTransactionssJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getBackCounterTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related BackCounterTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getBackCounterTransactionssJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getBackCounterTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related BackCounterTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getBackCounterTransactionssJoinAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Account', $join_behavior);

        return $this->getBackCounterTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related BackCounterTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getBackCounterTransactionssJoinTargetAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('TargetAccount', $join_behavior);

        return $this->getBackCounterTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related BackCounterTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getBackCounterTransactionssJoinCounterParty($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterParty', $join_behavior);

        return $this->getBackCounterTransactionss($query, $con);
    }

    /**
     * Clears out the collSubTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Transaction The current object (for fluent API support)
     * @see        addSubTransactionss()
     */
    public function clearSubTransactionss()
    {
        $this->collSubTransactionss = null; // important to set this to null since that means it is uninitialized
        $this->collSubTransactionssPartial = null;

        return $this;
    }

    /**
     * reset is the collSubTransactionss collection loaded partially
     *
     * @return void
     */
    public function resetPartialSubTransactionss($v = true)
    {
        $this->collSubTransactionssPartial = $v;
    }

    /**
     * Initializes the collSubTransactionss collection.
     *
     * By default this just sets the collSubTransactionss collection to an empty array (like clearcollSubTransactionss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSubTransactionss($overrideExisting = true)
    {
        if (null !== $this->collSubTransactionss && !$overrideExisting) {
            return;
        }
        $this->collSubTransactionss = new PropelObjectCollection();
        $this->collSubTransactionss->setModel('Transaction');
    }

    /**
     * Gets an array of Transaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Transaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     * @throws PropelException
     */
    public function getSubTransactionss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSubTransactionssPartial && !$this->isNew();
        if (null === $this->collSubTransactionss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSubTransactionss) {
                // return empty collection
                $this->initSubTransactionss();
            } else {
                $collSubTransactionss = TransactionQuery::create(null, $criteria)
                    ->filterByParentTransaction($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSubTransactionssPartial && count($collSubTransactionss)) {
                      $this->initSubTransactionss(false);

                      foreach ($collSubTransactionss as $obj) {
                        if (false == $this->collSubTransactionss->contains($obj)) {
                          $this->collSubTransactionss->append($obj);
                        }
                      }

                      $this->collSubTransactionssPartial = true;
                    }

                    $collSubTransactionss->getInternalIterator()->rewind();

                    return $collSubTransactionss;
                }

                if ($partial && $this->collSubTransactionss) {
                    foreach ($this->collSubTransactionss as $obj) {
                        if ($obj->isNew()) {
                            $collSubTransactionss[] = $obj;
                        }
                    }
                }

                $this->collSubTransactionss = $collSubTransactionss;
                $this->collSubTransactionssPartial = false;
            }
        }

        return $this->collSubTransactionss;
    }

    /**
     * Sets a collection of SubTransactions objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $subTransactionss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Transaction The current object (for fluent API support)
     */
    public function setSubTransactionss(PropelCollection $subTransactionss, PropelPDO $con = null)
    {
        $subTransactionssToDelete = $this->getSubTransactionss(new Criteria(), $con)->diff($subTransactionss);


        $this->subTransactionssScheduledForDeletion = $subTransactionssToDelete;

        foreach ($subTransactionssToDelete as $subTransactionsRemoved) {
            $subTransactionsRemoved->setParentTransaction(null);
        }

        $this->collSubTransactionss = null;
        foreach ($subTransactionss as $subTransactions) {
            $this->addSubTransactions($subTransactions);
        }

        $this->collSubTransactionss = $subTransactionss;
        $this->collSubTransactionssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Transaction objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Transaction objects.
     * @throws PropelException
     */
    public function countSubTransactionss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSubTransactionssPartial && !$this->isNew();
        if (null === $this->collSubTransactionss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSubTransactionss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSubTransactionss());
            }
            $query = TransactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByParentTransaction($this)
                ->count($con);
        }

        return count($this->collSubTransactionss);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return Transaction The current object (for fluent API support)
     */
    public function addSubTransactions(Transaction $l)
    {
        if ($this->collSubTransactionss === null) {
            $this->initSubTransactionss();
            $this->collSubTransactionssPartial = true;
        }

        if (!in_array($l, $this->collSubTransactionss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSubTransactions($l);

            if ($this->subTransactionssScheduledForDeletion and $this->subTransactionssScheduledForDeletion->contains($l)) {
                $this->subTransactionssScheduledForDeletion->remove($this->subTransactionssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  SubTransactions $subTransactions The subTransactions object to add.
     */
    protected function doAddSubTransactions($subTransactions)
    {
        $this->collSubTransactionss[]= $subTransactions;
        $subTransactions->setParentTransaction($this);
    }

    /**
     * @param  SubTransactions $subTransactions The subTransactions object to remove.
     * @return Transaction The current object (for fluent API support)
     */
    public function removeSubTransactions($subTransactions)
    {
        if ($this->getSubTransactionss()->contains($subTransactions)) {
            $this->collSubTransactionss->remove($this->collSubTransactionss->search($subTransactions));
            if (null === $this->subTransactionssScheduledForDeletion) {
                $this->subTransactionssScheduledForDeletion = clone $this->collSubTransactionss;
                $this->subTransactionssScheduledForDeletion->clear();
            }
            $this->subTransactionssScheduledForDeletion[]= $subTransactions;
            $subTransactions->setParentTransaction(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related SubTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getSubTransactionssJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getSubTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related SubTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getSubTransactionssJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getSubTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related SubTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getSubTransactionssJoinAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Account', $join_behavior);

        return $this->getSubTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related SubTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getSubTransactionssJoinTargetAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('TargetAccount', $join_behavior);

        return $this->getSubTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related SubTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getSubTransactionssJoinCounterParty($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterParty', $join_behavior);

        return $this->getSubTransactionss($query, $con);
    }

    /**
     * Clears out the collRefTransactionTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Transaction The current object (for fluent API support)
     * @see        addRefTransactionTags()
     */
    public function clearRefTransactionTags()
    {
        $this->collRefTransactionTags = null; // important to set this to null since that means it is uninitialized
        $this->collRefTransactionTagsPartial = null;

        return $this;
    }

    /**
     * reset is the collRefTransactionTags collection loaded partially
     *
     * @return void
     */
    public function resetPartialRefTransactionTags($v = true)
    {
        $this->collRefTransactionTagsPartial = $v;
    }

    /**
     * Initializes the collRefTransactionTags collection.
     *
     * By default this just sets the collRefTransactionTags collection to an empty array (like clearcollRefTransactionTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRefTransactionTags($overrideExisting = true)
    {
        if (null !== $this->collRefTransactionTags && !$overrideExisting) {
            return;
        }
        $this->collRefTransactionTags = new PropelObjectCollection();
        $this->collRefTransactionTags->setModel('RefTransactionTag');
    }

    /**
     * Gets an array of RefTransactionTag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Transaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|RefTransactionTag[] List of RefTransactionTag objects
     * @throws PropelException
     */
    public function getRefTransactionTags($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRefTransactionTagsPartial && !$this->isNew();
        if (null === $this->collRefTransactionTags || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRefTransactionTags) {
                // return empty collection
                $this->initRefTransactionTags();
            } else {
                $collRefTransactionTags = RefTransactionTagQuery::create(null, $criteria)
                    ->filterByTransaction($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRefTransactionTagsPartial && count($collRefTransactionTags)) {
                      $this->initRefTransactionTags(false);

                      foreach ($collRefTransactionTags as $obj) {
                        if (false == $this->collRefTransactionTags->contains($obj)) {
                          $this->collRefTransactionTags->append($obj);
                        }
                      }

                      $this->collRefTransactionTagsPartial = true;
                    }

                    $collRefTransactionTags->getInternalIterator()->rewind();

                    return $collRefTransactionTags;
                }

                if ($partial && $this->collRefTransactionTags) {
                    foreach ($this->collRefTransactionTags as $obj) {
                        if ($obj->isNew()) {
                            $collRefTransactionTags[] = $obj;
                        }
                    }
                }

                $this->collRefTransactionTags = $collRefTransactionTags;
                $this->collRefTransactionTagsPartial = false;
            }
        }

        return $this->collRefTransactionTags;
    }

    /**
     * Sets a collection of RefTransactionTag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $refTransactionTags A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Transaction The current object (for fluent API support)
     */
    public function setRefTransactionTags(PropelCollection $refTransactionTags, PropelPDO $con = null)
    {
        $refTransactionTagsToDelete = $this->getRefTransactionTags(new Criteria(), $con)->diff($refTransactionTags);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->refTransactionTagsScheduledForDeletion = clone $refTransactionTagsToDelete;

        foreach ($refTransactionTagsToDelete as $refTransactionTagRemoved) {
            $refTransactionTagRemoved->setTransaction(null);
        }

        $this->collRefTransactionTags = null;
        foreach ($refTransactionTags as $refTransactionTag) {
            $this->addRefTransactionTag($refTransactionTag);
        }

        $this->collRefTransactionTags = $refTransactionTags;
        $this->collRefTransactionTagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RefTransactionTag objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related RefTransactionTag objects.
     * @throws PropelException
     */
    public function countRefTransactionTags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRefTransactionTagsPartial && !$this->isNew();
        if (null === $this->collRefTransactionTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRefTransactionTags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRefTransactionTags());
            }
            $query = RefTransactionTagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTransaction($this)
                ->count($con);
        }

        return count($this->collRefTransactionTags);
    }

    /**
     * Method called to associate a RefTransactionTag object to this object
     * through the RefTransactionTag foreign key attribute.
     *
     * @param    RefTransactionTag $l RefTransactionTag
     * @return Transaction The current object (for fluent API support)
     */
    public function addRefTransactionTag(RefTransactionTag $l)
    {
        if ($this->collRefTransactionTags === null) {
            $this->initRefTransactionTags();
            $this->collRefTransactionTagsPartial = true;
        }

        if (!in_array($l, $this->collRefTransactionTags->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRefTransactionTag($l);

            if ($this->refTransactionTagsScheduledForDeletion and $this->refTransactionTagsScheduledForDeletion->contains($l)) {
                $this->refTransactionTagsScheduledForDeletion->remove($this->refTransactionTagsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  RefTransactionTag $refTransactionTag The refTransactionTag object to add.
     */
    protected function doAddRefTransactionTag($refTransactionTag)
    {
        $this->collRefTransactionTags[]= $refTransactionTag;
        $refTransactionTag->setTransaction($this);
    }

    /**
     * @param  RefTransactionTag $refTransactionTag The refTransactionTag object to remove.
     * @return Transaction The current object (for fluent API support)
     */
    public function removeRefTransactionTag($refTransactionTag)
    {
        if ($this->getRefTransactionTags()->contains($refTransactionTag)) {
            $this->collRefTransactionTags->remove($this->collRefTransactionTags->search($refTransactionTag));
            if (null === $this->refTransactionTagsScheduledForDeletion) {
                $this->refTransactionTagsScheduledForDeletion = clone $this->collRefTransactionTags;
                $this->refTransactionTagsScheduledForDeletion->clear();
            }
            $this->refTransactionTagsScheduledForDeletion[]= clone $refTransactionTag;
            $refTransactionTag->setTransaction(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Transaction is new, it will return
     * an empty collection; or if this Transaction has previously
     * been saved, it will retrieve related RefTransactionTags from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Transaction.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RefTransactionTag[] List of RefTransactionTag objects
     */
    public function getRefTransactionTagsJoinTransactionTag($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RefTransactionTagQuery::create(null, $criteria);
        $query->joinWith('TransactionTag', $join_behavior);

        return $this->getRefTransactionTags($query, $con);
    }

    /**
     * Clears out the collTransactionTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Transaction The current object (for fluent API support)
     * @see        addTransactionTags()
     */
    public function clearTransactionTags()
    {
        $this->collTransactionTags = null; // important to set this to null since that means it is uninitialized
        $this->collTransactionTagsPartial = null;

        return $this;
    }

    /**
     * Initializes the collTransactionTags collection.
     *
     * By default this just sets the collTransactionTags collection to an empty collection (like clearTransactionTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initTransactionTags()
    {
        $this->collTransactionTags = new PropelObjectCollection();
        $this->collTransactionTags->setModel('TransactionTag');
    }

    /**
     * Gets a collection of TransactionTag objects related by a many-to-many relationship
     * to the current object by way of the ref_transactions_tags cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Transaction is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|TransactionTag[] List of TransactionTag objects
     */
    public function getTransactionTags($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collTransactionTags || null !== $criteria) {
            if ($this->isNew() && null === $this->collTransactionTags) {
                // return empty collection
                $this->initTransactionTags();
            } else {
                $collTransactionTags = TransactionTagQuery::create(null, $criteria)
                    ->filterByTransaction($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collTransactionTags;
                }
                $this->collTransactionTags = $collTransactionTags;
            }
        }

        return $this->collTransactionTags;
    }

    /**
     * Sets a collection of TransactionTag objects related by a many-to-many relationship
     * to the current object by way of the ref_transactions_tags cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $transactionTags A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Transaction The current object (for fluent API support)
     */
    public function setTransactionTags(PropelCollection $transactionTags, PropelPDO $con = null)
    {
        $this->clearTransactionTags();
        $currentTransactionTags = $this->getTransactionTags(null, $con);

        $this->transactionTagsScheduledForDeletion = $currentTransactionTags->diff($transactionTags);

        foreach ($transactionTags as $transactionTag) {
            if (!$currentTransactionTags->contains($transactionTag)) {
                $this->doAddTransactionTag($transactionTag);
            }
        }

        $this->collTransactionTags = $transactionTags;

        return $this;
    }

    /**
     * Gets the number of TransactionTag objects related by a many-to-many relationship
     * to the current object by way of the ref_transactions_tags cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related TransactionTag objects
     */
    public function countTransactionTags($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collTransactionTags || null !== $criteria) {
            if ($this->isNew() && null === $this->collTransactionTags) {
                return 0;
            } else {
                $query = TransactionTagQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByTransaction($this)
                    ->count($con);
            }
        } else {
            return count($this->collTransactionTags);
        }
    }

    /**
     * Associate a TransactionTag object to this object
     * through the ref_transactions_tags cross reference table.
     *
     * @param  TransactionTag $transactionTag The RefTransactionTag object to relate
     * @return Transaction The current object (for fluent API support)
     */
    public function addTransactionTag(TransactionTag $transactionTag)
    {
        if ($this->collTransactionTags === null) {
            $this->initTransactionTags();
        }

        if (!$this->collTransactionTags->contains($transactionTag)) { // only add it if the **same** object is not already associated
            $this->doAddTransactionTag($transactionTag);
            $this->collTransactionTags[] = $transactionTag;

            if ($this->transactionTagsScheduledForDeletion and $this->transactionTagsScheduledForDeletion->contains($transactionTag)) {
                $this->transactionTagsScheduledForDeletion->remove($this->transactionTagsScheduledForDeletion->search($transactionTag));
            }
        }

        return $this;
    }

    /**
     * @param  TransactionTag $transactionTag The transactionTag object to add.
     */
    protected function doAddTransactionTag(TransactionTag $transactionTag)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$transactionTag->getTransactions()->contains($this)) { $refTransactionTag = new RefTransactionTag();
            $refTransactionTag->setTransactionTag($transactionTag);
            $this->addRefTransactionTag($refTransactionTag);

            $foreignCollection = $transactionTag->getTransactions();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a TransactionTag object to this object
     * through the ref_transactions_tags cross reference table.
     *
     * @param TransactionTag $transactionTag The RefTransactionTag object to relate
     * @return Transaction The current object (for fluent API support)
     */
    public function removeTransactionTag(TransactionTag $transactionTag)
    {
        if ($this->getTransactionTags()->contains($transactionTag)) {
            $this->collTransactionTags->remove($this->collTransactionTags->search($transactionTag));
            if (null === $this->transactionTagsScheduledForDeletion) {
                $this->transactionTagsScheduledForDeletion = clone $this->collTransactionTags;
                $this->transactionTagsScheduledForDeletion->clear();
            }
            $this->transactionTagsScheduledForDeletion[]= $transactionTag;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->title = null;
        $this->category_id = null;
        $this->account_id = null;
        $this->target_account_id = null;
        $this->counter_transaction_id = null;
        $this->counter_party_id = null;
        $this->parent_transaction_id = null;
        $this->amount = null;
        $this->isprojected = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collBackCounterTransactionss) {
                foreach ($this->collBackCounterTransactionss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSubTransactionss) {
                foreach ($this->collSubTransactionss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRefTransactionTags) {
                foreach ($this->collRefTransactionTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTransactionTags) {
                foreach ($this->collTransactionTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
            }
            if ($this->aCategory instanceof Persistent) {
              $this->aCategory->clearAllReferences($deep);
            }
            if ($this->aAccount instanceof Persistent) {
              $this->aAccount->clearAllReferences($deep);
            }
            if ($this->aTargetAccount instanceof Persistent) {
              $this->aTargetAccount->clearAllReferences($deep);
            }
            if ($this->aCounterTransaction instanceof Persistent) {
              $this->aCounterTransaction->clearAllReferences($deep);
            }
            if ($this->aCounterParty instanceof Persistent) {
              $this->aCounterParty->clearAllReferences($deep);
            }
            if ($this->aParentTransaction instanceof Persistent) {
              $this->aParentTransaction->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collBackCounterTransactionss instanceof PropelCollection) {
            $this->collBackCounterTransactionss->clearIterator();
        }
        $this->collBackCounterTransactionss = null;
        if ($this->collSubTransactionss instanceof PropelCollection) {
            $this->collSubTransactionss->clearIterator();
        }
        $this->collSubTransactionss = null;
        if ($this->collRefTransactionTags instanceof PropelCollection) {
            $this->collRefTransactionTags->clearIterator();
        }
        $this->collRefTransactionTags = null;
        if ($this->collTransactionTags instanceof PropelCollection) {
            $this->collTransactionTags->clearIterator();
        }
        $this->collTransactionTags = null;
        $this->aUser = null;
        $this->aCategory = null;
        $this->aAccount = null;
        $this->aTargetAccount = null;
        $this->aCounterTransaction = null;
        $this->aCounterParty = null;
        $this->aParentTransaction = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'title' column
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

  // timestampable behavior

  /**
   * Mark the current object so that the update date doesn't get updated during next save
   *
   * @return     Transaction The current object (for fluent API support)
   */
  public function keepUpdateDateUnchanged()
  {
      $this->modifiedColumns[] = TransactionPeer::UPDATED_AT;

      return $this;
  }

}

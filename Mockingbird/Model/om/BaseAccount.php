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
use Mockingbird\Model\AccountPeer;
use Mockingbird\Model\AccountQuery;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyQuery;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionQuery;

/**
 * Base class that represents a row from the 'accounts' table.
 *
 *
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseAccount extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Mockingbird\\Model\\AccountPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        AccountPeer
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
     * The value for the currency_id field.
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the initial_amount field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $initial_amount;

    /**
     * The value for the isclosed field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $isclosed;

    /**
     * The value for the isdebt field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $isdebt;

    /**
     * The value for the iscredit field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $iscredit;

    /**
     * The value for the color field.
     * Note: this column has a database default value of: '#000000'
     * @var        string
     */
    protected $color;

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
     * @var        Currency
     */
    protected $aCurrency;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collTransactionss;
    protected $collTransactionssPartial;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collTargetTransactionss;
    protected $collTargetTransactionssPartial;

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
    protected $transactionssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $targetTransactionssScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->initial_amount = '0';
        $this->isclosed = false;
        $this->isdebt = false;
        $this->iscredit = false;
        $this->color = '#000000';
    }

    /**
     * Initializes internal state of BaseAccount object.
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
     * Get the [currency_id] column value.
     *
     * @return int
     */
    public function getCurrencyId()
    {

        return $this->currency_id;
    }

    /**
     * Get the [initial_amount] column value.
     *
     * @return string
     */
    public function getInitialAmount()
    {

        return $this->initial_amount;
    }

    /**
     * Get the [isclosed] column value.
     *
     * @return boolean
     */
    public function getIsclosed()
    {

        return $this->isclosed;
    }

    /**
     * Get the [isdebt] column value.
     *
     * @return boolean
     */
    public function getIsdebt()
    {

        return $this->isdebt;
    }

    /**
     * Get the [iscredit] column value.
     *
     * @return boolean
     */
    public function getIscredit()
    {

        return $this->iscredit;
    }

    /**
     * Get the [color] column value.
     *
     * @return string
     */
    public function getColor()
    {

        return $this->color;
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
     * @return Account The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = AccountPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = AccountPeer::USER_ID;
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
     * @return Account The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = AccountPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [currency_id] column.
     *
     * @param  int $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[] = AccountPeer::CURRENCY_ID;
        }

        if ($this->aCurrency !== null && $this->aCurrency->getId() !== $v) {
            $this->aCurrency = null;
        }


        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [initial_amount] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setInitialAmount($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->initial_amount !== $v) {
            $this->initial_amount = $v;
            $this->modifiedColumns[] = AccountPeer::INITIAL_AMOUNT;
        }


        return $this;
    } // setInitialAmount()

    /**
     * Sets the value of the [isclosed] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Account The current object (for fluent API support)
     */
    public function setIsclosed($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->isclosed !== $v) {
            $this->isclosed = $v;
            $this->modifiedColumns[] = AccountPeer::ISCLOSED;
        }


        return $this;
    } // setIsclosed()

    /**
     * Sets the value of the [isdebt] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Account The current object (for fluent API support)
     */
    public function setIsdebt($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->isdebt !== $v) {
            $this->isdebt = $v;
            $this->modifiedColumns[] = AccountPeer::ISDEBT;
        }


        return $this;
    } // setIsdebt()

    /**
     * Sets the value of the [iscredit] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Account The current object (for fluent API support)
     */
    public function setIscredit($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->iscredit !== $v) {
            $this->iscredit = $v;
            $this->modifiedColumns[] = AccountPeer::ISCREDIT;
        }


        return $this;
    } // setIscredit()

    /**
     * Set the value of [color] column.
     *
     * @param  string $v new value
     * @return Account The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = AccountPeer::COLOR;
        }


        return $this;
    } // setColor()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Account The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = AccountPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return Account The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = AccountPeer::UPDATED_AT;
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
            if ($this->initial_amount !== '0') {
                return false;
            }

            if ($this->isclosed !== false) {
                return false;
            }

            if ($this->isdebt !== false) {
                return false;
            }

            if ($this->iscredit !== false) {
                return false;
            }

            if ($this->color !== '#000000') {
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
            $this->currency_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->initial_amount = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->isclosed = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->isdebt = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
            $this->iscredit = ($row[$startcol + 7] !== null) ? (boolean) $row[$startcol + 7] : null;
            $this->color = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->created_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->updated_at = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 11; // 11 = AccountPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Account object", $e);
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
        if ($this->aCurrency !== null && $this->currency_id !== $this->aCurrency->getId()) {
            $this->aCurrency = null;
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = AccountPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->aCurrency = null;
            $this->collTransactionss = null;

            $this->collTargetTransactionss = null;

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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = AccountQuery::create()
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
            $con = Propel::getConnection(AccountPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
        // timestampable behavior
        if (!$this->isColumnModified(AccountPeer::CREATED_AT))
        {
            $this->setCreatedAt(time());
        }
        if (!$this->isColumnModified(AccountPeer::UPDATED_AT))
        {
            $this->setUpdatedAt(time());
        }
            } else {
                $ret = $ret && $this->preUpdate($con);
        // timestampable behavior
        if ($this->isModified() && !$this->isColumnModified(AccountPeer::UPDATED_AT))
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
                AccountPeer::addInstanceToPool($this);
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

            if ($this->aCurrency !== null) {
                if ($this->aCurrency->isModified() || $this->aCurrency->isNew()) {
                    $affectedRows += $this->aCurrency->save($con);
                }
                $this->setCurrency($this->aCurrency);
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

            if ($this->transactionssScheduledForDeletion !== null) {
                if (!$this->transactionssScheduledForDeletion->isEmpty()) {
                    TransactionQuery::create()
                        ->filterByPrimaryKeys($this->transactionssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->transactionssScheduledForDeletion = null;
                }
            }

            if ($this->collTransactionss !== null) {
                foreach ($this->collTransactionss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->targetTransactionssScheduledForDeletion !== null) {
                if (!$this->targetTransactionssScheduledForDeletion->isEmpty()) {
                    foreach ($this->targetTransactionssScheduledForDeletion as $targetTransactions) {
                        // need to save related object because we set the relation to null
                        $targetTransactions->save($con);
                    }
                    $this->targetTransactionssScheduledForDeletion = null;
                }
            }

            if ($this->collTargetTransactionss !== null) {
                foreach ($this->collTargetTransactionss as $referrerFK) {
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

        $this->modifiedColumns[] = AccountPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AccountPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AccountPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(AccountPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(AccountPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(AccountPeer::CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = '`currency_id`';
        }
        if ($this->isColumnModified(AccountPeer::INITIAL_AMOUNT)) {
            $modifiedColumns[':p' . $index++]  = '`initial_amount`';
        }
        if ($this->isColumnModified(AccountPeer::ISCLOSED)) {
            $modifiedColumns[':p' . $index++]  = '`isclosed`';
        }
        if ($this->isColumnModified(AccountPeer::ISDEBT)) {
            $modifiedColumns[':p' . $index++]  = '`isdebt`';
        }
        if ($this->isColumnModified(AccountPeer::ISCREDIT)) {
            $modifiedColumns[':p' . $index++]  = '`iscredit`';
        }
        if ($this->isColumnModified(AccountPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`color`';
        }
        if ($this->isColumnModified(AccountPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(AccountPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `accounts` (%s) VALUES (%s)',
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
                    case '`currency_id`':
            $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case '`initial_amount`':
            $stmt->bindValue($identifier, $this->initial_amount, PDO::PARAM_STR);
                        break;
                    case '`isclosed`':
            $stmt->bindValue($identifier, (int) $this->isclosed, PDO::PARAM_INT);
                        break;
                    case '`isdebt`':
            $stmt->bindValue($identifier, (int) $this->isdebt, PDO::PARAM_INT);
                        break;
                    case '`iscredit`':
            $stmt->bindValue($identifier, (int) $this->iscredit, PDO::PARAM_INT);
                        break;
                    case '`color`':
            $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
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

            if ($this->aCurrency !== null) {
                if (!$this->aCurrency->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCurrency->getValidationFailures());
                }
            }


            if (($retval = AccountPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collTransactionss !== null) {
                    foreach ($this->collTransactionss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collTargetTransactionss !== null) {
                    foreach ($this->collTargetTransactionss as $referrerFK) {
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
        $pos = AccountPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCurrencyId();
                break;
            case 4:
                return $this->getInitialAmount();
                break;
            case 5:
                return $this->getIsclosed();
                break;
            case 6:
                return $this->getIsdebt();
                break;
            case 7:
                return $this->getIscredit();
                break;
            case 8:
                return $this->getColor();
                break;
            case 9:
                return $this->getCreatedAt();
                break;
            case 10:
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
        if (isset($alreadyDumpedObjects['Account'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Account'][$this->getPrimaryKey()] = true;
        $keys = AccountPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getCurrencyId(),
            $keys[4] => $this->getInitialAmount(),
            $keys[5] => $this->getIsclosed(),
            $keys[6] => $this->getIsdebt(),
            $keys[7] => $this->getIscredit(),
            $keys[8] => $this->getColor(),
            $keys[9] => $this->getCreatedAt(),
            $keys[10] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCurrency) {
                $result['Currency'] = $this->aCurrency->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collTransactionss) {
                $result['Transactionss'] = $this->collTransactionss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTargetTransactionss) {
                $result['TargetTransactionss'] = $this->collTargetTransactionss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = AccountPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setCurrencyId($value);
                break;
            case 4:
                $this->setInitialAmount($value);
                break;
            case 5:
                $this->setIsclosed($value);
                break;
            case 6:
                $this->setIsdebt($value);
                break;
            case 7:
                $this->setIscredit($value);
                break;
            case 8:
                $this->setColor($value);
                break;
            case 9:
                $this->setCreatedAt($value);
                break;
            case 10:
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
        $keys = AccountPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCurrencyId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setInitialAmount($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setIsclosed($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setIsdebt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setIscredit($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setColor($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AccountPeer::DATABASE_NAME);

        if ($this->isColumnModified(AccountPeer::ID)) $criteria->add(AccountPeer::ID, $this->id);
        if ($this->isColumnModified(AccountPeer::USER_ID)) $criteria->add(AccountPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(AccountPeer::TITLE)) $criteria->add(AccountPeer::TITLE, $this->title);
        if ($this->isColumnModified(AccountPeer::CURRENCY_ID)) $criteria->add(AccountPeer::CURRENCY_ID, $this->currency_id);
        if ($this->isColumnModified(AccountPeer::INITIAL_AMOUNT)) $criteria->add(AccountPeer::INITIAL_AMOUNT, $this->initial_amount);
        if ($this->isColumnModified(AccountPeer::ISCLOSED)) $criteria->add(AccountPeer::ISCLOSED, $this->isclosed);
        if ($this->isColumnModified(AccountPeer::ISDEBT)) $criteria->add(AccountPeer::ISDEBT, $this->isdebt);
        if ($this->isColumnModified(AccountPeer::ISCREDIT)) $criteria->add(AccountPeer::ISCREDIT, $this->iscredit);
        if ($this->isColumnModified(AccountPeer::COLOR)) $criteria->add(AccountPeer::COLOR, $this->color);
        if ($this->isColumnModified(AccountPeer::CREATED_AT)) $criteria->add(AccountPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(AccountPeer::UPDATED_AT)) $criteria->add(AccountPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(AccountPeer::DATABASE_NAME);
        $criteria->add(AccountPeer::ID, $this->id);

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
     * @param object $copyObj An object of Account (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setCurrencyId($this->getCurrencyId());
        $copyObj->setInitialAmount($this->getInitialAmount());
        $copyObj->setIsclosed($this->getIsclosed());
        $copyObj->setIsdebt($this->getIsdebt());
        $copyObj->setIscredit($this->getIscredit());
        $copyObj->setColor($this->getColor());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getTransactionss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTransactions($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTargetTransactionss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTargetTransactions($relObj->copy($deepCopy));
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
     * @return Account Clone of current object.
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
     * @return AccountPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new AccountPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param                  User $v
     * @return Account The current object (for fluent API support)
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
            $v->addAccount($this);
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
                $this->aUser->addAccounts($this);
             */
        }

        return $this->aUser;
    }

    /**
     * Declares an association between this object and a Currency object.
     *
     * @param                  Currency $v
     * @return Account The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrency(Currency $v = null)
    {
        if ($v === null) {
            $this->setCurrencyId(NULL);
        } else {
            $this->setCurrencyId($v->getId());
        }

        $this->aCurrency = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Currency object, it will not be re-added.
        if ($v !== null) {
            $v->addAccounts($this);
        }


        return $this;
    }


    /**
     * Get the associated Currency object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Currency The associated Currency object.
     * @throws PropelException
     */
    public function getCurrency(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCurrency === null && ($this->currency_id !== null) && $doQuery) {
            $this->aCurrency = CurrencyQuery::create()->findPk($this->currency_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCurrency->addAccountss($this);
             */
        }

        return $this->aCurrency;
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
        if ('Transactions' == $relationName) {
            $this->initTransactionss();
        }
        if ('TargetTransactions' == $relationName) {
            $this->initTargetTransactionss();
        }
    }

    /**
     * Clears out the collTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addTransactionss()
     */
    public function clearTransactionss()
    {
        $this->collTransactionss = null; // important to set this to null since that means it is uninitialized
        $this->collTransactionssPartial = null;

        return $this;
    }

    /**
     * reset is the collTransactionss collection loaded partially
     *
     * @return void
     */
    public function resetPartialTransactionss($v = true)
    {
        $this->collTransactionssPartial = $v;
    }

    /**
     * Initializes the collTransactionss collection.
     *
     * By default this just sets the collTransactionss collection to an empty array (like clearcollTransactionss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTransactionss($overrideExisting = true)
    {
        if (null !== $this->collTransactionss && !$overrideExisting) {
            return;
        }
        $this->collTransactionss = new PropelObjectCollection();
        $this->collTransactionss->setModel('Transaction');
    }

    /**
     * Gets an array of Transaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     * @throws PropelException
     */
    public function getTransactionss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTransactionssPartial && !$this->isNew();
        if (null === $this->collTransactionss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTransactionss) {
                // return empty collection
                $this->initTransactionss();
            } else {
                $collTransactionss = TransactionQuery::create(null, $criteria)
                    ->filterByAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTransactionssPartial && count($collTransactionss)) {
                      $this->initTransactionss(false);

                      foreach ($collTransactionss as $obj) {
                        if (false == $this->collTransactionss->contains($obj)) {
                          $this->collTransactionss->append($obj);
                        }
                      }

                      $this->collTransactionssPartial = true;
                    }

                    $collTransactionss->getInternalIterator()->rewind();

                    return $collTransactionss;
                }

                if ($partial && $this->collTransactionss) {
                    foreach ($this->collTransactionss as $obj) {
                        if ($obj->isNew()) {
                            $collTransactionss[] = $obj;
                        }
                    }
                }

                $this->collTransactionss = $collTransactionss;
                $this->collTransactionssPartial = false;
            }
        }

        return $this->collTransactionss;
    }

    /**
     * Sets a collection of Transactions objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $transactionss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setTransactionss(PropelCollection $transactionss, PropelPDO $con = null)
    {
        $transactionssToDelete = $this->getTransactionss(new Criteria(), $con)->diff($transactionss);


        $this->transactionssScheduledForDeletion = $transactionssToDelete;

        foreach ($transactionssToDelete as $transactionsRemoved) {
            $transactionsRemoved->setAccount(null);
        }

        $this->collTransactionss = null;
        foreach ($transactionss as $transactions) {
            $this->addTransactions($transactions);
        }

        $this->collTransactionss = $transactionss;
        $this->collTransactionssPartial = false;

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
    public function countTransactionss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTransactionssPartial && !$this->isNew();
        if (null === $this->collTransactionss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTransactionss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTransactionss());
            }
            $query = TransactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAccount($this)
                ->count($con);
        }

        return count($this->collTransactionss);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return Account The current object (for fluent API support)
     */
    public function addTransactions(Transaction $l)
    {
        if ($this->collTransactionss === null) {
            $this->initTransactionss();
            $this->collTransactionssPartial = true;
        }

        if (!in_array($l, $this->collTransactionss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTransactions($l);

            if ($this->transactionssScheduledForDeletion and $this->transactionssScheduledForDeletion->contains($l)) {
                $this->transactionssScheduledForDeletion->remove($this->transactionssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Transactions $transactions The transactions object to add.
     */
    protected function doAddTransactions($transactions)
    {
        $this->collTransactionss[]= $transactions;
        $transactions->setAccount($this);
    }

    /**
     * @param  Transactions $transactions The transactions object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeTransactions($transactions)
    {
        if ($this->getTransactionss()->contains($transactions)) {
            $this->collTransactionss->remove($this->collTransactionss->search($transactions));
            if (null === $this->transactionssScheduledForDeletion) {
                $this->transactionssScheduledForDeletion = clone $this->collTransactionss;
                $this->transactionssScheduledForDeletion->clear();
            }
            $this->transactionssScheduledForDeletion[]= clone $transactions;
            $transactions->setAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinCounterTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterTransaction', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinCounterParty($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterParty', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinParentTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('ParentTransaction', $join_behavior);

        return $this->getTransactionss($query, $con);
    }

    /**
     * Clears out the collTargetTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Account The current object (for fluent API support)
     * @see        addTargetTransactionss()
     */
    public function clearTargetTransactionss()
    {
        $this->collTargetTransactionss = null; // important to set this to null since that means it is uninitialized
        $this->collTargetTransactionssPartial = null;

        return $this;
    }

    /**
     * reset is the collTargetTransactionss collection loaded partially
     *
     * @return void
     */
    public function resetPartialTargetTransactionss($v = true)
    {
        $this->collTargetTransactionssPartial = $v;
    }

    /**
     * Initializes the collTargetTransactionss collection.
     *
     * By default this just sets the collTargetTransactionss collection to an empty array (like clearcollTargetTransactionss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTargetTransactionss($overrideExisting = true)
    {
        if (null !== $this->collTargetTransactionss && !$overrideExisting) {
            return;
        }
        $this->collTargetTransactionss = new PropelObjectCollection();
        $this->collTargetTransactionss->setModel('Transaction');
    }

    /**
     * Gets an array of Transaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Account is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     * @throws PropelException
     */
    public function getTargetTransactionss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTargetTransactionssPartial && !$this->isNew();
        if (null === $this->collTargetTransactionss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTargetTransactionss) {
                // return empty collection
                $this->initTargetTransactionss();
            } else {
                $collTargetTransactionss = TransactionQuery::create(null, $criteria)
                    ->filterByTargetAccount($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTargetTransactionssPartial && count($collTargetTransactionss)) {
                      $this->initTargetTransactionss(false);

                      foreach ($collTargetTransactionss as $obj) {
                        if (false == $this->collTargetTransactionss->contains($obj)) {
                          $this->collTargetTransactionss->append($obj);
                        }
                      }

                      $this->collTargetTransactionssPartial = true;
                    }

                    $collTargetTransactionss->getInternalIterator()->rewind();

                    return $collTargetTransactionss;
                }

                if ($partial && $this->collTargetTransactionss) {
                    foreach ($this->collTargetTransactionss as $obj) {
                        if ($obj->isNew()) {
                            $collTargetTransactionss[] = $obj;
                        }
                    }
                }

                $this->collTargetTransactionss = $collTargetTransactionss;
                $this->collTargetTransactionssPartial = false;
            }
        }

        return $this->collTargetTransactionss;
    }

    /**
     * Sets a collection of TargetTransactions objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $targetTransactionss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Account The current object (for fluent API support)
     */
    public function setTargetTransactionss(PropelCollection $targetTransactionss, PropelPDO $con = null)
    {
        $targetTransactionssToDelete = $this->getTargetTransactionss(new Criteria(), $con)->diff($targetTransactionss);


        $this->targetTransactionssScheduledForDeletion = $targetTransactionssToDelete;

        foreach ($targetTransactionssToDelete as $targetTransactionsRemoved) {
            $targetTransactionsRemoved->setTargetAccount(null);
        }

        $this->collTargetTransactionss = null;
        foreach ($targetTransactionss as $targetTransactions) {
            $this->addTargetTransactions($targetTransactions);
        }

        $this->collTargetTransactionss = $targetTransactionss;
        $this->collTargetTransactionssPartial = false;

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
    public function countTargetTransactionss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTargetTransactionssPartial && !$this->isNew();
        if (null === $this->collTargetTransactionss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTargetTransactionss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTargetTransactionss());
            }
            $query = TransactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTargetAccount($this)
                ->count($con);
        }

        return count($this->collTargetTransactionss);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return Account The current object (for fluent API support)
     */
    public function addTargetTransactions(Transaction $l)
    {
        if ($this->collTargetTransactionss === null) {
            $this->initTargetTransactionss();
            $this->collTargetTransactionssPartial = true;
        }

        if (!in_array($l, $this->collTargetTransactionss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTargetTransactions($l);

            if ($this->targetTransactionssScheduledForDeletion and $this->targetTransactionssScheduledForDeletion->contains($l)) {
                $this->targetTransactionssScheduledForDeletion->remove($this->targetTransactionssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  TargetTransactions $targetTransactions The targetTransactions object to add.
     */
    protected function doAddTargetTransactions($targetTransactions)
    {
        $this->collTargetTransactionss[]= $targetTransactions;
        $targetTransactions->setTargetAccount($this);
    }

    /**
     * @param  TargetTransactions $targetTransactions The targetTransactions object to remove.
     * @return Account The current object (for fluent API support)
     */
    public function removeTargetTransactions($targetTransactions)
    {
        if ($this->getTargetTransactionss()->contains($targetTransactions)) {
            $this->collTargetTransactionss->remove($this->collTargetTransactionss->search($targetTransactions));
            if (null === $this->targetTransactionssScheduledForDeletion) {
                $this->targetTransactionssScheduledForDeletion = clone $this->collTargetTransactionss;
                $this->targetTransactionssScheduledForDeletion->clear();
            }
            $this->targetTransactionssScheduledForDeletion[]= $targetTransactions;
            $targetTransactions->setTargetAccount(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related TargetTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTargetTransactionssJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getTargetTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related TargetTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTargetTransactionssJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getTargetTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related TargetTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTargetTransactionssJoinCounterTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterTransaction', $join_behavior);

        return $this->getTargetTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related TargetTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTargetTransactionssJoinCounterParty($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterParty', $join_behavior);

        return $this->getTargetTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Account is new, it will return
     * an empty collection; or if this Account has previously
     * been saved, it will retrieve related TargetTransactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Account.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTargetTransactionssJoinParentTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('ParentTransaction', $join_behavior);

        return $this->getTargetTransactionss($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->title = null;
        $this->currency_id = null;
        $this->initial_amount = null;
        $this->isclosed = null;
        $this->isdebt = null;
        $this->iscredit = null;
        $this->color = null;
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
            if ($this->collTransactionss) {
                foreach ($this->collTransactionss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTargetTransactionss) {
                foreach ($this->collTargetTransactionss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
            }
            if ($this->aCurrency instanceof Persistent) {
              $this->aCurrency->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collTransactionss instanceof PropelCollection) {
            $this->collTransactionss->clearIterator();
        }
        $this->collTransactionss = null;
        if ($this->collTargetTransactionss instanceof PropelCollection) {
            $this->collTargetTransactionss->clearIterator();
        }
        $this->collTargetTransactionss = null;
        $this->aUser = null;
        $this->aCurrency = null;
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
   * @return     Account The current object (for fluent API support)
   */
  public function keepUpdateDateUnchanged()
  {
      $this->modifiedColumns[] = AccountPeer::UPDATED_AT;

      return $this;
  }

}

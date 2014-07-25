<?php

namespace Mockingbird\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserQuery;
use Mockingbird\Model\BudgetEntry;
use Mockingbird\Model\BudgetEntryQuery;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategory;
use Mockingbird\Model\TransactionCategoryPeer;
use Mockingbird\Model\TransactionCategoryQuery;
use Mockingbird\Model\TransactionQuery;

/**
 * Base class that represents a row from the 'transaction_categories' table.
 *
 *
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseTransactionCategory extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Mockingbird\\Model\\TransactionCategoryPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TransactionCategoryPeer
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
     * The value for the color field.
     * Note: this column has a database default value of: '#000000'
     * @var        string
     */
    protected $color;

    /**
     * @var        User
     */
    protected $aUser;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collTransactionss;
    protected $collTransactionssPartial;

    /**
     * @var        PropelObjectCollection|BudgetEntry[] Collection to store aggregation of BudgetEntry objects.
     */
    protected $collBudgetEntrys;
    protected $collBudgetEntrysPartial;

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
    protected $budgetEntrysScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->color = '#000000';
    }

    /**
     * Initializes internal state of BaseTransactionCategory object.
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
     * Get the [color] column value.
     *
     * @return string
     */
    public function getColor()
    {

        return $this->color;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = TransactionCategoryPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [user_id] column.
     *
     * @param  int $v new value
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setUserId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->user_id !== $v) {
            $this->user_id = $v;
            $this->modifiedColumns[] = TransactionCategoryPeer::USER_ID;
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
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = TransactionCategoryPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [color] column.
     *
     * @param  string $v new value
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setColor($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->color !== $v) {
            $this->color = $v;
            $this->modifiedColumns[] = TransactionCategoryPeer::COLOR;
        }


        return $this;
    } // setColor()

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
            $this->color = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 4; // 4 = TransactionCategoryPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating TransactionCategory object", $e);
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
            $con = Propel::getConnection(TransactionCategoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TransactionCategoryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aUser = null;
            $this->collTransactionss = null;

            $this->collBudgetEntrys = null;

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
            $con = Propel::getConnection(TransactionCategoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TransactionCategoryQuery::create()
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
            $con = Propel::getConnection(TransactionCategoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                TransactionCategoryPeer::addInstanceToPool($this);
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
                    foreach ($this->transactionssScheduledForDeletion as $transactions) {
                        // need to save related object because we set the relation to null
                        $transactions->save($con);
                    }
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

            if ($this->budgetEntrysScheduledForDeletion !== null) {
                if (!$this->budgetEntrysScheduledForDeletion->isEmpty()) {
                    BudgetEntryQuery::create()
                        ->filterByPrimaryKeys($this->budgetEntrysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->budgetEntrysScheduledForDeletion = null;
                }
            }

            if ($this->collBudgetEntrys !== null) {
                foreach ($this->collBudgetEntrys as $referrerFK) {
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

        $this->modifiedColumns[] = TransactionCategoryPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TransactionCategoryPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TransactionCategoryPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(TransactionCategoryPeer::USER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`user_id`';
        }
        if ($this->isColumnModified(TransactionCategoryPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(TransactionCategoryPeer::COLOR)) {
            $modifiedColumns[':p' . $index++]  = '`color`';
        }

        $sql = sprintf(
            'INSERT INTO `transaction_categories` (%s) VALUES (%s)',
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
                    case '`color`':
            $stmt->bindValue($identifier, $this->color, PDO::PARAM_STR);
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


            if (($retval = TransactionCategoryPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collTransactionss !== null) {
                    foreach ($this->collTransactionss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collBudgetEntrys !== null) {
                    foreach ($this->collBudgetEntrys as $referrerFK) {
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
        $pos = TransactionCategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getColor();
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
        if (isset($alreadyDumpedObjects['TransactionCategory'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['TransactionCategory'][$this->getPrimaryKey()] = true;
        $keys = TransactionCategoryPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUserId(),
            $keys[2] => $this->getTitle(),
            $keys[3] => $this->getColor(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aUser) {
                $result['User'] = $this->aUser->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collTransactionss) {
                $result['Transactionss'] = $this->collTransactionss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collBudgetEntrys) {
                $result['BudgetEntrys'] = $this->collBudgetEntrys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = TransactionCategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setColor($value);
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
        $keys = TransactionCategoryPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUserId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitle($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setColor($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TransactionCategoryPeer::DATABASE_NAME);

        if ($this->isColumnModified(TransactionCategoryPeer::ID)) $criteria->add(TransactionCategoryPeer::ID, $this->id);
        if ($this->isColumnModified(TransactionCategoryPeer::USER_ID)) $criteria->add(TransactionCategoryPeer::USER_ID, $this->user_id);
        if ($this->isColumnModified(TransactionCategoryPeer::TITLE)) $criteria->add(TransactionCategoryPeer::TITLE, $this->title);
        if ($this->isColumnModified(TransactionCategoryPeer::COLOR)) $criteria->add(TransactionCategoryPeer::COLOR, $this->color);

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
        $criteria = new Criteria(TransactionCategoryPeer::DATABASE_NAME);
        $criteria->add(TransactionCategoryPeer::ID, $this->id);

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
     * @param object $copyObj An object of TransactionCategory (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUserId($this->getUserId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setColor($this->getColor());

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

            foreach ($this->getBudgetEntrys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBudgetEntry($relObj->copy($deepCopy));
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
     * @return TransactionCategory Clone of current object.
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
     * @return TransactionCategoryPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TransactionCategoryPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a User object.
     *
     * @param                  User $v
     * @return TransactionCategory The current object (for fluent API support)
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
            $v->addCategory($this);
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
                $this->aUser->addCategorys($this);
             */
        }

        return $this->aUser;
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
        if ('BudgetEntry' == $relationName) {
            $this->initBudgetEntrys();
        }
    }

    /**
     * Clears out the collTransactionss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return TransactionCategory The current object (for fluent API support)
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
     * If this TransactionCategory is new, it will return
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
                    ->filterByCategory($this)
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
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setTransactionss(PropelCollection $transactionss, PropelPDO $con = null)
    {
        $transactionssToDelete = $this->getTransactionss(new Criteria(), $con)->diff($transactionss);


        $this->transactionssScheduledForDeletion = $transactionssToDelete;

        foreach ($transactionssToDelete as $transactionsRemoved) {
            $transactionsRemoved->setCategory(null);
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
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collTransactionss);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return TransactionCategory The current object (for fluent API support)
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
        $transactions->setCategory($this);
    }

    /**
     * @param  Transactions $transactions The transactions object to remove.
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function removeTransactions($transactions)
    {
        if ($this->getTransactionss()->contains($transactions)) {
            $this->collTransactionss->remove($this->collTransactionss->search($transactions));
            if (null === $this->transactionssScheduledForDeletion) {
                $this->transactionssScheduledForDeletion = clone $this->collTransactionss;
                $this->transactionssScheduledForDeletion->clear();
            }
            $this->transactionssScheduledForDeletion[]= $transactions;
            $transactions->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
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
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Account', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionssJoinTargetAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('TargetAccount', $join_behavior);

        return $this->getTransactionss($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
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
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
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
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related Transactionss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
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
     * Clears out the collBudgetEntrys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return TransactionCategory The current object (for fluent API support)
     * @see        addBudgetEntrys()
     */
    public function clearBudgetEntrys()
    {
        $this->collBudgetEntrys = null; // important to set this to null since that means it is uninitialized
        $this->collBudgetEntrysPartial = null;

        return $this;
    }

    /**
     * reset is the collBudgetEntrys collection loaded partially
     *
     * @return void
     */
    public function resetPartialBudgetEntrys($v = true)
    {
        $this->collBudgetEntrysPartial = $v;
    }

    /**
     * Initializes the collBudgetEntrys collection.
     *
     * By default this just sets the collBudgetEntrys collection to an empty array (like clearcollBudgetEntrys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBudgetEntrys($overrideExisting = true)
    {
        if (null !== $this->collBudgetEntrys && !$overrideExisting) {
            return;
        }
        $this->collBudgetEntrys = new PropelObjectCollection();
        $this->collBudgetEntrys->setModel('BudgetEntry');
    }

    /**
     * Gets an array of BudgetEntry objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this TransactionCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|BudgetEntry[] List of BudgetEntry objects
     * @throws PropelException
     */
    public function getBudgetEntrys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collBudgetEntrysPartial && !$this->isNew();
        if (null === $this->collBudgetEntrys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBudgetEntrys) {
                // return empty collection
                $this->initBudgetEntrys();
            } else {
                $collBudgetEntrys = BudgetEntryQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collBudgetEntrysPartial && count($collBudgetEntrys)) {
                      $this->initBudgetEntrys(false);

                      foreach ($collBudgetEntrys as $obj) {
                        if (false == $this->collBudgetEntrys->contains($obj)) {
                          $this->collBudgetEntrys->append($obj);
                        }
                      }

                      $this->collBudgetEntrysPartial = true;
                    }

                    $collBudgetEntrys->getInternalIterator()->rewind();

                    return $collBudgetEntrys;
                }

                if ($partial && $this->collBudgetEntrys) {
                    foreach ($this->collBudgetEntrys as $obj) {
                        if ($obj->isNew()) {
                            $collBudgetEntrys[] = $obj;
                        }
                    }
                }

                $this->collBudgetEntrys = $collBudgetEntrys;
                $this->collBudgetEntrysPartial = false;
            }
        }

        return $this->collBudgetEntrys;
    }

    /**
     * Sets a collection of BudgetEntry objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $budgetEntrys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function setBudgetEntrys(PropelCollection $budgetEntrys, PropelPDO $con = null)
    {
        $budgetEntrysToDelete = $this->getBudgetEntrys(new Criteria(), $con)->diff($budgetEntrys);


        $this->budgetEntrysScheduledForDeletion = $budgetEntrysToDelete;

        foreach ($budgetEntrysToDelete as $budgetEntryRemoved) {
            $budgetEntryRemoved->setCategory(null);
        }

        $this->collBudgetEntrys = null;
        foreach ($budgetEntrys as $budgetEntry) {
            $this->addBudgetEntry($budgetEntry);
        }

        $this->collBudgetEntrys = $budgetEntrys;
        $this->collBudgetEntrysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BudgetEntry objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related BudgetEntry objects.
     * @throws PropelException
     */
    public function countBudgetEntrys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collBudgetEntrysPartial && !$this->isNew();
        if (null === $this->collBudgetEntrys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBudgetEntrys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBudgetEntrys());
            }
            $query = BudgetEntryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collBudgetEntrys);
    }

    /**
     * Method called to associate a BudgetEntry object to this object
     * through the BudgetEntry foreign key attribute.
     *
     * @param    BudgetEntry $l BudgetEntry
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function addBudgetEntry(BudgetEntry $l)
    {
        if ($this->collBudgetEntrys === null) {
            $this->initBudgetEntrys();
            $this->collBudgetEntrysPartial = true;
        }

        if (!in_array($l, $this->collBudgetEntrys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBudgetEntry($l);

            if ($this->budgetEntrysScheduledForDeletion and $this->budgetEntrysScheduledForDeletion->contains($l)) {
                $this->budgetEntrysScheduledForDeletion->remove($this->budgetEntrysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  BudgetEntry $budgetEntry The budgetEntry object to add.
     */
    protected function doAddBudgetEntry($budgetEntry)
    {
        $this->collBudgetEntrys[]= $budgetEntry;
        $budgetEntry->setCategory($this);
    }

    /**
     * @param  BudgetEntry $budgetEntry The budgetEntry object to remove.
     * @return TransactionCategory The current object (for fluent API support)
     */
    public function removeBudgetEntry($budgetEntry)
    {
        if ($this->getBudgetEntrys()->contains($budgetEntry)) {
            $this->collBudgetEntrys->remove($this->collBudgetEntrys->search($budgetEntry));
            if (null === $this->budgetEntrysScheduledForDeletion) {
                $this->budgetEntrysScheduledForDeletion = clone $this->collBudgetEntrys;
                $this->budgetEntrysScheduledForDeletion->clear();
            }
            $this->budgetEntrysScheduledForDeletion[]= clone $budgetEntry;
            $budgetEntry->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this TransactionCategory is new, it will return
     * an empty collection; or if this TransactionCategory has previously
     * been saved, it will retrieve related BudgetEntrys from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in TransactionCategory.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|BudgetEntry[] List of BudgetEntry objects
     */
    public function getBudgetEntrysJoinBudget($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = BudgetEntryQuery::create(null, $criteria);
        $query->joinWith('Budget', $join_behavior);

        return $this->getBudgetEntrys($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->user_id = null;
        $this->title = null;
        $this->color = null;
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
            if ($this->collBudgetEntrys) {
                foreach ($this->collBudgetEntrys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aUser instanceof Persistent) {
              $this->aUser->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collTransactionss instanceof PropelCollection) {
            $this->collTransactionss->clearIterator();
        }
        $this->collTransactionss = null;
        if ($this->collBudgetEntrys instanceof PropelCollection) {
            $this->collBudgetEntrys->clearIterator();
        }
        $this->collBudgetEntrys = null;
        $this->aUser = null;
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

}

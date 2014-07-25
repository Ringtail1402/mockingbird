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
use Mockingbird\Model\Account;
use Mockingbird\Model\AccountQuery;
use Mockingbird\Model\Budget;
use Mockingbird\Model\BudgetQuery;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyPeer;
use Mockingbird\Model\CurrencyQuery;

/**
 * Base class that represents a row from the 'currencies' table.
 *
 *
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseCurrency extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Mockingbird\\Model\\CurrencyPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CurrencyPeer
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
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the format field.
     * @var        string
     */
    protected $format;

    /**
     * The value for the is_primary field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_primary;

    /**
     * The value for the rate_to_primary field.
     * Note: this column has a database default value of: 1
     * @var        double
     */
    protected $rate_to_primary;

    /**
     * @var        PropelObjectCollection|Account[] Collection to store aggregation of Account objects.
     */
    protected $collAccountss;
    protected $collAccountssPartial;

    /**
     * @var        PropelObjectCollection|Budget[] Collection to store aggregation of Budget objects.
     */
    protected $collBudgets;
    protected $collBudgetsPartial;

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
    protected $accountssScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $budgetsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see        __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_primary = false;
        $this->rate_to_primary = 1;
    }

    /**
     * Initializes internal state of BaseCurrency object.
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
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [format] column value.
     *
     * @return string
     */
    public function getFormat()
    {

        return $this->format;
    }

    /**
     * Get the [is_primary] column value.
     *
     * @return boolean
     */
    public function getIsPrimary()
    {

        return $this->is_primary;
    }

    /**
     * Get the [rate_to_primary] column value.
     *
     * @return double
     */
    public function getRateToPrimary()
    {

        return $this->rate_to_primary;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Currency The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CurrencyPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param  string $v new value
     * @return Currency The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = CurrencyPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [format] column.
     *
     * @param  string $v new value
     * @return Currency The current object (for fluent API support)
     */
    public function setFormat($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->format !== $v) {
            $this->format = $v;
            $this->modifiedColumns[] = CurrencyPeer::FORMAT;
        }


        return $this;
    } // setFormat()

    /**
     * Sets the value of the [is_primary] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return Currency The current object (for fluent API support)
     */
    public function setIsPrimary($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_primary !== $v) {
            $this->is_primary = $v;
            $this->modifiedColumns[] = CurrencyPeer::IS_PRIMARY;
        }


        return $this;
    } // setIsPrimary()

    /**
     * Set the value of [rate_to_primary] column.
     *
     * @param  double $v new value
     * @return Currency The current object (for fluent API support)
     */
    public function setRateToPrimary($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (double) $v;
        }

        if ($this->rate_to_primary !== $v) {
            $this->rate_to_primary = $v;
            $this->modifiedColumns[] = CurrencyPeer::RATE_TO_PRIMARY;
        }


        return $this;
    } // setRateToPrimary()

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
            if ($this->is_primary !== false) {
                return false;
            }

            if ($this->rate_to_primary !== 1) {
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
            $this->title = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->format = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->is_primary = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->rate_to_primary = ($row[$startcol + 4] !== null) ? (double) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = CurrencyPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Currency object", $e);
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
            $con = Propel::getConnection(CurrencyPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CurrencyPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collAccountss = null;

            $this->collBudgets = null;

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
            $con = Propel::getConnection(CurrencyPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CurrencyQuery::create()
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
            $con = Propel::getConnection(CurrencyPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                CurrencyPeer::addInstanceToPool($this);
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

            if ($this->accountssScheduledForDeletion !== null) {
                if (!$this->accountssScheduledForDeletion->isEmpty()) {
                    AccountQuery::create()
                        ->filterByPrimaryKeys($this->accountssScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountssScheduledForDeletion = null;
                }
            }

            if ($this->collAccountss !== null) {
                foreach ($this->collAccountss as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->budgetsScheduledForDeletion !== null) {
                if (!$this->budgetsScheduledForDeletion->isEmpty()) {
                    foreach ($this->budgetsScheduledForDeletion as $budget) {
                        // need to save related object because we set the relation to null
                        $budget->save($con);
                    }
                    $this->budgetsScheduledForDeletion = null;
                }
            }

            if ($this->collBudgets !== null) {
                foreach ($this->collBudgets as $referrerFK) {
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

        $this->modifiedColumns[] = CurrencyPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CurrencyPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CurrencyPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CurrencyPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = '`title`';
        }
        if ($this->isColumnModified(CurrencyPeer::FORMAT)) {
            $modifiedColumns[':p' . $index++]  = '`format`';
        }
        if ($this->isColumnModified(CurrencyPeer::IS_PRIMARY)) {
            $modifiedColumns[':p' . $index++]  = '`is_primary`';
        }
        if ($this->isColumnModified(CurrencyPeer::RATE_TO_PRIMARY)) {
            $modifiedColumns[':p' . $index++]  = '`rate_to_primary`';
        }

        $sql = sprintf(
            'INSERT INTO `currencies` (%s) VALUES (%s)',
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
                    case '`title`':
            $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case '`format`':
            $stmt->bindValue($identifier, $this->format, PDO::PARAM_STR);
                        break;
                    case '`is_primary`':
            $stmt->bindValue($identifier, (int) $this->is_primary, PDO::PARAM_INT);
                        break;
                    case '`rate_to_primary`':
            $stmt->bindValue($identifier, $this->rate_to_primary, PDO::PARAM_STR);
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


            if (($retval = CurrencyPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collAccountss !== null) {
                    foreach ($this->collAccountss as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collBudgets !== null) {
                    foreach ($this->collBudgets as $referrerFK) {
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
        $pos = CurrencyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getTitle();
                break;
            case 2:
                return $this->getFormat();
                break;
            case 3:
                return $this->getIsPrimary();
                break;
            case 4:
                return $this->getRateToPrimary();
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
        if (isset($alreadyDumpedObjects['Currency'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Currency'][$this->getPrimaryKey()] = true;
        $keys = CurrencyPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getFormat(),
            $keys[3] => $this->getIsPrimary(),
            $keys[4] => $this->getRateToPrimary(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collAccountss) {
                $result['Accountss'] = $this->collAccountss->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collBudgets) {
                $result['Budgets'] = $this->collBudgets->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CurrencyPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setTitle($value);
                break;
            case 2:
                $this->setFormat($value);
                break;
            case 3:
                $this->setIsPrimary($value);
                break;
            case 4:
                $this->setRateToPrimary($value);
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
        $keys = CurrencyPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setTitle($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setFormat($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setIsPrimary($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setRateToPrimary($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CurrencyPeer::DATABASE_NAME);

        if ($this->isColumnModified(CurrencyPeer::ID)) $criteria->add(CurrencyPeer::ID, $this->id);
        if ($this->isColumnModified(CurrencyPeer::TITLE)) $criteria->add(CurrencyPeer::TITLE, $this->title);
        if ($this->isColumnModified(CurrencyPeer::FORMAT)) $criteria->add(CurrencyPeer::FORMAT, $this->format);
        if ($this->isColumnModified(CurrencyPeer::IS_PRIMARY)) $criteria->add(CurrencyPeer::IS_PRIMARY, $this->is_primary);
        if ($this->isColumnModified(CurrencyPeer::RATE_TO_PRIMARY)) $criteria->add(CurrencyPeer::RATE_TO_PRIMARY, $this->rate_to_primary);

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
        $criteria = new Criteria(CurrencyPeer::DATABASE_NAME);
        $criteria->add(CurrencyPeer::ID, $this->id);

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
     * @param object $copyObj An object of Currency (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setFormat($this->getFormat());
        $copyObj->setIsPrimary($this->getIsPrimary());
        $copyObj->setRateToPrimary($this->getRateToPrimary());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getAccountss() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccounts($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getBudgets() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBudget($relObj->copy($deepCopy));
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
     * @return Currency Clone of current object.
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
     * @return CurrencyPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CurrencyPeer();
        }

        return self::$peer;
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
        if ('Accounts' == $relationName) {
            $this->initAccountss();
        }
        if ('Budget' == $relationName) {
            $this->initBudgets();
        }
    }

    /**
     * Clears out the collAccountss collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Currency The current object (for fluent API support)
     * @see        addAccountss()
     */
    public function clearAccountss()
    {
        $this->collAccountss = null; // important to set this to null since that means it is uninitialized
        $this->collAccountssPartial = null;

        return $this;
    }

    /**
     * reset is the collAccountss collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccountss($v = true)
    {
        $this->collAccountssPartial = $v;
    }

    /**
     * Initializes the collAccountss collection.
     *
     * By default this just sets the collAccountss collection to an empty array (like clearcollAccountss());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccountss($overrideExisting = true)
    {
        if (null !== $this->collAccountss && !$overrideExisting) {
            return;
        }
        $this->collAccountss = new PropelObjectCollection();
        $this->collAccountss->setModel('Account');
    }

    /**
     * Gets an array of Account objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Currency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Account[] List of Account objects
     * @throws PropelException
     */
    public function getAccountss($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountssPartial && !$this->isNew();
        if (null === $this->collAccountss || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccountss) {
                // return empty collection
                $this->initAccountss();
            } else {
                $collAccountss = AccountQuery::create(null, $criteria)
                    ->filterByCurrency($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountssPartial && count($collAccountss)) {
                      $this->initAccountss(false);

                      foreach ($collAccountss as $obj) {
                        if (false == $this->collAccountss->contains($obj)) {
                          $this->collAccountss->append($obj);
                        }
                      }

                      $this->collAccountssPartial = true;
                    }

                    $collAccountss->getInternalIterator()->rewind();

                    return $collAccountss;
                }

                if ($partial && $this->collAccountss) {
                    foreach ($this->collAccountss as $obj) {
                        if ($obj->isNew()) {
                            $collAccountss[] = $obj;
                        }
                    }
                }

                $this->collAccountss = $collAccountss;
                $this->collAccountssPartial = false;
            }
        }

        return $this->collAccountss;
    }

    /**
     * Sets a collection of Accounts objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accountss A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Currency The current object (for fluent API support)
     */
    public function setAccountss(PropelCollection $accountss, PropelPDO $con = null)
    {
        $accountssToDelete = $this->getAccountss(new Criteria(), $con)->diff($accountss);


        $this->accountssScheduledForDeletion = $accountssToDelete;

        foreach ($accountssToDelete as $accountsRemoved) {
            $accountsRemoved->setCurrency(null);
        }

        $this->collAccountss = null;
        foreach ($accountss as $accounts) {
            $this->addAccounts($accounts);
        }

        $this->collAccountss = $accountss;
        $this->collAccountssPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Account objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Account objects.
     * @throws PropelException
     */
    public function countAccountss(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountssPartial && !$this->isNew();
        if (null === $this->collAccountss || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccountss) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccountss());
            }
            $query = AccountQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrency($this)
                ->count($con);
        }

        return count($this->collAccountss);
    }

    /**
     * Method called to associate a Account object to this object
     * through the Account foreign key attribute.
     *
     * @param    Account $l Account
     * @return Currency The current object (for fluent API support)
     */
    public function addAccounts(Account $l)
    {
        if ($this->collAccountss === null) {
            $this->initAccountss();
            $this->collAccountssPartial = true;
        }

        if (!in_array($l, $this->collAccountss->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccounts($l);

            if ($this->accountssScheduledForDeletion and $this->accountssScheduledForDeletion->contains($l)) {
                $this->accountssScheduledForDeletion->remove($this->accountssScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Accounts $accounts The accounts object to add.
     */
    protected function doAddAccounts($accounts)
    {
        $this->collAccountss[]= $accounts;
        $accounts->setCurrency($this);
    }

    /**
     * @param  Accounts $accounts The accounts object to remove.
     * @return Currency The current object (for fluent API support)
     */
    public function removeAccounts($accounts)
    {
        if ($this->getAccountss()->contains($accounts)) {
            $this->collAccountss->remove($this->collAccountss->search($accounts));
            if (null === $this->accountssScheduledForDeletion) {
                $this->accountssScheduledForDeletion = clone $this->collAccountss;
                $this->accountssScheduledForDeletion->clear();
            }
            $this->accountssScheduledForDeletion[]= clone $accounts;
            $accounts->setCurrency(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Accountss from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Account[] List of Account objects
     */
    public function getAccountssJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getAccountss($query, $con);
    }

    /**
     * Clears out the collBudgets collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Currency The current object (for fluent API support)
     * @see        addBudgets()
     */
    public function clearBudgets()
    {
        $this->collBudgets = null; // important to set this to null since that means it is uninitialized
        $this->collBudgetsPartial = null;

        return $this;
    }

    /**
     * reset is the collBudgets collection loaded partially
     *
     * @return void
     */
    public function resetPartialBudgets($v = true)
    {
        $this->collBudgetsPartial = $v;
    }

    /**
     * Initializes the collBudgets collection.
     *
     * By default this just sets the collBudgets collection to an empty array (like clearcollBudgets());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBudgets($overrideExisting = true)
    {
        if (null !== $this->collBudgets && !$overrideExisting) {
            return;
        }
        $this->collBudgets = new PropelObjectCollection();
        $this->collBudgets->setModel('Budget');
    }

    /**
     * Gets an array of Budget objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Currency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Budget[] List of Budget objects
     * @throws PropelException
     */
    public function getBudgets($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collBudgetsPartial && !$this->isNew();
        if (null === $this->collBudgets || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBudgets) {
                // return empty collection
                $this->initBudgets();
            } else {
                $collBudgets = BudgetQuery::create(null, $criteria)
                    ->filterByCurrency($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collBudgetsPartial && count($collBudgets)) {
                      $this->initBudgets(false);

                      foreach ($collBudgets as $obj) {
                        if (false == $this->collBudgets->contains($obj)) {
                          $this->collBudgets->append($obj);
                        }
                      }

                      $this->collBudgetsPartial = true;
                    }

                    $collBudgets->getInternalIterator()->rewind();

                    return $collBudgets;
                }

                if ($partial && $this->collBudgets) {
                    foreach ($this->collBudgets as $obj) {
                        if ($obj->isNew()) {
                            $collBudgets[] = $obj;
                        }
                    }
                }

                $this->collBudgets = $collBudgets;
                $this->collBudgetsPartial = false;
            }
        }

        return $this->collBudgets;
    }

    /**
     * Sets a collection of Budget objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $budgets A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Currency The current object (for fluent API support)
     */
    public function setBudgets(PropelCollection $budgets, PropelPDO $con = null)
    {
        $budgetsToDelete = $this->getBudgets(new Criteria(), $con)->diff($budgets);


        $this->budgetsScheduledForDeletion = $budgetsToDelete;

        foreach ($budgetsToDelete as $budgetRemoved) {
            $budgetRemoved->setCurrency(null);
        }

        $this->collBudgets = null;
        foreach ($budgets as $budget) {
            $this->addBudget($budget);
        }

        $this->collBudgets = $budgets;
        $this->collBudgetsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Budget objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Budget objects.
     * @throws PropelException
     */
    public function countBudgets(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collBudgetsPartial && !$this->isNew();
        if (null === $this->collBudgets || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBudgets) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBudgets());
            }
            $query = BudgetQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrency($this)
                ->count($con);
        }

        return count($this->collBudgets);
    }

    /**
     * Method called to associate a Budget object to this object
     * through the Budget foreign key attribute.
     *
     * @param    Budget $l Budget
     * @return Currency The current object (for fluent API support)
     */
    public function addBudget(Budget $l)
    {
        if ($this->collBudgets === null) {
            $this->initBudgets();
            $this->collBudgetsPartial = true;
        }

        if (!in_array($l, $this->collBudgets->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBudget($l);

            if ($this->budgetsScheduledForDeletion and $this->budgetsScheduledForDeletion->contains($l)) {
                $this->budgetsScheduledForDeletion->remove($this->budgetsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Budget $budget The budget object to add.
     */
    protected function doAddBudget($budget)
    {
        $this->collBudgets[]= $budget;
        $budget->setCurrency($this);
    }

    /**
     * @param  Budget $budget The budget object to remove.
     * @return Currency The current object (for fluent API support)
     */
    public function removeBudget($budget)
    {
        if ($this->getBudgets()->contains($budget)) {
            $this->collBudgets->remove($this->collBudgets->search($budget));
            if (null === $this->budgetsScheduledForDeletion) {
                $this->budgetsScheduledForDeletion = clone $this->collBudgets;
                $this->budgetsScheduledForDeletion->clear();
            }
            $this->budgetsScheduledForDeletion[]= $budget;
            $budget->setCurrency(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Budgets from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Budget[] List of Budget objects
     */
    public function getBudgetsJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = BudgetQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getBudgets($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->title = null;
        $this->format = null;
        $this->is_primary = null;
        $this->rate_to_primary = null;
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
            if ($this->collAccountss) {
                foreach ($this->collAccountss as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBudgets) {
                foreach ($this->collBudgets as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collAccountss instanceof PropelCollection) {
            $this->collAccountss->clearIterator();
        }
        $this->collAccountss = null;
        if ($this->collBudgets instanceof PropelCollection) {
            $this->collBudgets->clearIterator();
        }
        $this->collBudgets = null;
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

<?php

namespace Anthem\Auth\Model\om;

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
use AnthemCM\Feedback\Model\Feedback;
use AnthemCM\Feedback\Model\FeedbackQuery;
use AnthemCM\UserProfile\Model\UserProfile;
use AnthemCM\UserProfile\Model\UserProfileQuery;
use Anthem\Auth\Model\Group;
use Anthem\Auth\Model\GroupQuery;
use Anthem\Auth\Model\RefUserGroup;
use Anthem\Auth\Model\RefUserGroupQuery;
use Anthem\Auth\Model\User;
use Anthem\Auth\Model\UserKey;
use Anthem\Auth\Model\UserKeyQuery;
use Anthem\Auth\Model\UserPeer;
use Anthem\Auth\Model\UserPolicy;
use Anthem\Auth\Model\UserPolicyQuery;
use Anthem\Auth\Model\UserQuery;
use Anthem\Auth\Model\UserSocialAccount;
use Anthem\Auth\Model\UserSocialAccountQuery;
use Anthem\Notify\Model\Notification;
use Anthem\Notify\Model\NotificationQuery;
use Anthem\Settings\Model\Setting;
use Anthem\Settings\Model\SettingQuery;
use Mockingbird\Model\Account;
use Mockingbird\Model\AccountQuery;
use Mockingbird\Model\Budget;
use Mockingbird\Model\BudgetQuery;
use Mockingbird\Model\CounterParty;
use Mockingbird\Model\CounterPartyQuery;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategory;
use Mockingbird\Model\TransactionCategoryQuery;
use Mockingbird\Model\TransactionQuery;
use Mockingbird\Model\TransactionTag;
use Mockingbird\Model\TransactionTagQuery;

/**
 * Base class that represents a row from the 'users' table.
 *
 *
 *
 * @package    propel.generator.Anthem.Auth.Model.om
 */
abstract class BaseUser extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Anthem\\Auth\\Model\\UserPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserPeer
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
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the algorithm field.
     * @var        string
     */
    protected $algorithm;

    /**
     * The value for the salt field.
     * @var        string
     */
    protected $salt;

    /**
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the locked field.
     * @var        string
     */
    protected $locked;

    /**
     * The value for the is_superuser field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $is_superuser;

    /**
     * The value for the last_login field.
     * @var        string
     */
    protected $last_login;

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
     * @var        PropelObjectCollection|Feedback[] Collection to store aggregation of Feedback objects.
     */
    protected $collFeedbacks;
    protected $collFeedbacksPartial;

    /**
     * @var        UserProfile one-to-one related UserProfile object
     */
    protected $singleUserProfile;

    /**
     * @var        PropelObjectCollection|UserKey[] Collection to store aggregation of UserKey objects.
     */
    protected $collKeys;
    protected $collKeysPartial;

    /**
     * @var        PropelObjectCollection|UserSocialAccount[] Collection to store aggregation of UserSocialAccount objects.
     */
    protected $collSocialAccounts;
    protected $collSocialAccountsPartial;

    /**
     * @var        PropelObjectCollection|UserPolicy[] Collection to store aggregation of UserPolicy objects.
     */
    protected $collPolicys;
    protected $collPolicysPartial;

    /**
     * @var        PropelObjectCollection|RefUserGroup[] Collection to store aggregation of RefUserGroup objects.
     */
    protected $collRefGroups;
    protected $collRefGroupsPartial;

    /**
     * @var        PropelObjectCollection|Notification[] Collection to store aggregation of Notification objects.
     */
    protected $collNotifications;
    protected $collNotificationsPartial;

    /**
     * @var        PropelObjectCollection|Setting[] Collection to store aggregation of Setting objects.
     */
    protected $collSettings;
    protected $collSettingsPartial;

    /**
     * @var        PropelObjectCollection|Account[] Collection to store aggregation of Account objects.
     */
    protected $collAccounts;
    protected $collAccountsPartial;

    /**
     * @var        PropelObjectCollection|Transaction[] Collection to store aggregation of Transaction objects.
     */
    protected $collTransactions;
    protected $collTransactionsPartial;

    /**
     * @var        PropelObjectCollection|TransactionCategory[] Collection to store aggregation of TransactionCategory objects.
     */
    protected $collCategorys;
    protected $collCategorysPartial;

    /**
     * @var        PropelObjectCollection|TransactionTag[] Collection to store aggregation of TransactionTag objects.
     */
    protected $collTags;
    protected $collTagsPartial;

    /**
     * @var        PropelObjectCollection|CounterParty[] Collection to store aggregation of CounterParty objects.
     */
    protected $collCounterPartys;
    protected $collCounterPartysPartial;

    /**
     * @var        PropelObjectCollection|Budget[] Collection to store aggregation of Budget objects.
     */
    protected $collBudgets;
    protected $collBudgetsPartial;

    /**
     * @var        PropelObjectCollection|Group[] Collection to store aggregation of Group objects.
     */
    protected $collGroups;

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
    protected $groupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $feedbacksScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $keysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $socialAccountsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $policysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $refGroupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $notificationsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $settingsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $accountsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $transactionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $categorysScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $tagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var    PropelObjectCollection
     */
    protected $counterPartysScheduledForDeletion = null;

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
        $this->is_superuser = false;
    }

    /**
     * Initializes internal state of BaseUser object.
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
     * Get the [email] column value.
     *
     * @return string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [algorithm] column value.
     *
     * @return string
     */
    public function getAlgorithm()
    {

        return $this->algorithm;
    }

    /**
     * Get the [salt] column value.
     *
     * @return string
     */
    public function getSalt()
    {

        return $this->salt;
    }

    /**
     * Get the [password] column value.
     *
     * @return string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Get the [locked] column value.
     *
     * @return string
     */
    public function getLocked()
    {

        return $this->locked;
    }

    /**
     * Get the [is_superuser] column value.
     *
     * @return boolean
     */
    public function getIsSuperuser()
    {

        return $this->is_superuser;
    }

    /**
     * Get the [optionally formatted] temporal [last_login] column value.
     *
     *
     * @param string $format The date/time format string (either date()-style or strftime()-style).
     *         If format is null, then the raw DateTime object will be returned.
     * @return mixed Formatted date/time value as string or DateTime object (if format is null), null if column is null, and 0 if column value is 0000-00-00 00:00:00
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getLastLogin($format = 'Y-m-d H:i:s')
    {
        if ($this->last_login === null) {
            return null;
        }

        if ($this->last_login === '0000-00-00 00:00:00') {
            // while technically this is not a default value of null,
            // this seems to be closest in meaning.
            return null;
        }

        try {
            $dt = new DateTime($this->last_login);
        } catch (Exception $x) {
            throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->last_login, true), $x);
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
     * @return User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [email] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [algorithm] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setAlgorithm($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->algorithm !== $v) {
            $this->algorithm = $v;
            $this->modifiedColumns[] = UserPeer::ALGORITHM;
        }


        return $this;
    } // setAlgorithm()

    /**
     * Set the value of [salt] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setSalt($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->salt !== $v) {
            $this->salt = $v;
            $this->modifiedColumns[] = UserPeer::SALT;
        }


        return $this;
    } // setSalt()

    /**
     * Set the value of [password] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[] = UserPeer::PASSWORD;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [locked] column.
     *
     * @param  string $v new value
     * @return User The current object (for fluent API support)
     */
    public function setLocked($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->locked !== $v) {
            $this->locked = $v;
            $this->modifiedColumns[] = UserPeer::LOCKED;
        }


        return $this;
    } // setLocked()

    /**
     * Sets the value of the [is_superuser] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return User The current object (for fluent API support)
     */
    public function setIsSuperuser($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->is_superuser !== $v) {
            $this->is_superuser = $v;
            $this->modifiedColumns[] = UserPeer::IS_SUPERUSER;
        }


        return $this;
    } // setIsSuperuser()

    /**
     * Sets the value of [last_login] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setLastLogin($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->last_login !== null || $dt !== null) {
            $currentDateAsString = ($this->last_login !== null && $tmpDt = new DateTime($this->last_login)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->last_login = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::LAST_LOGIN;
            }
        } // if either are not null


        return $this;
    } // setLastLogin()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            $currentDateAsString = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->created_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::CREATED_AT;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param mixed $v string, integer (timestamp), or DateTime value.
     *               Empty strings are treated as null.
     * @return User The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            $currentDateAsString = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
            $newDateAsString = $dt ? $dt->format('Y-m-d H:i:s') : null;
            if ($currentDateAsString !== $newDateAsString) {
                $this->updated_at = $newDateAsString;
                $this->modifiedColumns[] = UserPeer::UPDATED_AT;
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
            if ($this->is_superuser !== false) {
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
            $this->email = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->algorithm = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->salt = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->password = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->locked = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->is_superuser = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
            $this->last_login = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->created_at = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->updated_at = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 10; // 10 = UserPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating User object", $e);
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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collFeedbacks = null;

            $this->singleUserProfile = null;

            $this->collKeys = null;

            $this->collSocialAccounts = null;

            $this->collPolicys = null;

            $this->collRefGroups = null;

            $this->collNotifications = null;

            $this->collSettings = null;

            $this->collAccounts = null;

            $this->collTransactions = null;

            $this->collCategorys = null;

            $this->collTags = null;

            $this->collCounterPartys = null;

            $this->collBudgets = null;

            $this->collGroups = null;
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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserQuery::create()
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
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
        // timestampable behavior
        if (!$this->isColumnModified(UserPeer::CREATED_AT))
        {
            $this->setCreatedAt(time());
        }
        if (!$this->isColumnModified(UserPeer::UPDATED_AT))
        {
            $this->setUpdatedAt(time());
        }
            } else {
                $ret = $ret && $this->preUpdate($con);
        // timestampable behavior
        if ($this->isModified() && !$this->isColumnModified(UserPeer::UPDATED_AT))
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
                UserPeer::addInstanceToPool($this);
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

            if ($this->groupsScheduledForDeletion !== null) {
                if (!$this->groupsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->groupsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    RefUserGroupQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->groupsScheduledForDeletion = null;
                }

                foreach ($this->getGroups() as $group) {
                    if ($group->isModified()) {
                        $group->save($con);
                    }
                }
            } elseif ($this->collGroups) {
                foreach ($this->collGroups as $group) {
                    if ($group->isModified()) {
                        $group->save($con);
                    }
                }
            }

            if ($this->feedbacksScheduledForDeletion !== null) {
                if (!$this->feedbacksScheduledForDeletion->isEmpty()) {
                    FeedbackQuery::create()
                        ->filterByPrimaryKeys($this->feedbacksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->feedbacksScheduledForDeletion = null;
                }
            }

            if ($this->collFeedbacks !== null) {
                foreach ($this->collFeedbacks as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleUserProfile !== null) {
                if (!$this->singleUserProfile->isDeleted() && ($this->singleUserProfile->isNew() || $this->singleUserProfile->isModified())) {
                        $affectedRows += $this->singleUserProfile->save($con);
                }
            }

            if ($this->keysScheduledForDeletion !== null) {
                if (!$this->keysScheduledForDeletion->isEmpty()) {
                    UserKeyQuery::create()
                        ->filterByPrimaryKeys($this->keysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->keysScheduledForDeletion = null;
                }
            }

            if ($this->collKeys !== null) {
                foreach ($this->collKeys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->socialAccountsScheduledForDeletion !== null) {
                if (!$this->socialAccountsScheduledForDeletion->isEmpty()) {
                    UserSocialAccountQuery::create()
                        ->filterByPrimaryKeys($this->socialAccountsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->socialAccountsScheduledForDeletion = null;
                }
            }

            if ($this->collSocialAccounts !== null) {
                foreach ($this->collSocialAccounts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->policysScheduledForDeletion !== null) {
                if (!$this->policysScheduledForDeletion->isEmpty()) {
                    UserPolicyQuery::create()
                        ->filterByPrimaryKeys($this->policysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->policysScheduledForDeletion = null;
                }
            }

            if ($this->collPolicys !== null) {
                foreach ($this->collPolicys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->refGroupsScheduledForDeletion !== null) {
                if (!$this->refGroupsScheduledForDeletion->isEmpty()) {
                    RefUserGroupQuery::create()
                        ->filterByPrimaryKeys($this->refGroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->refGroupsScheduledForDeletion = null;
                }
            }

            if ($this->collRefGroups !== null) {
                foreach ($this->collRefGroups as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->notificationsScheduledForDeletion !== null) {
                if (!$this->notificationsScheduledForDeletion->isEmpty()) {
                    NotificationQuery::create()
                        ->filterByPrimaryKeys($this->notificationsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->notificationsScheduledForDeletion = null;
                }
            }

            if ($this->collNotifications !== null) {
                foreach ($this->collNotifications as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->settingsScheduledForDeletion !== null) {
                if (!$this->settingsScheduledForDeletion->isEmpty()) {
                    SettingQuery::create()
                        ->filterByPrimaryKeys($this->settingsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->settingsScheduledForDeletion = null;
                }
            }

            if ($this->collSettings !== null) {
                foreach ($this->collSettings as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->accountsScheduledForDeletion !== null) {
                if (!$this->accountsScheduledForDeletion->isEmpty()) {
                    AccountQuery::create()
                        ->filterByPrimaryKeys($this->accountsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->accountsScheduledForDeletion = null;
                }
            }

            if ($this->collAccounts !== null) {
                foreach ($this->collAccounts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->transactionsScheduledForDeletion !== null) {
                if (!$this->transactionsScheduledForDeletion->isEmpty()) {
                    TransactionQuery::create()
                        ->filterByPrimaryKeys($this->transactionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->transactionsScheduledForDeletion = null;
                }
            }

            if ($this->collTransactions !== null) {
                foreach ($this->collTransactions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categorysScheduledForDeletion !== null) {
                if (!$this->categorysScheduledForDeletion->isEmpty()) {
                    TransactionCategoryQuery::create()
                        ->filterByPrimaryKeys($this->categorysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categorysScheduledForDeletion = null;
                }
            }

            if ($this->collCategorys !== null) {
                foreach ($this->collCategorys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->tagsScheduledForDeletion !== null) {
                if (!$this->tagsScheduledForDeletion->isEmpty()) {
                    TransactionTagQuery::create()
                        ->filterByPrimaryKeys($this->tagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->tagsScheduledForDeletion = null;
                }
            }

            if ($this->collTags !== null) {
                foreach ($this->collTags as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->counterPartysScheduledForDeletion !== null) {
                if (!$this->counterPartysScheduledForDeletion->isEmpty()) {
                    CounterPartyQuery::create()
                        ->filterByPrimaryKeys($this->counterPartysScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->counterPartysScheduledForDeletion = null;
                }
            }

            if ($this->collCounterPartys !== null) {
                foreach ($this->collCounterPartys as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->budgetsScheduledForDeletion !== null) {
                if (!$this->budgetsScheduledForDeletion->isEmpty()) {
                    BudgetQuery::create()
                        ->filterByPrimaryKeys($this->budgetsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
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

        $this->modifiedColumns[] = UserPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(UserPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = '`email`';
        }
        if ($this->isColumnModified(UserPeer::ALGORITHM)) {
            $modifiedColumns[':p' . $index++]  = '`algorithm`';
        }
        if ($this->isColumnModified(UserPeer::SALT)) {
            $modifiedColumns[':p' . $index++]  = '`salt`';
        }
        if ($this->isColumnModified(UserPeer::PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = '`password`';
        }
        if ($this->isColumnModified(UserPeer::LOCKED)) {
            $modifiedColumns[':p' . $index++]  = '`locked`';
        }
        if ($this->isColumnModified(UserPeer::IS_SUPERUSER)) {
            $modifiedColumns[':p' . $index++]  = '`is_superuser`';
        }
        if ($this->isColumnModified(UserPeer::LAST_LOGIN)) {
            $modifiedColumns[':p' . $index++]  = '`last_login`';
        }
        if ($this->isColumnModified(UserPeer::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`created_at`';
        }
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = '`updated_at`';
        }

        $sql = sprintf(
            'INSERT INTO `users` (%s) VALUES (%s)',
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
                    case '`email`':
            $stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case '`algorithm`':
            $stmt->bindValue($identifier, $this->algorithm, PDO::PARAM_STR);
                        break;
                    case '`salt`':
            $stmt->bindValue($identifier, $this->salt, PDO::PARAM_STR);
                        break;
                    case '`password`':
            $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case '`locked`':
            $stmt->bindValue($identifier, $this->locked, PDO::PARAM_STR);
                        break;
                    case '`is_superuser`':
            $stmt->bindValue($identifier, (int) $this->is_superuser, PDO::PARAM_INT);
                        break;
                    case '`last_login`':
            $stmt->bindValue($identifier, $this->last_login, PDO::PARAM_STR);
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


            if (($retval = UserPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collFeedbacks !== null) {
                    foreach ($this->collFeedbacks as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->singleUserProfile !== null) {
                    if (!$this->singleUserProfile->validate($columns)) {
                        $failureMap = array_merge($failureMap, $this->singleUserProfile->getValidationFailures());
                    }
                }

                if ($this->collKeys !== null) {
                    foreach ($this->collKeys as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSocialAccounts !== null) {
                    foreach ($this->collSocialAccounts as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPolicys !== null) {
                    foreach ($this->collPolicys as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collRefGroups !== null) {
                    foreach ($this->collRefGroups as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collNotifications !== null) {
                    foreach ($this->collNotifications as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collSettings !== null) {
                    foreach ($this->collSettings as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collAccounts !== null) {
                    foreach ($this->collAccounts as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collTransactions !== null) {
                    foreach ($this->collTransactions as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCategorys !== null) {
                    foreach ($this->collCategorys as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collTags !== null) {
                    foreach ($this->collTags as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collCounterPartys !== null) {
                    foreach ($this->collCounterPartys as $referrerFK) {
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
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getEmail();
                break;
            case 2:
                return $this->getAlgorithm();
                break;
            case 3:
                return $this->getSalt();
                break;
            case 4:
                return $this->getPassword();
                break;
            case 5:
                return $this->getLocked();
                break;
            case 6:
                return $this->getIsSuperuser();
                break;
            case 7:
                return $this->getLastLogin();
                break;
            case 8:
                return $this->getCreatedAt();
                break;
            case 9:
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
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getEmail(),
            $keys[2] => $this->getAlgorithm(),
            $keys[3] => $this->getSalt(),
            $keys[4] => $this->getPassword(),
            $keys[5] => $this->getLocked(),
            $keys[6] => $this->getIsSuperuser(),
            $keys[7] => $this->getLastLogin(),
            $keys[8] => $this->getCreatedAt(),
            $keys[9] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collFeedbacks) {
                $result['Feedbacks'] = $this->collFeedbacks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleUserProfile) {
                $result['UserProfile'] = $this->singleUserProfile->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->collKeys) {
                $result['Keys'] = $this->collKeys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSocialAccounts) {
                $result['SocialAccounts'] = $this->collSocialAccounts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPolicys) {
                $result['Policys'] = $this->collPolicys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collRefGroups) {
                $result['RefGroups'] = $this->collRefGroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNotifications) {
                $result['Notifications'] = $this->collNotifications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSettings) {
                $result['Settings'] = $this->collSettings->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAccounts) {
                $result['Accounts'] = $this->collAccounts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTransactions) {
                $result['Transactions'] = $this->collTransactions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategorys) {
                $result['Categorys'] = $this->collCategorys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTags) {
                $result['Tags'] = $this->collTags->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCounterPartys) {
                $result['CounterPartys'] = $this->collCounterPartys->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setEmail($value);
                break;
            case 2:
                $this->setAlgorithm($value);
                break;
            case 3:
                $this->setSalt($value);
                break;
            case 4:
                $this->setPassword($value);
                break;
            case 5:
                $this->setLocked($value);
                break;
            case 6:
                $this->setIsSuperuser($value);
                break;
            case 7:
                $this->setLastLogin($value);
                break;
            case 8:
                $this->setCreatedAt($value);
                break;
            case 9:
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
        $keys = UserPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setEmail($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAlgorithm($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSalt($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPassword($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLocked($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setIsSuperuser($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setLastLogin($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCreatedAt($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUpdatedAt($arr[$keys[9]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserPeer::ID)) $criteria->add(UserPeer::ID, $this->id);
        if ($this->isColumnModified(UserPeer::EMAIL)) $criteria->add(UserPeer::EMAIL, $this->email);
        if ($this->isColumnModified(UserPeer::ALGORITHM)) $criteria->add(UserPeer::ALGORITHM, $this->algorithm);
        if ($this->isColumnModified(UserPeer::SALT)) $criteria->add(UserPeer::SALT, $this->salt);
        if ($this->isColumnModified(UserPeer::PASSWORD)) $criteria->add(UserPeer::PASSWORD, $this->password);
        if ($this->isColumnModified(UserPeer::LOCKED)) $criteria->add(UserPeer::LOCKED, $this->locked);
        if ($this->isColumnModified(UserPeer::IS_SUPERUSER)) $criteria->add(UserPeer::IS_SUPERUSER, $this->is_superuser);
        if ($this->isColumnModified(UserPeer::LAST_LOGIN)) $criteria->add(UserPeer::LAST_LOGIN, $this->last_login);
        if ($this->isColumnModified(UserPeer::CREATED_AT)) $criteria->add(UserPeer::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(UserPeer::UPDATED_AT)) $criteria->add(UserPeer::UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(UserPeer::DATABASE_NAME);
        $criteria->add(UserPeer::ID, $this->id);

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
     * @param object $copyObj An object of User (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setEmail($this->getEmail());
        $copyObj->setAlgorithm($this->getAlgorithm());
        $copyObj->setSalt($this->getSalt());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setLocked($this->getLocked());
        $copyObj->setIsSuperuser($this->getIsSuperuser());
        $copyObj->setLastLogin($this->getLastLogin());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getFeedbacks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addFeedback($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getUserProfile();
            if ($relObj) {
                $copyObj->setUserProfile($relObj->copy($deepCopy));
            }

            foreach ($this->getKeys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addKey($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSocialAccounts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSocialAccount($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPolicys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPolicy($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getRefGroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addRefGroup($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNotifications() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotification($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSettings() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSetting($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAccounts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAccount($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTransactions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTransaction($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategorys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTag($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCounterPartys() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCounterParty($relObj->copy($deepCopy));
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
     * @return User Clone of current object.
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
     * @return UserPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserPeer();
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
        if ('Feedback' == $relationName) {
            $this->initFeedbacks();
        }
        if ('Key' == $relationName) {
            $this->initKeys();
        }
        if ('SocialAccount' == $relationName) {
            $this->initSocialAccounts();
        }
        if ('Policy' == $relationName) {
            $this->initPolicys();
        }
        if ('RefGroup' == $relationName) {
            $this->initRefGroups();
        }
        if ('Notification' == $relationName) {
            $this->initNotifications();
        }
        if ('Setting' == $relationName) {
            $this->initSettings();
        }
        if ('Account' == $relationName) {
            $this->initAccounts();
        }
        if ('Transaction' == $relationName) {
            $this->initTransactions();
        }
        if ('Category' == $relationName) {
            $this->initCategorys();
        }
        if ('Tag' == $relationName) {
            $this->initTags();
        }
        if ('CounterParty' == $relationName) {
            $this->initCounterPartys();
        }
        if ('Budget' == $relationName) {
            $this->initBudgets();
        }
    }

    /**
     * Clears out the collFeedbacks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addFeedbacks()
     */
    public function clearFeedbacks()
    {
        $this->collFeedbacks = null; // important to set this to null since that means it is uninitialized
        $this->collFeedbacksPartial = null;

        return $this;
    }

    /**
     * reset is the collFeedbacks collection loaded partially
     *
     * @return void
     */
    public function resetPartialFeedbacks($v = true)
    {
        $this->collFeedbacksPartial = $v;
    }

    /**
     * Initializes the collFeedbacks collection.
     *
     * By default this just sets the collFeedbacks collection to an empty array (like clearcollFeedbacks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initFeedbacks($overrideExisting = true)
    {
        if (null !== $this->collFeedbacks && !$overrideExisting) {
            return;
        }
        $this->collFeedbacks = new PropelObjectCollection();
        $this->collFeedbacks->setModel('Feedback');
    }

    /**
     * Gets an array of Feedback objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Feedback[] List of Feedback objects
     * @throws PropelException
     */
    public function getFeedbacks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collFeedbacksPartial && !$this->isNew();
        if (null === $this->collFeedbacks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collFeedbacks) {
                // return empty collection
                $this->initFeedbacks();
            } else {
                $collFeedbacks = FeedbackQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collFeedbacksPartial && count($collFeedbacks)) {
                      $this->initFeedbacks(false);

                      foreach ($collFeedbacks as $obj) {
                        if (false == $this->collFeedbacks->contains($obj)) {
                          $this->collFeedbacks->append($obj);
                        }
                      }

                      $this->collFeedbacksPartial = true;
                    }

                    $collFeedbacks->getInternalIterator()->rewind();

                    return $collFeedbacks;
                }

                if ($partial && $this->collFeedbacks) {
                    foreach ($this->collFeedbacks as $obj) {
                        if ($obj->isNew()) {
                            $collFeedbacks[] = $obj;
                        }
                    }
                }

                $this->collFeedbacks = $collFeedbacks;
                $this->collFeedbacksPartial = false;
            }
        }

        return $this->collFeedbacks;
    }

    /**
     * Sets a collection of Feedback objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $feedbacks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setFeedbacks(PropelCollection $feedbacks, PropelPDO $con = null)
    {
        $feedbacksToDelete = $this->getFeedbacks(new Criteria(), $con)->diff($feedbacks);


        $this->feedbacksScheduledForDeletion = $feedbacksToDelete;

        foreach ($feedbacksToDelete as $feedbackRemoved) {
            $feedbackRemoved->setUser(null);
        }

        $this->collFeedbacks = null;
        foreach ($feedbacks as $feedback) {
            $this->addFeedback($feedback);
        }

        $this->collFeedbacks = $feedbacks;
        $this->collFeedbacksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Feedback objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Feedback objects.
     * @throws PropelException
     */
    public function countFeedbacks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collFeedbacksPartial && !$this->isNew();
        if (null === $this->collFeedbacks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collFeedbacks) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getFeedbacks());
            }
            $query = FeedbackQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collFeedbacks);
    }

    /**
     * Method called to associate a Feedback object to this object
     * through the Feedback foreign key attribute.
     *
     * @param    Feedback $l Feedback
     * @return User The current object (for fluent API support)
     */
    public function addFeedback(Feedback $l)
    {
        if ($this->collFeedbacks === null) {
            $this->initFeedbacks();
            $this->collFeedbacksPartial = true;
        }

        if (!in_array($l, $this->collFeedbacks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddFeedback($l);

            if ($this->feedbacksScheduledForDeletion and $this->feedbacksScheduledForDeletion->contains($l)) {
                $this->feedbacksScheduledForDeletion->remove($this->feedbacksScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Feedback $feedback The feedback object to add.
     */
    protected function doAddFeedback($feedback)
    {
        $this->collFeedbacks[]= $feedback;
        $feedback->setUser($this);
    }

    /**
     * @param  Feedback $feedback The feedback object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeFeedback($feedback)
    {
        if ($this->getFeedbacks()->contains($feedback)) {
            $this->collFeedbacks->remove($this->collFeedbacks->search($feedback));
            if (null === $this->feedbacksScheduledForDeletion) {
                $this->feedbacksScheduledForDeletion = clone $this->collFeedbacks;
                $this->feedbacksScheduledForDeletion->clear();
            }
            $this->feedbacksScheduledForDeletion[]= $feedback;
            $feedback->setUser(null);
        }

        return $this;
    }

    /**
     * Gets a single UserProfile object, which is related to this object by a one-to-one relationship.
     *
     * @param PropelPDO $con optional connection object
     * @return UserProfile
     * @throws PropelException
     */
    public function getUserProfile(PropelPDO $con = null)
    {

        if ($this->singleUserProfile === null && !$this->isNew()) {
            $this->singleUserProfile = UserProfileQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleUserProfile;
    }

    /**
     * Sets a single UserProfile object as related to this object by a one-to-one relationship.
     *
     * @param                  UserProfile $v UserProfile
     * @return User The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUserProfile(UserProfile $v = null)
    {
        $this->singleUserProfile = $v;

        // Make sure that that the passed-in UserProfile isn't already associated with this object
        if ($v !== null && $v->getUser(null, false) === null) {
            $v->setUser($this);
        }

        return $this;
    }

    /**
     * Clears out the collKeys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addKeys()
     */
    public function clearKeys()
    {
        $this->collKeys = null; // important to set this to null since that means it is uninitialized
        $this->collKeysPartial = null;

        return $this;
    }

    /**
     * reset is the collKeys collection loaded partially
     *
     * @return void
     */
    public function resetPartialKeys($v = true)
    {
        $this->collKeysPartial = $v;
    }

    /**
     * Initializes the collKeys collection.
     *
     * By default this just sets the collKeys collection to an empty array (like clearcollKeys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initKeys($overrideExisting = true)
    {
        if (null !== $this->collKeys && !$overrideExisting) {
            return;
        }
        $this->collKeys = new PropelObjectCollection();
        $this->collKeys->setModel('UserKey');
    }

    /**
     * Gets an array of UserKey objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserKey[] List of UserKey objects
     * @throws PropelException
     */
    public function getKeys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collKeysPartial && !$this->isNew();
        if (null === $this->collKeys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collKeys) {
                // return empty collection
                $this->initKeys();
            } else {
                $collKeys = UserKeyQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collKeysPartial && count($collKeys)) {
                      $this->initKeys(false);

                      foreach ($collKeys as $obj) {
                        if (false == $this->collKeys->contains($obj)) {
                          $this->collKeys->append($obj);
                        }
                      }

                      $this->collKeysPartial = true;
                    }

                    $collKeys->getInternalIterator()->rewind();

                    return $collKeys;
                }

                if ($partial && $this->collKeys) {
                    foreach ($this->collKeys as $obj) {
                        if ($obj->isNew()) {
                            $collKeys[] = $obj;
                        }
                    }
                }

                $this->collKeys = $collKeys;
                $this->collKeysPartial = false;
            }
        }

        return $this->collKeys;
    }

    /**
     * Sets a collection of Key objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $keys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setKeys(PropelCollection $keys, PropelPDO $con = null)
    {
        $keysToDelete = $this->getKeys(new Criteria(), $con)->diff($keys);


        $this->keysScheduledForDeletion = $keysToDelete;

        foreach ($keysToDelete as $keyRemoved) {
            $keyRemoved->setUser(null);
        }

        $this->collKeys = null;
        foreach ($keys as $key) {
            $this->addKey($key);
        }

        $this->collKeys = $keys;
        $this->collKeysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserKey objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserKey objects.
     * @throws PropelException
     */
    public function countKeys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collKeysPartial && !$this->isNew();
        if (null === $this->collKeys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collKeys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getKeys());
            }
            $query = UserKeyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collKeys);
    }

    /**
     * Method called to associate a UserKey object to this object
     * through the UserKey foreign key attribute.
     *
     * @param    UserKey $l UserKey
     * @return User The current object (for fluent API support)
     */
    public function addKey(UserKey $l)
    {
        if ($this->collKeys === null) {
            $this->initKeys();
            $this->collKeysPartial = true;
        }

        if (!in_array($l, $this->collKeys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddKey($l);

            if ($this->keysScheduledForDeletion and $this->keysScheduledForDeletion->contains($l)) {
                $this->keysScheduledForDeletion->remove($this->keysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Key $key The key object to add.
     */
    protected function doAddKey($key)
    {
        $this->collKeys[]= $key;
        $key->setUser($this);
    }

    /**
     * @param  Key $key The key object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeKey($key)
    {
        if ($this->getKeys()->contains($key)) {
            $this->collKeys->remove($this->collKeys->search($key));
            if (null === $this->keysScheduledForDeletion) {
                $this->keysScheduledForDeletion = clone $this->collKeys;
                $this->keysScheduledForDeletion->clear();
            }
            $this->keysScheduledForDeletion[]= clone $key;
            $key->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collSocialAccounts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addSocialAccounts()
     */
    public function clearSocialAccounts()
    {
        $this->collSocialAccounts = null; // important to set this to null since that means it is uninitialized
        $this->collSocialAccountsPartial = null;

        return $this;
    }

    /**
     * reset is the collSocialAccounts collection loaded partially
     *
     * @return void
     */
    public function resetPartialSocialAccounts($v = true)
    {
        $this->collSocialAccountsPartial = $v;
    }

    /**
     * Initializes the collSocialAccounts collection.
     *
     * By default this just sets the collSocialAccounts collection to an empty array (like clearcollSocialAccounts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSocialAccounts($overrideExisting = true)
    {
        if (null !== $this->collSocialAccounts && !$overrideExisting) {
            return;
        }
        $this->collSocialAccounts = new PropelObjectCollection();
        $this->collSocialAccounts->setModel('UserSocialAccount');
    }

    /**
     * Gets an array of UserSocialAccount objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserSocialAccount[] List of UserSocialAccount objects
     * @throws PropelException
     */
    public function getSocialAccounts($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSocialAccountsPartial && !$this->isNew();
        if (null === $this->collSocialAccounts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSocialAccounts) {
                // return empty collection
                $this->initSocialAccounts();
            } else {
                $collSocialAccounts = UserSocialAccountQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSocialAccountsPartial && count($collSocialAccounts)) {
                      $this->initSocialAccounts(false);

                      foreach ($collSocialAccounts as $obj) {
                        if (false == $this->collSocialAccounts->contains($obj)) {
                          $this->collSocialAccounts->append($obj);
                        }
                      }

                      $this->collSocialAccountsPartial = true;
                    }

                    $collSocialAccounts->getInternalIterator()->rewind();

                    return $collSocialAccounts;
                }

                if ($partial && $this->collSocialAccounts) {
                    foreach ($this->collSocialAccounts as $obj) {
                        if ($obj->isNew()) {
                            $collSocialAccounts[] = $obj;
                        }
                    }
                }

                $this->collSocialAccounts = $collSocialAccounts;
                $this->collSocialAccountsPartial = false;
            }
        }

        return $this->collSocialAccounts;
    }

    /**
     * Sets a collection of SocialAccount objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $socialAccounts A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setSocialAccounts(PropelCollection $socialAccounts, PropelPDO $con = null)
    {
        $socialAccountsToDelete = $this->getSocialAccounts(new Criteria(), $con)->diff($socialAccounts);


        $this->socialAccountsScheduledForDeletion = $socialAccountsToDelete;

        foreach ($socialAccountsToDelete as $socialAccountRemoved) {
            $socialAccountRemoved->setUser(null);
        }

        $this->collSocialAccounts = null;
        foreach ($socialAccounts as $socialAccount) {
            $this->addSocialAccount($socialAccount);
        }

        $this->collSocialAccounts = $socialAccounts;
        $this->collSocialAccountsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserSocialAccount objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserSocialAccount objects.
     * @throws PropelException
     */
    public function countSocialAccounts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSocialAccountsPartial && !$this->isNew();
        if (null === $this->collSocialAccounts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSocialAccounts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSocialAccounts());
            }
            $query = UserSocialAccountQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collSocialAccounts);
    }

    /**
     * Method called to associate a UserSocialAccount object to this object
     * through the UserSocialAccount foreign key attribute.
     *
     * @param    UserSocialAccount $l UserSocialAccount
     * @return User The current object (for fluent API support)
     */
    public function addSocialAccount(UserSocialAccount $l)
    {
        if ($this->collSocialAccounts === null) {
            $this->initSocialAccounts();
            $this->collSocialAccountsPartial = true;
        }

        if (!in_array($l, $this->collSocialAccounts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSocialAccount($l);

            if ($this->socialAccountsScheduledForDeletion and $this->socialAccountsScheduledForDeletion->contains($l)) {
                $this->socialAccountsScheduledForDeletion->remove($this->socialAccountsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  SocialAccount $socialAccount The socialAccount object to add.
     */
    protected function doAddSocialAccount($socialAccount)
    {
        $this->collSocialAccounts[]= $socialAccount;
        $socialAccount->setUser($this);
    }

    /**
     * @param  SocialAccount $socialAccount The socialAccount object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeSocialAccount($socialAccount)
    {
        if ($this->getSocialAccounts()->contains($socialAccount)) {
            $this->collSocialAccounts->remove($this->collSocialAccounts->search($socialAccount));
            if (null === $this->socialAccountsScheduledForDeletion) {
                $this->socialAccountsScheduledForDeletion = clone $this->collSocialAccounts;
                $this->socialAccountsScheduledForDeletion->clear();
            }
            $this->socialAccountsScheduledForDeletion[]= clone $socialAccount;
            $socialAccount->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collPolicys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addPolicys()
     */
    public function clearPolicys()
    {
        $this->collPolicys = null; // important to set this to null since that means it is uninitialized
        $this->collPolicysPartial = null;

        return $this;
    }

    /**
     * reset is the collPolicys collection loaded partially
     *
     * @return void
     */
    public function resetPartialPolicys($v = true)
    {
        $this->collPolicysPartial = $v;
    }

    /**
     * Initializes the collPolicys collection.
     *
     * By default this just sets the collPolicys collection to an empty array (like clearcollPolicys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPolicys($overrideExisting = true)
    {
        if (null !== $this->collPolicys && !$overrideExisting) {
            return;
        }
        $this->collPolicys = new PropelObjectCollection();
        $this->collPolicys->setModel('UserPolicy');
    }

    /**
     * Gets an array of UserPolicy objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserPolicy[] List of UserPolicy objects
     * @throws PropelException
     */
    public function getPolicys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPolicysPartial && !$this->isNew();
        if (null === $this->collPolicys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPolicys) {
                // return empty collection
                $this->initPolicys();
            } else {
                $collPolicys = UserPolicyQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPolicysPartial && count($collPolicys)) {
                      $this->initPolicys(false);

                      foreach ($collPolicys as $obj) {
                        if (false == $this->collPolicys->contains($obj)) {
                          $this->collPolicys->append($obj);
                        }
                      }

                      $this->collPolicysPartial = true;
                    }

                    $collPolicys->getInternalIterator()->rewind();

                    return $collPolicys;
                }

                if ($partial && $this->collPolicys) {
                    foreach ($this->collPolicys as $obj) {
                        if ($obj->isNew()) {
                            $collPolicys[] = $obj;
                        }
                    }
                }

                $this->collPolicys = $collPolicys;
                $this->collPolicysPartial = false;
            }
        }

        return $this->collPolicys;
    }

    /**
     * Sets a collection of Policy objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $policys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setPolicys(PropelCollection $policys, PropelPDO $con = null)
    {
        $policysToDelete = $this->getPolicys(new Criteria(), $con)->diff($policys);


        $this->policysScheduledForDeletion = $policysToDelete;

        foreach ($policysToDelete as $policyRemoved) {
            $policyRemoved->setUser(null);
        }

        $this->collPolicys = null;
        foreach ($policys as $policy) {
            $this->addPolicy($policy);
        }

        $this->collPolicys = $policys;
        $this->collPolicysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserPolicy objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related UserPolicy objects.
     * @throws PropelException
     */
    public function countPolicys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPolicysPartial && !$this->isNew();
        if (null === $this->collPolicys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPolicys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPolicys());
            }
            $query = UserPolicyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collPolicys);
    }

    /**
     * Method called to associate a UserPolicy object to this object
     * through the UserPolicy foreign key attribute.
     *
     * @param    UserPolicy $l UserPolicy
     * @return User The current object (for fluent API support)
     */
    public function addPolicy(UserPolicy $l)
    {
        if ($this->collPolicys === null) {
            $this->initPolicys();
            $this->collPolicysPartial = true;
        }

        if (!in_array($l, $this->collPolicys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPolicy($l);

            if ($this->policysScheduledForDeletion and $this->policysScheduledForDeletion->contains($l)) {
                $this->policysScheduledForDeletion->remove($this->policysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Policy $policy The policy object to add.
     */
    protected function doAddPolicy($policy)
    {
        $this->collPolicys[]= $policy;
        $policy->setUser($this);
    }

    /**
     * @param  Policy $policy The policy object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removePolicy($policy)
    {
        if ($this->getPolicys()->contains($policy)) {
            $this->collPolicys->remove($this->collPolicys->search($policy));
            if (null === $this->policysScheduledForDeletion) {
                $this->policysScheduledForDeletion = clone $this->collPolicys;
                $this->policysScheduledForDeletion->clear();
            }
            $this->policysScheduledForDeletion[]= clone $policy;
            $policy->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collRefGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addRefGroups()
     */
    public function clearRefGroups()
    {
        $this->collRefGroups = null; // important to set this to null since that means it is uninitialized
        $this->collRefGroupsPartial = null;

        return $this;
    }

    /**
     * reset is the collRefGroups collection loaded partially
     *
     * @return void
     */
    public function resetPartialRefGroups($v = true)
    {
        $this->collRefGroupsPartial = $v;
    }

    /**
     * Initializes the collRefGroups collection.
     *
     * By default this just sets the collRefGroups collection to an empty array (like clearcollRefGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initRefGroups($overrideExisting = true)
    {
        if (null !== $this->collRefGroups && !$overrideExisting) {
            return;
        }
        $this->collRefGroups = new PropelObjectCollection();
        $this->collRefGroups->setModel('RefUserGroup');
    }

    /**
     * Gets an array of RefUserGroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|RefUserGroup[] List of RefUserGroup objects
     * @throws PropelException
     */
    public function getRefGroups($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collRefGroupsPartial && !$this->isNew();
        if (null === $this->collRefGroups || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collRefGroups) {
                // return empty collection
                $this->initRefGroups();
            } else {
                $collRefGroups = RefUserGroupQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collRefGroupsPartial && count($collRefGroups)) {
                      $this->initRefGroups(false);

                      foreach ($collRefGroups as $obj) {
                        if (false == $this->collRefGroups->contains($obj)) {
                          $this->collRefGroups->append($obj);
                        }
                      }

                      $this->collRefGroupsPartial = true;
                    }

                    $collRefGroups->getInternalIterator()->rewind();

                    return $collRefGroups;
                }

                if ($partial && $this->collRefGroups) {
                    foreach ($this->collRefGroups as $obj) {
                        if ($obj->isNew()) {
                            $collRefGroups[] = $obj;
                        }
                    }
                }

                $this->collRefGroups = $collRefGroups;
                $this->collRefGroupsPartial = false;
            }
        }

        return $this->collRefGroups;
    }

    /**
     * Sets a collection of RefGroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $refGroups A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setRefGroups(PropelCollection $refGroups, PropelPDO $con = null)
    {
        $refGroupsToDelete = $this->getRefGroups(new Criteria(), $con)->diff($refGroups);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->refGroupsScheduledForDeletion = clone $refGroupsToDelete;

        foreach ($refGroupsToDelete as $refGroupRemoved) {
            $refGroupRemoved->setUser(null);
        }

        $this->collRefGroups = null;
        foreach ($refGroups as $refGroup) {
            $this->addRefGroup($refGroup);
        }

        $this->collRefGroups = $refGroups;
        $this->collRefGroupsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related RefUserGroup objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related RefUserGroup objects.
     * @throws PropelException
     */
    public function countRefGroups(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collRefGroupsPartial && !$this->isNew();
        if (null === $this->collRefGroups || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collRefGroups) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getRefGroups());
            }
            $query = RefUserGroupQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collRefGroups);
    }

    /**
     * Method called to associate a RefUserGroup object to this object
     * through the RefUserGroup foreign key attribute.
     *
     * @param    RefUserGroup $l RefUserGroup
     * @return User The current object (for fluent API support)
     */
    public function addRefGroup(RefUserGroup $l)
    {
        if ($this->collRefGroups === null) {
            $this->initRefGroups();
            $this->collRefGroupsPartial = true;
        }

        if (!in_array($l, $this->collRefGroups->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddRefGroup($l);

            if ($this->refGroupsScheduledForDeletion and $this->refGroupsScheduledForDeletion->contains($l)) {
                $this->refGroupsScheduledForDeletion->remove($this->refGroupsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  RefGroup $refGroup The refGroup object to add.
     */
    protected function doAddRefGroup($refGroup)
    {
        $this->collRefGroups[]= $refGroup;
        $refGroup->setUser($this);
    }

    /**
     * @param  RefGroup $refGroup The refGroup object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeRefGroup($refGroup)
    {
        if ($this->getRefGroups()->contains($refGroup)) {
            $this->collRefGroups->remove($this->collRefGroups->search($refGroup));
            if (null === $this->refGroupsScheduledForDeletion) {
                $this->refGroupsScheduledForDeletion = clone $this->collRefGroups;
                $this->refGroupsScheduledForDeletion->clear();
            }
            $this->refGroupsScheduledForDeletion[]= clone $refGroup;
            $refGroup->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related RefGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|RefUserGroup[] List of RefUserGroup objects
     */
    public function getRefGroupsJoinGroup($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = RefUserGroupQuery::create(null, $criteria);
        $query->joinWith('Group', $join_behavior);

        return $this->getRefGroups($query, $con);
    }

    /**
     * Clears out the collNotifications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addNotifications()
     */
    public function clearNotifications()
    {
        $this->collNotifications = null; // important to set this to null since that means it is uninitialized
        $this->collNotificationsPartial = null;

        return $this;
    }

    /**
     * reset is the collNotifications collection loaded partially
     *
     * @return void
     */
    public function resetPartialNotifications($v = true)
    {
        $this->collNotificationsPartial = $v;
    }

    /**
     * Initializes the collNotifications collection.
     *
     * By default this just sets the collNotifications collection to an empty array (like clearcollNotifications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotifications($overrideExisting = true)
    {
        if (null !== $this->collNotifications && !$overrideExisting) {
            return;
        }
        $this->collNotifications = new PropelObjectCollection();
        $this->collNotifications->setModel('Notification');
    }

    /**
     * Gets an array of Notification objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Notification[] List of Notification objects
     * @throws PropelException
     */
    public function getNotifications($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsPartial && !$this->isNew();
        if (null === $this->collNotifications || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotifications) {
                // return empty collection
                $this->initNotifications();
            } else {
                $collNotifications = NotificationQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collNotificationsPartial && count($collNotifications)) {
                      $this->initNotifications(false);

                      foreach ($collNotifications as $obj) {
                        if (false == $this->collNotifications->contains($obj)) {
                          $this->collNotifications->append($obj);
                        }
                      }

                      $this->collNotificationsPartial = true;
                    }

                    $collNotifications->getInternalIterator()->rewind();

                    return $collNotifications;
                }

                if ($partial && $this->collNotifications) {
                    foreach ($this->collNotifications as $obj) {
                        if ($obj->isNew()) {
                            $collNotifications[] = $obj;
                        }
                    }
                }

                $this->collNotifications = $collNotifications;
                $this->collNotificationsPartial = false;
            }
        }

        return $this->collNotifications;
    }

    /**
     * Sets a collection of Notification objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $notifications A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setNotifications(PropelCollection $notifications, PropelPDO $con = null)
    {
        $notificationsToDelete = $this->getNotifications(new Criteria(), $con)->diff($notifications);


        $this->notificationsScheduledForDeletion = $notificationsToDelete;

        foreach ($notificationsToDelete as $notificationRemoved) {
            $notificationRemoved->setUser(null);
        }

        $this->collNotifications = null;
        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }

        $this->collNotifications = $notifications;
        $this->collNotificationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Notification objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Notification objects.
     * @throws PropelException
     */
    public function countNotifications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collNotificationsPartial && !$this->isNew();
        if (null === $this->collNotifications || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotifications) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNotifications());
            }
            $query = NotificationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collNotifications);
    }

    /**
     * Method called to associate a Notification object to this object
     * through the Notification foreign key attribute.
     *
     * @param    Notification $l Notification
     * @return User The current object (for fluent API support)
     */
    public function addNotification(Notification $l)
    {
        if ($this->collNotifications === null) {
            $this->initNotifications();
            $this->collNotificationsPartial = true;
        }

        if (!in_array($l, $this->collNotifications->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotification($l);

            if ($this->notificationsScheduledForDeletion and $this->notificationsScheduledForDeletion->contains($l)) {
                $this->notificationsScheduledForDeletion->remove($this->notificationsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Notification $notification The notification object to add.
     */
    protected function doAddNotification($notification)
    {
        $this->collNotifications[]= $notification;
        $notification->setUser($this);
    }

    /**
     * @param  Notification $notification The notification object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeNotification($notification)
    {
        if ($this->getNotifications()->contains($notification)) {
            $this->collNotifications->remove($this->collNotifications->search($notification));
            if (null === $this->notificationsScheduledForDeletion) {
                $this->notificationsScheduledForDeletion = clone $this->collNotifications;
                $this->notificationsScheduledForDeletion->clear();
            }
            $this->notificationsScheduledForDeletion[]= $notification;
            $notification->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collSettings collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addSettings()
     */
    public function clearSettings()
    {
        $this->collSettings = null; // important to set this to null since that means it is uninitialized
        $this->collSettingsPartial = null;

        return $this;
    }

    /**
     * reset is the collSettings collection loaded partially
     *
     * @return void
     */
    public function resetPartialSettings($v = true)
    {
        $this->collSettingsPartial = $v;
    }

    /**
     * Initializes the collSettings collection.
     *
     * By default this just sets the collSettings collection to an empty array (like clearcollSettings());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSettings($overrideExisting = true)
    {
        if (null !== $this->collSettings && !$overrideExisting) {
            return;
        }
        $this->collSettings = new PropelObjectCollection();
        $this->collSettings->setModel('Setting');
    }

    /**
     * Gets an array of Setting objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Setting[] List of Setting objects
     * @throws PropelException
     */
    public function getSettings($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collSettingsPartial && !$this->isNew();
        if (null === $this->collSettings || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSettings) {
                // return empty collection
                $this->initSettings();
            } else {
                $collSettings = SettingQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collSettingsPartial && count($collSettings)) {
                      $this->initSettings(false);

                      foreach ($collSettings as $obj) {
                        if (false == $this->collSettings->contains($obj)) {
                          $this->collSettings->append($obj);
                        }
                      }

                      $this->collSettingsPartial = true;
                    }

                    $collSettings->getInternalIterator()->rewind();

                    return $collSettings;
                }

                if ($partial && $this->collSettings) {
                    foreach ($this->collSettings as $obj) {
                        if ($obj->isNew()) {
                            $collSettings[] = $obj;
                        }
                    }
                }

                $this->collSettings = $collSettings;
                $this->collSettingsPartial = false;
            }
        }

        return $this->collSettings;
    }

    /**
     * Sets a collection of Setting objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $settings A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setSettings(PropelCollection $settings, PropelPDO $con = null)
    {
        $settingsToDelete = $this->getSettings(new Criteria(), $con)->diff($settings);


        $this->settingsScheduledForDeletion = $settingsToDelete;

        foreach ($settingsToDelete as $settingRemoved) {
            $settingRemoved->setUser(null);
        }

        $this->collSettings = null;
        foreach ($settings as $setting) {
            $this->addSetting($setting);
        }

        $this->collSettings = $settings;
        $this->collSettingsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Setting objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Setting objects.
     * @throws PropelException
     */
    public function countSettings(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collSettingsPartial && !$this->isNew();
        if (null === $this->collSettings || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSettings) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSettings());
            }
            $query = SettingQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collSettings);
    }

    /**
     * Method called to associate a Setting object to this object
     * through the Setting foreign key attribute.
     *
     * @param    Setting $l Setting
     * @return User The current object (for fluent API support)
     */
    public function addSetting(Setting $l)
    {
        if ($this->collSettings === null) {
            $this->initSettings();
            $this->collSettingsPartial = true;
        }

        if (!in_array($l, $this->collSettings->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSetting($l);

            if ($this->settingsScheduledForDeletion and $this->settingsScheduledForDeletion->contains($l)) {
                $this->settingsScheduledForDeletion->remove($this->settingsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Setting $setting The setting object to add.
     */
    protected function doAddSetting($setting)
    {
        $this->collSettings[]= $setting;
        $setting->setUser($this);
    }

    /**
     * @param  Setting $setting The setting object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeSetting($setting)
    {
        if ($this->getSettings()->contains($setting)) {
            $this->collSettings->remove($this->collSettings->search($setting));
            if (null === $this->settingsScheduledForDeletion) {
                $this->settingsScheduledForDeletion = clone $this->collSettings;
                $this->settingsScheduledForDeletion->clear();
            }
            $this->settingsScheduledForDeletion[]= $setting;
            $setting->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collAccounts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addAccounts()
     */
    public function clearAccounts()
    {
        $this->collAccounts = null; // important to set this to null since that means it is uninitialized
        $this->collAccountsPartial = null;

        return $this;
    }

    /**
     * reset is the collAccounts collection loaded partially
     *
     * @return void
     */
    public function resetPartialAccounts($v = true)
    {
        $this->collAccountsPartial = $v;
    }

    /**
     * Initializes the collAccounts collection.
     *
     * By default this just sets the collAccounts collection to an empty array (like clearcollAccounts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAccounts($overrideExisting = true)
    {
        if (null !== $this->collAccounts && !$overrideExisting) {
            return;
        }
        $this->collAccounts = new PropelObjectCollection();
        $this->collAccounts->setModel('Account');
    }

    /**
     * Gets an array of Account objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Account[] List of Account objects
     * @throws PropelException
     */
    public function getAccounts($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collAccountsPartial && !$this->isNew();
        if (null === $this->collAccounts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAccounts) {
                // return empty collection
                $this->initAccounts();
            } else {
                $collAccounts = AccountQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collAccountsPartial && count($collAccounts)) {
                      $this->initAccounts(false);

                      foreach ($collAccounts as $obj) {
                        if (false == $this->collAccounts->contains($obj)) {
                          $this->collAccounts->append($obj);
                        }
                      }

                      $this->collAccountsPartial = true;
                    }

                    $collAccounts->getInternalIterator()->rewind();

                    return $collAccounts;
                }

                if ($partial && $this->collAccounts) {
                    foreach ($this->collAccounts as $obj) {
                        if ($obj->isNew()) {
                            $collAccounts[] = $obj;
                        }
                    }
                }

                $this->collAccounts = $collAccounts;
                $this->collAccountsPartial = false;
            }
        }

        return $this->collAccounts;
    }

    /**
     * Sets a collection of Account objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $accounts A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setAccounts(PropelCollection $accounts, PropelPDO $con = null)
    {
        $accountsToDelete = $this->getAccounts(new Criteria(), $con)->diff($accounts);


        $this->accountsScheduledForDeletion = $accountsToDelete;

        foreach ($accountsToDelete as $accountRemoved) {
            $accountRemoved->setUser(null);
        }

        $this->collAccounts = null;
        foreach ($accounts as $account) {
            $this->addAccount($account);
        }

        $this->collAccounts = $accounts;
        $this->collAccountsPartial = false;

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
    public function countAccounts(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collAccountsPartial && !$this->isNew();
        if (null === $this->collAccounts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAccounts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAccounts());
            }
            $query = AccountQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collAccounts);
    }

    /**
     * Method called to associate a Account object to this object
     * through the Account foreign key attribute.
     *
     * @param    Account $l Account
     * @return User The current object (for fluent API support)
     */
    public function addAccount(Account $l)
    {
        if ($this->collAccounts === null) {
            $this->initAccounts();
            $this->collAccountsPartial = true;
        }

        if (!in_array($l, $this->collAccounts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAccount($l);

            if ($this->accountsScheduledForDeletion and $this->accountsScheduledForDeletion->contains($l)) {
                $this->accountsScheduledForDeletion->remove($this->accountsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Account $account The account object to add.
     */
    protected function doAddAccount($account)
    {
        $this->collAccounts[]= $account;
        $account->setUser($this);
    }

    /**
     * @param  Account $account The account object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeAccount($account)
    {
        if ($this->getAccounts()->contains($account)) {
            $this->collAccounts->remove($this->collAccounts->search($account));
            if (null === $this->accountsScheduledForDeletion) {
                $this->accountsScheduledForDeletion = clone $this->collAccounts;
                $this->accountsScheduledForDeletion->clear();
            }
            $this->accountsScheduledForDeletion[]= clone $account;
            $account->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Accounts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Account[] List of Account objects
     */
    public function getAccountsJoinCurrency($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = AccountQuery::create(null, $criteria);
        $query->joinWith('Currency', $join_behavior);

        return $this->getAccounts($query, $con);
    }

    /**
     * Clears out the collTransactions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addTransactions()
     */
    public function clearTransactions()
    {
        $this->collTransactions = null; // important to set this to null since that means it is uninitialized
        $this->collTransactionsPartial = null;

        return $this;
    }

    /**
     * reset is the collTransactions collection loaded partially
     *
     * @return void
     */
    public function resetPartialTransactions($v = true)
    {
        $this->collTransactionsPartial = $v;
    }

    /**
     * Initializes the collTransactions collection.
     *
     * By default this just sets the collTransactions collection to an empty array (like clearcollTransactions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTransactions($overrideExisting = true)
    {
        if (null !== $this->collTransactions && !$overrideExisting) {
            return;
        }
        $this->collTransactions = new PropelObjectCollection();
        $this->collTransactions->setModel('Transaction');
    }

    /**
     * Gets an array of Transaction objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     * @throws PropelException
     */
    public function getTransactions($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTransactionsPartial && !$this->isNew();
        if (null === $this->collTransactions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTransactions) {
                // return empty collection
                $this->initTransactions();
            } else {
                $collTransactions = TransactionQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTransactionsPartial && count($collTransactions)) {
                      $this->initTransactions(false);

                      foreach ($collTransactions as $obj) {
                        if (false == $this->collTransactions->contains($obj)) {
                          $this->collTransactions->append($obj);
                        }
                      }

                      $this->collTransactionsPartial = true;
                    }

                    $collTransactions->getInternalIterator()->rewind();

                    return $collTransactions;
                }

                if ($partial && $this->collTransactions) {
                    foreach ($this->collTransactions as $obj) {
                        if ($obj->isNew()) {
                            $collTransactions[] = $obj;
                        }
                    }
                }

                $this->collTransactions = $collTransactions;
                $this->collTransactionsPartial = false;
            }
        }

        return $this->collTransactions;
    }

    /**
     * Sets a collection of Transaction objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $transactions A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setTransactions(PropelCollection $transactions, PropelPDO $con = null)
    {
        $transactionsToDelete = $this->getTransactions(new Criteria(), $con)->diff($transactions);


        $this->transactionsScheduledForDeletion = $transactionsToDelete;

        foreach ($transactionsToDelete as $transactionRemoved) {
            $transactionRemoved->setUser(null);
        }

        $this->collTransactions = null;
        foreach ($transactions as $transaction) {
            $this->addTransaction($transaction);
        }

        $this->collTransactions = $transactions;
        $this->collTransactionsPartial = false;

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
    public function countTransactions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTransactionsPartial && !$this->isNew();
        if (null === $this->collTransactions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTransactions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTransactions());
            }
            $query = TransactionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collTransactions);
    }

    /**
     * Method called to associate a Transaction object to this object
     * through the Transaction foreign key attribute.
     *
     * @param    Transaction $l Transaction
     * @return User The current object (for fluent API support)
     */
    public function addTransaction(Transaction $l)
    {
        if ($this->collTransactions === null) {
            $this->initTransactions();
            $this->collTransactionsPartial = true;
        }

        if (!in_array($l, $this->collTransactions->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTransaction($l);

            if ($this->transactionsScheduledForDeletion and $this->transactionsScheduledForDeletion->contains($l)) {
                $this->transactionsScheduledForDeletion->remove($this->transactionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Transaction $transaction The transaction object to add.
     */
    protected function doAddTransaction($transaction)
    {
        $this->collTransactions[]= $transaction;
        $transaction->setUser($this);
    }

    /**
     * @param  Transaction $transaction The transaction object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeTransaction($transaction)
    {
        if ($this->getTransactions()->contains($transaction)) {
            $this->collTransactions->remove($this->collTransactions->search($transaction));
            if (null === $this->transactionsScheduledForDeletion) {
                $this->transactionsScheduledForDeletion = clone $this->collTransactions;
                $this->transactionsScheduledForDeletion->clear();
            }
            $this->transactionsScheduledForDeletion[]= clone $transaction;
            $transaction->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinCategory($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Category', $join_behavior);

        return $this->getTransactions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('Account', $join_behavior);

        return $this->getTransactions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinTargetAccount($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('TargetAccount', $join_behavior);

        return $this->getTransactions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinCounterTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterTransaction', $join_behavior);

        return $this->getTransactions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinCounterParty($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('CounterParty', $join_behavior);

        return $this->getTransactions($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Transactions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Transaction[] List of Transaction objects
     */
    public function getTransactionsJoinParentTransaction($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TransactionQuery::create(null, $criteria);
        $query->joinWith('ParentTransaction', $join_behavior);

        return $this->getTransactions($query, $con);
    }

    /**
     * Clears out the collCategorys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addCategorys()
     */
    public function clearCategorys()
    {
        $this->collCategorys = null; // important to set this to null since that means it is uninitialized
        $this->collCategorysPartial = null;

        return $this;
    }

    /**
     * reset is the collCategorys collection loaded partially
     *
     * @return void
     */
    public function resetPartialCategorys($v = true)
    {
        $this->collCategorysPartial = $v;
    }

    /**
     * Initializes the collCategorys collection.
     *
     * By default this just sets the collCategorys collection to an empty array (like clearcollCategorys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategorys($overrideExisting = true)
    {
        if (null !== $this->collCategorys && !$overrideExisting) {
            return;
        }
        $this->collCategorys = new PropelObjectCollection();
        $this->collCategorys->setModel('TransactionCategory');
    }

    /**
     * Gets an array of TransactionCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|TransactionCategory[] List of TransactionCategory objects
     * @throws PropelException
     */
    public function getCategorys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCategorysPartial && !$this->isNew();
        if (null === $this->collCategorys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategorys) {
                // return empty collection
                $this->initCategorys();
            } else {
                $collCategorys = TransactionCategoryQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCategorysPartial && count($collCategorys)) {
                      $this->initCategorys(false);

                      foreach ($collCategorys as $obj) {
                        if (false == $this->collCategorys->contains($obj)) {
                          $this->collCategorys->append($obj);
                        }
                      }

                      $this->collCategorysPartial = true;
                    }

                    $collCategorys->getInternalIterator()->rewind();

                    return $collCategorys;
                }

                if ($partial && $this->collCategorys) {
                    foreach ($this->collCategorys as $obj) {
                        if ($obj->isNew()) {
                            $collCategorys[] = $obj;
                        }
                    }
                }

                $this->collCategorys = $collCategorys;
                $this->collCategorysPartial = false;
            }
        }

        return $this->collCategorys;
    }

    /**
     * Sets a collection of Category objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $categorys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setCategorys(PropelCollection $categorys, PropelPDO $con = null)
    {
        $categorysToDelete = $this->getCategorys(new Criteria(), $con)->diff($categorys);


        $this->categorysScheduledForDeletion = $categorysToDelete;

        foreach ($categorysToDelete as $categoryRemoved) {
            $categoryRemoved->setUser(null);
        }

        $this->collCategorys = null;
        foreach ($categorys as $category) {
            $this->addCategory($category);
        }

        $this->collCategorys = $categorys;
        $this->collCategorysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TransactionCategory objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related TransactionCategory objects.
     * @throws PropelException
     */
    public function countCategorys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCategorysPartial && !$this->isNew();
        if (null === $this->collCategorys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategorys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategorys());
            }
            $query = TransactionCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collCategorys);
    }

    /**
     * Method called to associate a TransactionCategory object to this object
     * through the TransactionCategory foreign key attribute.
     *
     * @param    TransactionCategory $l TransactionCategory
     * @return User The current object (for fluent API support)
     */
    public function addCategory(TransactionCategory $l)
    {
        if ($this->collCategorys === null) {
            $this->initCategorys();
            $this->collCategorysPartial = true;
        }

        if (!in_array($l, $this->collCategorys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategory($l);

            if ($this->categorysScheduledForDeletion and $this->categorysScheduledForDeletion->contains($l)) {
                $this->categorysScheduledForDeletion->remove($this->categorysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Category $category The category object to add.
     */
    protected function doAddCategory($category)
    {
        $this->collCategorys[]= $category;
        $category->setUser($this);
    }

    /**
     * @param  Category $category The category object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeCategory($category)
    {
        if ($this->getCategorys()->contains($category)) {
            $this->collCategorys->remove($this->collCategorys->search($category));
            if (null === $this->categorysScheduledForDeletion) {
                $this->categorysScheduledForDeletion = clone $this->collCategorys;
                $this->categorysScheduledForDeletion->clear();
            }
            $this->categorysScheduledForDeletion[]= clone $category;
            $category->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addTags()
     */
    public function clearTags()
    {
        $this->collTags = null; // important to set this to null since that means it is uninitialized
        $this->collTagsPartial = null;

        return $this;
    }

    /**
     * reset is the collTags collection loaded partially
     *
     * @return void
     */
    public function resetPartialTags($v = true)
    {
        $this->collTagsPartial = $v;
    }

    /**
     * Initializes the collTags collection.
     *
     * By default this just sets the collTags collection to an empty array (like clearcollTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTags($overrideExisting = true)
    {
        if (null !== $this->collTags && !$overrideExisting) {
            return;
        }
        $this->collTags = new PropelObjectCollection();
        $this->collTags->setModel('TransactionTag');
    }

    /**
     * Gets an array of TransactionTag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|TransactionTag[] List of TransactionTag objects
     * @throws PropelException
     */
    public function getTags($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTagsPartial && !$this->isNew();
        if (null === $this->collTags || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTags) {
                // return empty collection
                $this->initTags();
            } else {
                $collTags = TransactionTagQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTagsPartial && count($collTags)) {
                      $this->initTags(false);

                      foreach ($collTags as $obj) {
                        if (false == $this->collTags->contains($obj)) {
                          $this->collTags->append($obj);
                        }
                      }

                      $this->collTagsPartial = true;
                    }

                    $collTags->getInternalIterator()->rewind();

                    return $collTags;
                }

                if ($partial && $this->collTags) {
                    foreach ($this->collTags as $obj) {
                        if ($obj->isNew()) {
                            $collTags[] = $obj;
                        }
                    }
                }

                $this->collTags = $collTags;
                $this->collTagsPartial = false;
            }
        }

        return $this->collTags;
    }

    /**
     * Sets a collection of Tag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $tags A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setTags(PropelCollection $tags, PropelPDO $con = null)
    {
        $tagsToDelete = $this->getTags(new Criteria(), $con)->diff($tags);


        $this->tagsScheduledForDeletion = $tagsToDelete;

        foreach ($tagsToDelete as $tagRemoved) {
            $tagRemoved->setUser(null);
        }

        $this->collTags = null;
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        $this->collTags = $tags;
        $this->collTagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related TransactionTag objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related TransactionTag objects.
     * @throws PropelException
     */
    public function countTags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTagsPartial && !$this->isNew();
        if (null === $this->collTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTags());
            }
            $query = TransactionTagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collTags);
    }

    /**
     * Method called to associate a TransactionTag object to this object
     * through the TransactionTag foreign key attribute.
     *
     * @param    TransactionTag $l TransactionTag
     * @return User The current object (for fluent API support)
     */
    public function addTag(TransactionTag $l)
    {
        if ($this->collTags === null) {
            $this->initTags();
            $this->collTagsPartial = true;
        }

        if (!in_array($l, $this->collTags->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTag($l);

            if ($this->tagsScheduledForDeletion and $this->tagsScheduledForDeletion->contains($l)) {
                $this->tagsScheduledForDeletion->remove($this->tagsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  Tag $tag The tag object to add.
     */
    protected function doAddTag($tag)
    {
        $this->collTags[]= $tag;
        $tag->setUser($this);
    }

    /**
     * @param  Tag $tag The tag object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeTag($tag)
    {
        if ($this->getTags()->contains($tag)) {
            $this->collTags->remove($this->collTags->search($tag));
            if (null === $this->tagsScheduledForDeletion) {
                $this->tagsScheduledForDeletion = clone $this->collTags;
                $this->tagsScheduledForDeletion->clear();
            }
            $this->tagsScheduledForDeletion[]= clone $tag;
            $tag->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collCounterPartys collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addCounterPartys()
     */
    public function clearCounterPartys()
    {
        $this->collCounterPartys = null; // important to set this to null since that means it is uninitialized
        $this->collCounterPartysPartial = null;

        return $this;
    }

    /**
     * reset is the collCounterPartys collection loaded partially
     *
     * @return void
     */
    public function resetPartialCounterPartys($v = true)
    {
        $this->collCounterPartysPartial = $v;
    }

    /**
     * Initializes the collCounterPartys collection.
     *
     * By default this just sets the collCounterPartys collection to an empty array (like clearcollCounterPartys());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCounterPartys($overrideExisting = true)
    {
        if (null !== $this->collCounterPartys && !$overrideExisting) {
            return;
        }
        $this->collCounterPartys = new PropelObjectCollection();
        $this->collCounterPartys->setModel('CounterParty');
    }

    /**
     * Gets an array of CounterParty objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CounterParty[] List of CounterParty objects
     * @throws PropelException
     */
    public function getCounterPartys($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCounterPartysPartial && !$this->isNew();
        if (null === $this->collCounterPartys || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCounterPartys) {
                // return empty collection
                $this->initCounterPartys();
            } else {
                $collCounterPartys = CounterPartyQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCounterPartysPartial && count($collCounterPartys)) {
                      $this->initCounterPartys(false);

                      foreach ($collCounterPartys as $obj) {
                        if (false == $this->collCounterPartys->contains($obj)) {
                          $this->collCounterPartys->append($obj);
                        }
                      }

                      $this->collCounterPartysPartial = true;
                    }

                    $collCounterPartys->getInternalIterator()->rewind();

                    return $collCounterPartys;
                }

                if ($partial && $this->collCounterPartys) {
                    foreach ($this->collCounterPartys as $obj) {
                        if ($obj->isNew()) {
                            $collCounterPartys[] = $obj;
                        }
                    }
                }

                $this->collCounterPartys = $collCounterPartys;
                $this->collCounterPartysPartial = false;
            }
        }

        return $this->collCounterPartys;
    }

    /**
     * Sets a collection of CounterParty objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $counterPartys A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setCounterPartys(PropelCollection $counterPartys, PropelPDO $con = null)
    {
        $counterPartysToDelete = $this->getCounterPartys(new Criteria(), $con)->diff($counterPartys);


        $this->counterPartysScheduledForDeletion = $counterPartysToDelete;

        foreach ($counterPartysToDelete as $counterPartyRemoved) {
            $counterPartyRemoved->setUser(null);
        }

        $this->collCounterPartys = null;
        foreach ($counterPartys as $counterParty) {
            $this->addCounterParty($counterParty);
        }

        $this->collCounterPartys = $counterPartys;
        $this->collCounterPartysPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CounterParty objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CounterParty objects.
     * @throws PropelException
     */
    public function countCounterPartys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCounterPartysPartial && !$this->isNew();
        if (null === $this->collCounterPartys || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCounterPartys) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCounterPartys());
            }
            $query = CounterPartyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collCounterPartys);
    }

    /**
     * Method called to associate a CounterParty object to this object
     * through the CounterParty foreign key attribute.
     *
     * @param    CounterParty $l CounterParty
     * @return User The current object (for fluent API support)
     */
    public function addCounterParty(CounterParty $l)
    {
        if ($this->collCounterPartys === null) {
            $this->initCounterPartys();
            $this->collCounterPartysPartial = true;
        }

        if (!in_array($l, $this->collCounterPartys->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCounterParty($l);

            if ($this->counterPartysScheduledForDeletion and $this->counterPartysScheduledForDeletion->contains($l)) {
                $this->counterPartysScheduledForDeletion->remove($this->counterPartysScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  CounterParty $counterParty The counterParty object to add.
     */
    protected function doAddCounterParty($counterParty)
    {
        $this->collCounterPartys[]= $counterParty;
        $counterParty->setUser($this);
    }

    /**
     * @param  CounterParty $counterParty The counterParty object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeCounterParty($counterParty)
    {
        if ($this->getCounterPartys()->contains($counterParty)) {
            $this->collCounterPartys->remove($this->collCounterPartys->search($counterParty));
            if (null === $this->counterPartysScheduledForDeletion) {
                $this->counterPartysScheduledForDeletion = clone $this->collCounterPartys;
                $this->counterPartysScheduledForDeletion->clear();
            }
            $this->counterPartysScheduledForDeletion[]= clone $counterParty;
            $counterParty->setUser(null);
        }

        return $this;
    }

    /**
     * Clears out the collBudgets collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
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
     * If this User is new, it will return
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
                    ->filterByUser($this)
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
     * @return User The current object (for fluent API support)
     */
    public function setBudgets(PropelCollection $budgets, PropelPDO $con = null)
    {
        $budgetsToDelete = $this->getBudgets(new Criteria(), $con)->diff($budgets);


        $this->budgetsScheduledForDeletion = $budgetsToDelete;

        foreach ($budgetsToDelete as $budgetRemoved) {
            $budgetRemoved->setUser(null);
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
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collBudgets);
    }

    /**
     * Method called to associate a Budget object to this object
     * through the Budget foreign key attribute.
     *
     * @param    Budget $l Budget
     * @return User The current object (for fluent API support)
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
        $budget->setUser($this);
    }

    /**
     * @param  Budget $budget The budget object to remove.
     * @return User The current object (for fluent API support)
     */
    public function removeBudget($budget)
    {
        if ($this->getBudgets()->contains($budget)) {
            $this->collBudgets->remove($this->collBudgets->search($budget));
            if (null === $this->budgetsScheduledForDeletion) {
                $this->budgetsScheduledForDeletion = clone $this->collBudgets;
                $this->budgetsScheduledForDeletion->clear();
            }
            $this->budgetsScheduledForDeletion[]= clone $budget;
            $budget->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related Budgets from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Budget[] List of Budget objects
     */
    public function getBudgetsJoinCurrency($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = BudgetQuery::create(null, $criteria);
        $query->joinWith('Currency', $join_behavior);

        return $this->getBudgets($query, $con);
    }

    /**
     * Clears out the collGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return User The current object (for fluent API support)
     * @see        addGroups()
     */
    public function clearGroups()
    {
        $this->collGroups = null; // important to set this to null since that means it is uninitialized
        $this->collGroupsPartial = null;

        return $this;
    }

    /**
     * Initializes the collGroups collection.
     *
     * By default this just sets the collGroups collection to an empty collection (like clearGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initGroups()
    {
        $this->collGroups = new PropelObjectCollection();
        $this->collGroups->setModel('Group');
    }

    /**
     * Gets a collection of Group objects related by a many-to-many relationship
     * to the current object by way of the ref_users_groups cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Group[] List of Group objects
     */
    public function getGroups($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collGroups) {
                // return empty collection
                $this->initGroups();
            } else {
                $collGroups = GroupQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collGroups;
                }
                $this->collGroups = $collGroups;
            }
        }

        return $this->collGroups;
    }

    /**
     * Sets a collection of Group objects related by a many-to-many relationship
     * to the current object by way of the ref_users_groups cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $groups A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return User The current object (for fluent API support)
     */
    public function setGroups(PropelCollection $groups, PropelPDO $con = null)
    {
        $this->clearGroups();
        $currentGroups = $this->getGroups(null, $con);

        $this->groupsScheduledForDeletion = $currentGroups->diff($groups);

        foreach ($groups as $group) {
            if (!$currentGroups->contains($group)) {
                $this->doAddGroup($group);
            }
        }

        $this->collGroups = $groups;

        return $this;
    }

    /**
     * Gets the number of Group objects related by a many-to-many relationship
     * to the current object by way of the ref_users_groups cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Group objects
     */
    public function countGroups($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collGroups) {
                return 0;
            } else {
                $query = GroupQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collGroups);
        }
    }

    /**
     * Associate a Group object to this object
     * through the ref_users_groups cross reference table.
     *
     * @param  Group $group The RefUserGroup object to relate
     * @return User The current object (for fluent API support)
     */
    public function addGroup(Group $group)
    {
        if ($this->collGroups === null) {
            $this->initGroups();
        }

        if (!$this->collGroups->contains($group)) { // only add it if the **same** object is not already associated
            $this->doAddGroup($group);
            $this->collGroups[] = $group;

            if ($this->groupsScheduledForDeletion and $this->groupsScheduledForDeletion->contains($group)) {
                $this->groupsScheduledForDeletion->remove($this->groupsScheduledForDeletion->search($group));
            }
        }

        return $this;
    }

    /**
     * @param  Group $group The group object to add.
     */
    protected function doAddGroup(Group $group)
    {
        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$group->getUsers()->contains($this)) { $refUserGroup = new RefUserGroup();
            $refUserGroup->setGroup($group);
            $this->addRefGroup($refUserGroup);

            $foreignCollection = $group->getUsers();
            $foreignCollection[] = $this;
        }
    }

    /**
     * Remove a Group object to this object
     * through the ref_users_groups cross reference table.
     *
     * @param Group $group The RefUserGroup object to relate
     * @return User The current object (for fluent API support)
     */
    public function removeGroup(Group $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->collGroups->remove($this->collGroups->search($group));
            if (null === $this->groupsScheduledForDeletion) {
                $this->groupsScheduledForDeletion = clone $this->collGroups;
                $this->groupsScheduledForDeletion->clear();
            }
            $this->groupsScheduledForDeletion[]= $group;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->email = null;
        $this->algorithm = null;
        $this->salt = null;
        $this->password = null;
        $this->locked = null;
        $this->is_superuser = null;
        $this->last_login = null;
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
            if ($this->collFeedbacks) {
                foreach ($this->collFeedbacks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleUserProfile) {
                $this->singleUserProfile->clearAllReferences($deep);
            }
            if ($this->collKeys) {
                foreach ($this->collKeys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSocialAccounts) {
                foreach ($this->collSocialAccounts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPolicys) {
                foreach ($this->collPolicys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collRefGroups) {
                foreach ($this->collRefGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotifications) {
                foreach ($this->collNotifications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSettings) {
                foreach ($this->collSettings as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAccounts) {
                foreach ($this->collAccounts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTransactions) {
                foreach ($this->collTransactions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategorys) {
                foreach ($this->collCategorys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTags) {
                foreach ($this->collTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCounterPartys) {
                foreach ($this->collCounterPartys as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collBudgets) {
                foreach ($this->collBudgets as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGroups) {
                foreach ($this->collGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collFeedbacks instanceof PropelCollection) {
            $this->collFeedbacks->clearIterator();
        }
        $this->collFeedbacks = null;
        if ($this->singleUserProfile instanceof PropelCollection) {
            $this->singleUserProfile->clearIterator();
        }
        $this->singleUserProfile = null;
        if ($this->collKeys instanceof PropelCollection) {
            $this->collKeys->clearIterator();
        }
        $this->collKeys = null;
        if ($this->collSocialAccounts instanceof PropelCollection) {
            $this->collSocialAccounts->clearIterator();
        }
        $this->collSocialAccounts = null;
        if ($this->collPolicys instanceof PropelCollection) {
            $this->collPolicys->clearIterator();
        }
        $this->collPolicys = null;
        if ($this->collRefGroups instanceof PropelCollection) {
            $this->collRefGroups->clearIterator();
        }
        $this->collRefGroups = null;
        if ($this->collNotifications instanceof PropelCollection) {
            $this->collNotifications->clearIterator();
        }
        $this->collNotifications = null;
        if ($this->collSettings instanceof PropelCollection) {
            $this->collSettings->clearIterator();
        }
        $this->collSettings = null;
        if ($this->collAccounts instanceof PropelCollection) {
            $this->collAccounts->clearIterator();
        }
        $this->collAccounts = null;
        if ($this->collTransactions instanceof PropelCollection) {
            $this->collTransactions->clearIterator();
        }
        $this->collTransactions = null;
        if ($this->collCategorys instanceof PropelCollection) {
            $this->collCategorys->clearIterator();
        }
        $this->collCategorys = null;
        if ($this->collTags instanceof PropelCollection) {
            $this->collTags->clearIterator();
        }
        $this->collTags = null;
        if ($this->collCounterPartys instanceof PropelCollection) {
            $this->collCounterPartys->clearIterator();
        }
        $this->collCounterPartys = null;
        if ($this->collBudgets instanceof PropelCollection) {
            $this->collBudgets->clearIterator();
        }
        $this->collBudgets = null;
        if ($this->collGroups instanceof PropelCollection) {
            $this->collGroups->clearIterator();
        }
        $this->collGroups = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string The value of the 'email' column
     */
    public function __toString()
    {
        return (string) $this->getEmail();
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
   * @return     User The current object (for fluent API support)
   */
  public function keepUpdateDateUnchanged()
  {
      $this->modifiedColumns[] = UserPeer::UPDATED_AT;

      return $this;
  }

}

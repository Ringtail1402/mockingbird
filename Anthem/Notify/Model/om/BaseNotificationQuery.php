<?php

namespace Anthem\Notify\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Anthem\Auth\Model\User;
use Anthem\Notify\Model\Notification;
use Anthem\Notify\Model\NotificationPeer;
use Anthem\Notify\Model\NotificationQuery;

/**
 * Base class that represents a query for the 'notifications' table.
 *
 *
 *
 * @method NotificationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method NotificationQuery orderByUniqid($order = Criteria::ASC) Order by the uniqid column
 * @method NotificationQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method NotificationQuery orderByOutputClass($order = Criteria::ASC) Order by the output_class column
 * @method NotificationQuery orderByNoDismiss($order = Criteria::ASC) Order by the no_dismiss column
 * @method NotificationQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method NotificationQuery orderByPolicies($order = Criteria::ASC) Order by the policies column
 * @method NotificationQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method NotificationQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method NotificationQuery groupById() Group by the id column
 * @method NotificationQuery groupByUniqid() Group by the uniqid column
 * @method NotificationQuery groupByMessage() Group by the message column
 * @method NotificationQuery groupByOutputClass() Group by the output_class column
 * @method NotificationQuery groupByNoDismiss() Group by the no_dismiss column
 * @method NotificationQuery groupByUserId() Group by the user_id column
 * @method NotificationQuery groupByPolicies() Group by the policies column
 * @method NotificationQuery groupByCreatedAt() Group by the created_at column
 * @method NotificationQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method NotificationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method NotificationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method NotificationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method NotificationQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method NotificationQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method NotificationQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method Notification findOne(PropelPDO $con = null) Return the first Notification matching the query
 * @method Notification findOneOrCreate(PropelPDO $con = null) Return the first Notification matching the query, or a new Notification object populated from the query conditions when no match is found
 *
 * @method Notification findOneByUniqid(string $uniqid) Return the first Notification filtered by the uniqid column
 * @method Notification findOneByMessage(string $message) Return the first Notification filtered by the message column
 * @method Notification findOneByOutputClass(string $output_class) Return the first Notification filtered by the output_class column
 * @method Notification findOneByNoDismiss(boolean $no_dismiss) Return the first Notification filtered by the no_dismiss column
 * @method Notification findOneByUserId(int $user_id) Return the first Notification filtered by the user_id column
 * @method Notification findOneByPolicies(string $policies) Return the first Notification filtered by the policies column
 * @method Notification findOneByCreatedAt(string $created_at) Return the first Notification filtered by the created_at column
 * @method Notification findOneByUpdatedAt(string $updated_at) Return the first Notification filtered by the updated_at column
 *
 * @method array findById(int $id) Return Notification objects filtered by the id column
 * @method array findByUniqid(string $uniqid) Return Notification objects filtered by the uniqid column
 * @method array findByMessage(string $message) Return Notification objects filtered by the message column
 * @method array findByOutputClass(string $output_class) Return Notification objects filtered by the output_class column
 * @method array findByNoDismiss(boolean $no_dismiss) Return Notification objects filtered by the no_dismiss column
 * @method array findByUserId(int $user_id) Return Notification objects filtered by the user_id column
 * @method array findByPolicies(string $policies) Return Notification objects filtered by the policies column
 * @method array findByCreatedAt(string $created_at) Return Notification objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Notification objects filtered by the updated_at column
 *
 * @package    propel.generator.Anthem.Notify.Model.om
 */
abstract class BaseNotificationQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseNotificationQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Anthem\\Notify\\Model\\Notification';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new NotificationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   NotificationQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return NotificationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof NotificationQuery) {
            return $criteria;
        }
        $query = new NotificationQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Notification|Notification[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NotificationPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(NotificationPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Notification A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Notification A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `uniqid`, `message`, `output_class`, `no_dismiss`, `user_id`, `policies`, `created_at`, `updated_at` FROM `notifications` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
      $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Notification();
            $obj->hydrate($row);
            NotificationPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Notification|Notification[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Notification[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NotificationPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NotificationPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NotificationPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NotificationPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the uniqid column
     *
     * Example usage:
     * <code>
     * $query->filterByUniqid('fooValue');   // WHERE uniqid = 'fooValue'
     * $query->filterByUniqid('%fooValue%'); // WHERE uniqid LIKE '%fooValue%'
     * </code>
     *
     * @param     string $uniqid The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByUniqid($uniqid = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($uniqid)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $uniqid)) {
                $uniqid = str_replace('*', '%', $uniqid);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationPeer::UNIQID, $uniqid, $comparison);
    }

    /**
     * Filter the query on the message column
     *
     * Example usage:
     * <code>
     * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
     * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $message The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByMessage($message = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($message)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $message)) {
                $message = str_replace('*', '%', $message);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationPeer::MESSAGE, $message, $comparison);
    }

    /**
     * Filter the query on the output_class column
     *
     * Example usage:
     * <code>
     * $query->filterByOutputClass('fooValue');   // WHERE output_class = 'fooValue'
     * $query->filterByOutputClass('%fooValue%'); // WHERE output_class LIKE '%fooValue%'
     * </code>
     *
     * @param     string $outputClass The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByOutputClass($outputClass = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($outputClass)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $outputClass)) {
                $outputClass = str_replace('*', '%', $outputClass);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationPeer::OUTPUT_CLASS, $outputClass, $comparison);
    }

    /**
     * Filter the query on the no_dismiss column
     *
     * Example usage:
     * <code>
     * $query->filterByNoDismiss(true); // WHERE no_dismiss = true
     * $query->filterByNoDismiss('yes'); // WHERE no_dismiss = true
     * </code>
     *
     * @param     boolean|string $noDismiss The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByNoDismiss($noDismiss = null, $comparison = null)
    {
        if (is_string($noDismiss)) {
            $noDismiss = in_array(strtolower($noDismiss), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(NotificationPeer::NO_DISMISS, $noDismiss, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id >= 12
     * $query->filterByUserId(array('max' => 12)); // WHERE user_id <= 12
     * </code>
     *
     * @see       filterByUser()
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(NotificationPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(NotificationPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the policies column
     *
     * Example usage:
     * <code>
     * $query->filterByPolicies('fooValue');   // WHERE policies = 'fooValue'
     * $query->filterByPolicies('%fooValue%'); // WHERE policies LIKE '%fooValue%'
     * </code>
     *
     * @param     string $policies The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByPolicies($policies = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($policies)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $policies)) {
                $policies = str_replace('*', '%', $policies);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationPeer::POLICIES, $policies, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(NotificationPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(NotificationPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationPeer::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at < '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(NotificationPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(NotificationPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 NotificationQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(NotificationPeer::USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NotificationPeer::USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUser() only accepts arguments of type User or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the User relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('User');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'User');
        }

        return $this;
    }

    /**
     * Use the User relation User object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Anthem\Auth\Model\UserQuery A secondary query class using the current class as primary query
     */
    public function useUserQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\Anthem\Auth\Model\UserQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Notification $notification Object to remove from the list of results
     *
     * @return NotificationQuery The current query, for fluid interface
     */
    public function prune($notification = null)
    {
        if ($notification) {
            $this->addUsingAlias(NotificationPeer::ID, $notification->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

  // timestampable behavior

  /**
   * Filter by the latest updated
   *
   * @param      int $nbDays Maximum age of the latest update in days
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function recentlyUpdated($nbDays = 7)
  {
      return $this->addUsingAlias(NotificationPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
  }

  /**
   * Order by update date desc
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function lastUpdatedFirst()
  {
      return $this->addDescendingOrderByColumn(NotificationPeer::UPDATED_AT);
  }

  /**
   * Order by update date asc
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function firstUpdatedFirst()
  {
      return $this->addAscendingOrderByColumn(NotificationPeer::UPDATED_AT);
  }

  /**
   * Filter by the latest created
   *
   * @param      int $nbDays Maximum age of in days
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function recentlyCreated($nbDays = 7)
  {
      return $this->addUsingAlias(NotificationPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
  }

  /**
   * Order by create date desc
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function lastCreatedFirst()
  {
      return $this->addDescendingOrderByColumn(NotificationPeer::CREATED_AT);
  }

  /**
   * Order by create date asc
   *
   * @return     NotificationQuery The current query, for fluid interface
   */
  public function firstCreatedFirst()
  {
      return $this->addAscendingOrderByColumn(NotificationPeer::CREATED_AT);
  }
}

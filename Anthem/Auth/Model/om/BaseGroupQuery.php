<?php

namespace Anthem\Auth\Model\om;

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
use Anthem\Auth\Model\Group;
use Anthem\Auth\Model\GroupPeer;
use Anthem\Auth\Model\GroupPolicy;
use Anthem\Auth\Model\GroupQuery;
use Anthem\Auth\Model\RefUserGroup;
use Anthem\Auth\Model\User;

/**
 * Base class that represents a query for the 'groups' table.
 *
 *
 *
 * @method GroupQuery orderById($order = Criteria::ASC) Order by the id column
 * @method GroupQuery orderByTitle($order = Criteria::ASC) Order by the title column
 *
 * @method GroupQuery groupById() Group by the id column
 * @method GroupQuery groupByTitle() Group by the title column
 *
 * @method GroupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GroupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GroupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GroupQuery leftJoinPolicy($relationAlias = null) Adds a LEFT JOIN clause to the query using the Policy relation
 * @method GroupQuery rightJoinPolicy($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Policy relation
 * @method GroupQuery innerJoinPolicy($relationAlias = null) Adds a INNER JOIN clause to the query using the Policy relation
 *
 * @method GroupQuery leftJoinRefGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the RefGroup relation
 * @method GroupQuery rightJoinRefGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RefGroup relation
 * @method GroupQuery innerJoinRefGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the RefGroup relation
 *
 * @method Group findOne(PropelPDO $con = null) Return the first Group matching the query
 * @method Group findOneOrCreate(PropelPDO $con = null) Return the first Group matching the query, or a new Group object populated from the query conditions when no match is found
 *
 * @method Group findOneByTitle(string $title) Return the first Group filtered by the title column
 *
 * @method array findById(int $id) Return Group objects filtered by the id column
 * @method array findByTitle(string $title) Return Group objects filtered by the title column
 *
 * @package    propel.generator.Anthem.Auth.Model.om
 */
abstract class BaseGroupQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseGroupQuery object.
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
            $modelName = 'Anthem\\Auth\\Model\\Group';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new GroupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   GroupQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GroupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GroupQuery) {
            return $criteria;
        }
        $query = new GroupQuery(null, null, $modelAlias);

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
     * @return   Group|Group[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GroupPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GroupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Group A model object, or null if the key is not found
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
     * @return                 Group A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `title` FROM `groups` WHERE `id` = :p0';
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
            $obj = new Group();
            $obj->hydrate($row);
            GroupPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Group|Group[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Group[]|mixed the list of results, formatted by the current formatter
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
     * @return GroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GroupPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GroupPeer::ID, $keys, Criteria::IN);
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
     * @return GroupQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(GroupPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(GroupPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GroupPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GroupQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GroupPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query by a related GroupPolicy object
     *
     * @param   GroupPolicy|PropelObjectCollection $groupPolicy  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GroupQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPolicy($groupPolicy, $comparison = null)
    {
        if ($groupPolicy instanceof GroupPolicy) {
            return $this
                ->addUsingAlias(GroupPeer::ID, $groupPolicy->getGroupId(), $comparison);
        } elseif ($groupPolicy instanceof PropelObjectCollection) {
            return $this
                ->usePolicyQuery()
                ->filterByPrimaryKeys($groupPolicy->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPolicy() only accepts arguments of type GroupPolicy or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Policy relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GroupQuery The current query, for fluid interface
     */
    public function joinPolicy($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Policy');

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
            $this->addJoinObject($join, 'Policy');
        }

        return $this;
    }

    /**
     * Use the Policy relation GroupPolicy object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Anthem\Auth\Model\GroupPolicyQuery A secondary query class using the current class as primary query
     */
    public function usePolicyQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPolicy($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Policy', '\Anthem\Auth\Model\GroupPolicyQuery');
    }

    /**
     * Filter the query by a related RefUserGroup object
     *
     * @param   RefUserGroup|PropelObjectCollection $refUserGroup  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GroupQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRefGroup($refUserGroup, $comparison = null)
    {
        if ($refUserGroup instanceof RefUserGroup) {
            return $this
                ->addUsingAlias(GroupPeer::ID, $refUserGroup->getGroupId(), $comparison);
        } elseif ($refUserGroup instanceof PropelObjectCollection) {
            return $this
                ->useRefGroupQuery()
                ->filterByPrimaryKeys($refUserGroup->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRefGroup() only accepts arguments of type RefUserGroup or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RefGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GroupQuery The current query, for fluid interface
     */
    public function joinRefGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RefGroup');

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
            $this->addJoinObject($join, 'RefGroup');
        }

        return $this;
    }

    /**
     * Use the RefGroup relation RefUserGroup object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Anthem\Auth\Model\RefUserGroupQuery A secondary query class using the current class as primary query
     */
    public function useRefGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRefGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RefGroup', '\Anthem\Auth\Model\RefUserGroupQuery');
    }

    /**
     * Filter the query by a related User object
     * using the ref_users_groups table as cross reference
     *
     * @param   User $user the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   GroupQuery The current query, for fluid interface
     */
    public function filterByUser($user, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useRefGroupQuery()
            ->filterByUser($user, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Group $group Object to remove from the list of results
     *
     * @return GroupQuery The current query, for fluid interface
     */
    public function prune($group = null)
    {
        if ($group) {
            $this->addUsingAlias(GroupPeer::ID, $group->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

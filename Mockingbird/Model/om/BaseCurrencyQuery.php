<?php

namespace Mockingbird\Model\om;

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
use Mockingbird\Model\Account;
use Mockingbird\Model\Budget;
use Mockingbird\Model\Currency;
use Mockingbird\Model\CurrencyPeer;
use Mockingbird\Model\CurrencyQuery;

/**
 * Base class that represents a query for the 'currencies' table.
 *
 *
 *
 * @method CurrencyQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CurrencyQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method CurrencyQuery orderByFormat($order = Criteria::ASC) Order by the format column
 * @method CurrencyQuery orderByIsPrimary($order = Criteria::ASC) Order by the is_primary column
 * @method CurrencyQuery orderByRateToPrimary($order = Criteria::ASC) Order by the rate_to_primary column
 *
 * @method CurrencyQuery groupById() Group by the id column
 * @method CurrencyQuery groupByTitle() Group by the title column
 * @method CurrencyQuery groupByFormat() Group by the format column
 * @method CurrencyQuery groupByIsPrimary() Group by the is_primary column
 * @method CurrencyQuery groupByRateToPrimary() Group by the rate_to_primary column
 *
 * @method CurrencyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CurrencyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CurrencyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CurrencyQuery leftJoinAccounts($relationAlias = null) Adds a LEFT JOIN clause to the query using the Accounts relation
 * @method CurrencyQuery rightJoinAccounts($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Accounts relation
 * @method CurrencyQuery innerJoinAccounts($relationAlias = null) Adds a INNER JOIN clause to the query using the Accounts relation
 *
 * @method CurrencyQuery leftJoinBudget($relationAlias = null) Adds a LEFT JOIN clause to the query using the Budget relation
 * @method CurrencyQuery rightJoinBudget($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Budget relation
 * @method CurrencyQuery innerJoinBudget($relationAlias = null) Adds a INNER JOIN clause to the query using the Budget relation
 *
 * @method Currency findOne(PropelPDO $con = null) Return the first Currency matching the query
 * @method Currency findOneOrCreate(PropelPDO $con = null) Return the first Currency matching the query, or a new Currency object populated from the query conditions when no match is found
 *
 * @method Currency findOneByTitle(string $title) Return the first Currency filtered by the title column
 * @method Currency findOneByFormat(string $format) Return the first Currency filtered by the format column
 * @method Currency findOneByIsPrimary(boolean $is_primary) Return the first Currency filtered by the is_primary column
 * @method Currency findOneByRateToPrimary(double $rate_to_primary) Return the first Currency filtered by the rate_to_primary column
 *
 * @method array findById(int $id) Return Currency objects filtered by the id column
 * @method array findByTitle(string $title) Return Currency objects filtered by the title column
 * @method array findByFormat(string $format) Return Currency objects filtered by the format column
 * @method array findByIsPrimary(boolean $is_primary) Return Currency objects filtered by the is_primary column
 * @method array findByRateToPrimary(double $rate_to_primary) Return Currency objects filtered by the rate_to_primary column
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseCurrencyQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCurrencyQuery object.
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
            $modelName = 'Mockingbird\\Model\\Currency';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CurrencyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CurrencyQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CurrencyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CurrencyQuery) {
            return $criteria;
        }
        $query = new CurrencyQuery(null, null, $modelAlias);

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
     * @return   Currency|Currency[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CurrencyPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CurrencyPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Currency A model object, or null if the key is not found
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
     * @return                 Currency A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `title`, `format`, `is_primary`, `rate_to_primary` FROM `currencies` WHERE `id` = :p0';
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
            $obj = new Currency();
            $obj->hydrate($row);
            CurrencyPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Currency|Currency[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Currency[]|mixed the list of results, formatted by the current formatter
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
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CurrencyPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CurrencyPeer::ID, $keys, Criteria::IN);
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
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CurrencyPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CurrencyPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyPeer::ID, $id, $comparison);
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
     * @return CurrencyQuery The current query, for fluid interface
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

        return $this->addUsingAlias(CurrencyPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the format column
     *
     * Example usage:
     * <code>
     * $query->filterByFormat('fooValue');   // WHERE format = 'fooValue'
     * $query->filterByFormat('%fooValue%'); // WHERE format LIKE '%fooValue%'
     * </code>
     *
     * @param     string $format The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterByFormat($format = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($format)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $format)) {
                $format = str_replace('*', '%', $format);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyPeer::FORMAT, $format, $comparison);
    }

    /**
     * Filter the query on the is_primary column
     *
     * Example usage:
     * <code>
     * $query->filterByIsPrimary(true); // WHERE is_primary = true
     * $query->filterByIsPrimary('yes'); // WHERE is_primary = true
     * </code>
     *
     * @param     boolean|string $isPrimary The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterByIsPrimary($isPrimary = null, $comparison = null)
    {
        if (is_string($isPrimary)) {
            $isPrimary = in_array(strtolower($isPrimary), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(CurrencyPeer::IS_PRIMARY, $isPrimary, $comparison);
    }

    /**
     * Filter the query on the rate_to_primary column
     *
     * Example usage:
     * <code>
     * $query->filterByRateToPrimary(1234); // WHERE rate_to_primary = 1234
     * $query->filterByRateToPrimary(array(12, 34)); // WHERE rate_to_primary IN (12, 34)
     * $query->filterByRateToPrimary(array('min' => 12)); // WHERE rate_to_primary >= 12
     * $query->filterByRateToPrimary(array('max' => 12)); // WHERE rate_to_primary <= 12
     * </code>
     *
     * @param     mixed $rateToPrimary The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function filterByRateToPrimary($rateToPrimary = null, $comparison = null)
    {
        if (is_array($rateToPrimary)) {
            $useMinMax = false;
            if (isset($rateToPrimary['min'])) {
                $this->addUsingAlias(CurrencyPeer::RATE_TO_PRIMARY, $rateToPrimary['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rateToPrimary['max'])) {
                $this->addUsingAlias(CurrencyPeer::RATE_TO_PRIMARY, $rateToPrimary['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyPeer::RATE_TO_PRIMARY, $rateToPrimary, $comparison);
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CurrencyQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccounts($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(CurrencyPeer::ID, $account->getCurrencyId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            return $this
                ->useAccountsQuery()
                ->filterByPrimaryKeys($account->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAccounts() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Accounts relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function joinAccounts($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Accounts');

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
            $this->addJoinObject($join, 'Accounts');
        }

        return $this;
    }

    /**
     * Use the Accounts relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccounts($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Accounts', '\Mockingbird\Model\AccountQuery');
    }

    /**
     * Filter the query by a related Budget object
     *
     * @param   Budget|PropelObjectCollection $budget  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CurrencyQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByBudget($budget, $comparison = null)
    {
        if ($budget instanceof Budget) {
            return $this
                ->addUsingAlias(CurrencyPeer::ID, $budget->getCurrencyId(), $comparison);
        } elseif ($budget instanceof PropelObjectCollection) {
            return $this
                ->useBudgetQuery()
                ->filterByPrimaryKeys($budget->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBudget() only accepts arguments of type Budget or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Budget relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function joinBudget($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Budget');

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
            $this->addJoinObject($join, 'Budget');
        }

        return $this;
    }

    /**
     * Use the Budget relation Budget object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\BudgetQuery A secondary query class using the current class as primary query
     */
    public function useBudgetQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinBudget($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Budget', '\Mockingbird\Model\BudgetQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Currency $currency Object to remove from the list of results
     *
     * @return CurrencyQuery The current query, for fluid interface
     */
    public function prune($currency = null)
    {
        if ($currency) {
            $this->addUsingAlias(CurrencyPeer::ID, $currency->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

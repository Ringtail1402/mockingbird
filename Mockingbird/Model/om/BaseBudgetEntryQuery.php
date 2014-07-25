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
use Mockingbird\Model\Budget;
use Mockingbird\Model\BudgetEntry;
use Mockingbird\Model\BudgetEntryPeer;
use Mockingbird\Model\BudgetEntryQuery;
use Mockingbird\Model\TransactionCategory;

/**
 * Base class that represents a query for the 'budget_entries' table.
 *
 *
 *
 * @method BudgetEntryQuery orderById($order = Criteria::ASC) Order by the id column
 * @method BudgetEntryQuery orderByBudgetId($order = Criteria::ASC) Order by the budget_id column
 * @method BudgetEntryQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 * @method BudgetEntryQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method BudgetEntryQuery orderByWhen($order = Criteria::ASC) Order by the when_entry column
 * @method BudgetEntryQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method BudgetEntryQuery groupById() Group by the id column
 * @method BudgetEntryQuery groupByBudgetId() Group by the budget_id column
 * @method BudgetEntryQuery groupByCategoryId() Group by the category_id column
 * @method BudgetEntryQuery groupByAmount() Group by the amount column
 * @method BudgetEntryQuery groupByWhen() Group by the when_entry column
 * @method BudgetEntryQuery groupByDescription() Group by the description column
 *
 * @method BudgetEntryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method BudgetEntryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method BudgetEntryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method BudgetEntryQuery leftJoinBudget($relationAlias = null) Adds a LEFT JOIN clause to the query using the Budget relation
 * @method BudgetEntryQuery rightJoinBudget($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Budget relation
 * @method BudgetEntryQuery innerJoinBudget($relationAlias = null) Adds a INNER JOIN clause to the query using the Budget relation
 *
 * @method BudgetEntryQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method BudgetEntryQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method BudgetEntryQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method BudgetEntry findOne(PropelPDO $con = null) Return the first BudgetEntry matching the query
 * @method BudgetEntry findOneOrCreate(PropelPDO $con = null) Return the first BudgetEntry matching the query, or a new BudgetEntry object populated from the query conditions when no match is found
 *
 * @method BudgetEntry findOneByBudgetId(int $budget_id) Return the first BudgetEntry filtered by the budget_id column
 * @method BudgetEntry findOneByCategoryId(int $category_id) Return the first BudgetEntry filtered by the category_id column
 * @method BudgetEntry findOneByAmount(string $amount) Return the first BudgetEntry filtered by the amount column
 * @method BudgetEntry findOneByWhen(int $when_entry) Return the first BudgetEntry filtered by the when_entry column
 * @method BudgetEntry findOneByDescription(string $description) Return the first BudgetEntry filtered by the description column
 *
 * @method array findById(int $id) Return BudgetEntry objects filtered by the id column
 * @method array findByBudgetId(int $budget_id) Return BudgetEntry objects filtered by the budget_id column
 * @method array findByCategoryId(int $category_id) Return BudgetEntry objects filtered by the category_id column
 * @method array findByAmount(string $amount) Return BudgetEntry objects filtered by the amount column
 * @method array findByWhen(int $when_entry) Return BudgetEntry objects filtered by the when_entry column
 * @method array findByDescription(string $description) Return BudgetEntry objects filtered by the description column
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseBudgetEntryQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseBudgetEntryQuery object.
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
            $modelName = 'Mockingbird\\Model\\BudgetEntry';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new BudgetEntryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   BudgetEntryQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return BudgetEntryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof BudgetEntryQuery) {
            return $criteria;
        }
        $query = new BudgetEntryQuery(null, null, $modelAlias);

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
     * @return   BudgetEntry|BudgetEntry[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BudgetEntryPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(BudgetEntryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 BudgetEntry A model object, or null if the key is not found
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
     * @return                 BudgetEntry A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `budget_id`, `category_id`, `amount`, `when_entry`, `description` FROM `budget_entries` WHERE `id` = :p0';
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
            $obj = new BudgetEntry();
            $obj->hydrate($row);
            BudgetEntryPeer::addInstanceToPool($obj, (string) $key);
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
     * @return BudgetEntry|BudgetEntry[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|BudgetEntry[]|mixed the list of results, formatted by the current formatter
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
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BudgetEntryPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BudgetEntryPeer::ID, $keys, Criteria::IN);
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
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BudgetEntryPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BudgetEntryPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the budget_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBudgetId(1234); // WHERE budget_id = 1234
     * $query->filterByBudgetId(array(12, 34)); // WHERE budget_id IN (12, 34)
     * $query->filterByBudgetId(array('min' => 12)); // WHERE budget_id >= 12
     * $query->filterByBudgetId(array('max' => 12)); // WHERE budget_id <= 12
     * </code>
     *
     * @see       filterByBudget()
     *
     * @param     mixed $budgetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByBudgetId($budgetId = null, $comparison = null)
    {
        if (is_array($budgetId)) {
            $useMinMax = false;
            if (isset($budgetId['min'])) {
                $this->addUsingAlias(BudgetEntryPeer::BUDGET_ID, $budgetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($budgetId['max'])) {
                $this->addUsingAlias(BudgetEntryPeer::BUDGET_ID, $budgetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::BUDGET_ID, $budgetId, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id >= 12
     * $query->filterByCategoryId(array('max' => 12)); // WHERE category_id <= 12
     * </code>
     *
     * @see       filterByCategory()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(BudgetEntryPeer::CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(BudgetEntryPeer::CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query on the amount column
     *
     * Example usage:
     * <code>
     * $query->filterByAmount(1234); // WHERE amount = 1234
     * $query->filterByAmount(array(12, 34)); // WHERE amount IN (12, 34)
     * $query->filterByAmount(array('min' => 12)); // WHERE amount >= 12
     * $query->filterByAmount(array('max' => 12)); // WHERE amount <= 12
     * </code>
     *
     * @param     mixed $amount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(BudgetEntryPeer::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(BudgetEntryPeer::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query on the when_entry column
     *
     * Example usage:
     * <code>
     * $query->filterByWhen(1234); // WHERE when_entry = 1234
     * $query->filterByWhen(array(12, 34)); // WHERE when_entry IN (12, 34)
     * $query->filterByWhen(array('min' => 12)); // WHERE when_entry >= 12
     * $query->filterByWhen(array('max' => 12)); // WHERE when_entry <= 12
     * </code>
     *
     * @param     mixed $when The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByWhen($when = null, $comparison = null)
    {
        if (is_array($when)) {
            $useMinMax = false;
            if (isset($when['min'])) {
                $this->addUsingAlias(BudgetEntryPeer::WHEN_ENTRY, $when['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($when['max'])) {
                $this->addUsingAlias(BudgetEntryPeer::WHEN_ENTRY, $when['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::WHEN_ENTRY, $when, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(BudgetEntryPeer::DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query by a related Budget object
     *
     * @param   Budget|PropelObjectCollection $budget The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BudgetEntryQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByBudget($budget, $comparison = null)
    {
        if ($budget instanceof Budget) {
            return $this
                ->addUsingAlias(BudgetEntryPeer::BUDGET_ID, $budget->getId(), $comparison);
        } elseif ($budget instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BudgetEntryPeer::BUDGET_ID, $budget->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function joinBudget($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useBudgetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBudget($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Budget', '\Mockingbird\Model\BudgetQuery');
    }

    /**
     * Filter the query by a related TransactionCategory object
     *
     * @param   TransactionCategory|PropelObjectCollection $transactionCategory The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 BudgetEntryQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategory($transactionCategory, $comparison = null)
    {
        if ($transactionCategory instanceof TransactionCategory) {
            return $this
                ->addUsingAlias(BudgetEntryPeer::CATEGORY_ID, $transactionCategory->getId(), $comparison);
        } elseif ($transactionCategory instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BudgetEntryPeer::CATEGORY_ID, $transactionCategory->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCategory() only accepts arguments of type TransactionCategory or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Category relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Category');

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
            $this->addJoinObject($join, 'Category');
        }

        return $this;
    }

    /**
     * Use the Category relation TransactionCategory object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionCategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\Mockingbird\Model\TransactionCategoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   BudgetEntry $budgetEntry Object to remove from the list of results
     *
     * @return BudgetEntryQuery The current query, for fluid interface
     */
    public function prune($budgetEntry = null)
    {
        if ($budgetEntry) {
            $this->addUsingAlias(BudgetEntryPeer::ID, $budgetEntry->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}

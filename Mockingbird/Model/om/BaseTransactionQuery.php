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
use Anthem\Auth\Model\User;
use Mockingbird\Model\Account;
use Mockingbird\Model\CounterParty;
use Mockingbird\Model\RefTransactionTag;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionCategory;
use Mockingbird\Model\TransactionPeer;
use Mockingbird\Model\TransactionQuery;
use Mockingbird\Model\TransactionTag;

/**
 * Base class that represents a query for the 'transactions' table.
 *
 *
 *
 * @method TransactionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method TransactionQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method TransactionQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method TransactionQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 * @method TransactionQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method TransactionQuery orderByTargetAccountId($order = Criteria::ASC) Order by the target_account_id column
 * @method TransactionQuery orderByCounterTransactionId($order = Criteria::ASC) Order by the counter_transaction_id column
 * @method TransactionQuery orderByCounterPartyId($order = Criteria::ASC) Order by the counter_party_id column
 * @method TransactionQuery orderByParentTransactionId($order = Criteria::ASC) Order by the parent_transaction_id column
 * @method TransactionQuery orderByAmount($order = Criteria::ASC) Order by the amount column
 * @method TransactionQuery orderByIsprojected($order = Criteria::ASC) Order by the isprojected column
 * @method TransactionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method TransactionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method TransactionQuery groupById() Group by the id column
 * @method TransactionQuery groupByUserId() Group by the user_id column
 * @method TransactionQuery groupByTitle() Group by the title column
 * @method TransactionQuery groupByCategoryId() Group by the category_id column
 * @method TransactionQuery groupByAccountId() Group by the account_id column
 * @method TransactionQuery groupByTargetAccountId() Group by the target_account_id column
 * @method TransactionQuery groupByCounterTransactionId() Group by the counter_transaction_id column
 * @method TransactionQuery groupByCounterPartyId() Group by the counter_party_id column
 * @method TransactionQuery groupByParentTransactionId() Group by the parent_transaction_id column
 * @method TransactionQuery groupByAmount() Group by the amount column
 * @method TransactionQuery groupByIsprojected() Group by the isprojected column
 * @method TransactionQuery groupByCreatedAt() Group by the created_at column
 * @method TransactionQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method TransactionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method TransactionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method TransactionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method TransactionQuery leftJoinUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the User relation
 * @method TransactionQuery rightJoinUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the User relation
 * @method TransactionQuery innerJoinUser($relationAlias = null) Adds a INNER JOIN clause to the query using the User relation
 *
 * @method TransactionQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method TransactionQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method TransactionQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method TransactionQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method TransactionQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method TransactionQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method TransactionQuery leftJoinTargetAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the TargetAccount relation
 * @method TransactionQuery rightJoinTargetAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TargetAccount relation
 * @method TransactionQuery innerJoinTargetAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the TargetAccount relation
 *
 * @method TransactionQuery leftJoinCounterTransaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the CounterTransaction relation
 * @method TransactionQuery rightJoinCounterTransaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CounterTransaction relation
 * @method TransactionQuery innerJoinCounterTransaction($relationAlias = null) Adds a INNER JOIN clause to the query using the CounterTransaction relation
 *
 * @method TransactionQuery leftJoinCounterParty($relationAlias = null) Adds a LEFT JOIN clause to the query using the CounterParty relation
 * @method TransactionQuery rightJoinCounterParty($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CounterParty relation
 * @method TransactionQuery innerJoinCounterParty($relationAlias = null) Adds a INNER JOIN clause to the query using the CounterParty relation
 *
 * @method TransactionQuery leftJoinParentTransaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the ParentTransaction relation
 * @method TransactionQuery rightJoinParentTransaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ParentTransaction relation
 * @method TransactionQuery innerJoinParentTransaction($relationAlias = null) Adds a INNER JOIN clause to the query using the ParentTransaction relation
 *
 * @method TransactionQuery leftJoinBackCounterTransactions($relationAlias = null) Adds a LEFT JOIN clause to the query using the BackCounterTransactions relation
 * @method TransactionQuery rightJoinBackCounterTransactions($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BackCounterTransactions relation
 * @method TransactionQuery innerJoinBackCounterTransactions($relationAlias = null) Adds a INNER JOIN clause to the query using the BackCounterTransactions relation
 *
 * @method TransactionQuery leftJoinSubTransactions($relationAlias = null) Adds a LEFT JOIN clause to the query using the SubTransactions relation
 * @method TransactionQuery rightJoinSubTransactions($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SubTransactions relation
 * @method TransactionQuery innerJoinSubTransactions($relationAlias = null) Adds a INNER JOIN clause to the query using the SubTransactions relation
 *
 * @method TransactionQuery leftJoinRefTransactionTag($relationAlias = null) Adds a LEFT JOIN clause to the query using the RefTransactionTag relation
 * @method TransactionQuery rightJoinRefTransactionTag($relationAlias = null) Adds a RIGHT JOIN clause to the query using the RefTransactionTag relation
 * @method TransactionQuery innerJoinRefTransactionTag($relationAlias = null) Adds a INNER JOIN clause to the query using the RefTransactionTag relation
 *
 * @method Transaction findOne(PropelPDO $con = null) Return the first Transaction matching the query
 * @method Transaction findOneOrCreate(PropelPDO $con = null) Return the first Transaction matching the query, or a new Transaction object populated from the query conditions when no match is found
 *
 * @method Transaction findOneByUserId(int $user_id) Return the first Transaction filtered by the user_id column
 * @method Transaction findOneByTitle(string $title) Return the first Transaction filtered by the title column
 * @method Transaction findOneByCategoryId(int $category_id) Return the first Transaction filtered by the category_id column
 * @method Transaction findOneByAccountId(int $account_id) Return the first Transaction filtered by the account_id column
 * @method Transaction findOneByTargetAccountId(int $target_account_id) Return the first Transaction filtered by the target_account_id column
 * @method Transaction findOneByCounterTransactionId(int $counter_transaction_id) Return the first Transaction filtered by the counter_transaction_id column
 * @method Transaction findOneByCounterPartyId(int $counter_party_id) Return the first Transaction filtered by the counter_party_id column
 * @method Transaction findOneByParentTransactionId(int $parent_transaction_id) Return the first Transaction filtered by the parent_transaction_id column
 * @method Transaction findOneByAmount(string $amount) Return the first Transaction filtered by the amount column
 * @method Transaction findOneByIsprojected(boolean $isprojected) Return the first Transaction filtered by the isprojected column
 * @method Transaction findOneByCreatedAt(string $created_at) Return the first Transaction filtered by the created_at column
 * @method Transaction findOneByUpdatedAt(string $updated_at) Return the first Transaction filtered by the updated_at column
 *
 * @method array findById(int $id) Return Transaction objects filtered by the id column
 * @method array findByUserId(int $user_id) Return Transaction objects filtered by the user_id column
 * @method array findByTitle(string $title) Return Transaction objects filtered by the title column
 * @method array findByCategoryId(int $category_id) Return Transaction objects filtered by the category_id column
 * @method array findByAccountId(int $account_id) Return Transaction objects filtered by the account_id column
 * @method array findByTargetAccountId(int $target_account_id) Return Transaction objects filtered by the target_account_id column
 * @method array findByCounterTransactionId(int $counter_transaction_id) Return Transaction objects filtered by the counter_transaction_id column
 * @method array findByCounterPartyId(int $counter_party_id) Return Transaction objects filtered by the counter_party_id column
 * @method array findByParentTransactionId(int $parent_transaction_id) Return Transaction objects filtered by the parent_transaction_id column
 * @method array findByAmount(string $amount) Return Transaction objects filtered by the amount column
 * @method array findByIsprojected(boolean $isprojected) Return Transaction objects filtered by the isprojected column
 * @method array findByCreatedAt(string $created_at) Return Transaction objects filtered by the created_at column
 * @method array findByUpdatedAt(string $updated_at) Return Transaction objects filtered by the updated_at column
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseTransactionQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseTransactionQuery object.
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
            $modelName = 'Mockingbird\\Model\\Transaction';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new TransactionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   TransactionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return TransactionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof TransactionQuery) {
            return $criteria;
        }
        $query = new TransactionQuery(null, null, $modelAlias);

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
     * @return   Transaction|Transaction[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = TransactionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(TransactionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Transaction A model object, or null if the key is not found
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
     * @return                 Transaction A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `user_id`, `title`, `category_id`, `account_id`, `target_account_id`, `counter_transaction_id`, `counter_party_id`, `parent_transaction_id`, `amount`, `isprojected`, `created_at`, `updated_at` FROM `transactions` WHERE `id` = :p0';
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
            $obj = new Transaction();
            $obj->hydrate($row);
            TransactionPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Transaction|Transaction[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Transaction[]|mixed the list of results, formatted by the current formatter
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(TransactionPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(TransactionPeer::ID, $keys, Criteria::IN);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(TransactionPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(TransactionPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::ID, $id, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(TransactionPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(TransactionPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::USER_ID, $userId, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(TransactionPeer::TITLE, $title, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(TransactionPeer::CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(TransactionPeer::CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id >= 12
     * $query->filterByAccountId(array('max' => 12)); // WHERE account_id <= 12
     * </code>
     *
     * @see       filterByAccount()
     *
     * @param     mixed $accountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(TransactionPeer::ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(TransactionPeer::ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::ACCOUNT_ID, $accountId, $comparison);
    }

    /**
     * Filter the query on the target_account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTargetAccountId(1234); // WHERE target_account_id = 1234
     * $query->filterByTargetAccountId(array(12, 34)); // WHERE target_account_id IN (12, 34)
     * $query->filterByTargetAccountId(array('min' => 12)); // WHERE target_account_id >= 12
     * $query->filterByTargetAccountId(array('max' => 12)); // WHERE target_account_id <= 12
     * </code>
     *
     * @see       filterByTargetAccount()
     *
     * @param     mixed $targetAccountId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByTargetAccountId($targetAccountId = null, $comparison = null)
    {
        if (is_array($targetAccountId)) {
            $useMinMax = false;
            if (isset($targetAccountId['min'])) {
                $this->addUsingAlias(TransactionPeer::TARGET_ACCOUNT_ID, $targetAccountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($targetAccountId['max'])) {
                $this->addUsingAlias(TransactionPeer::TARGET_ACCOUNT_ID, $targetAccountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::TARGET_ACCOUNT_ID, $targetAccountId, $comparison);
    }

    /**
     * Filter the query on the counter_transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCounterTransactionId(1234); // WHERE counter_transaction_id = 1234
     * $query->filterByCounterTransactionId(array(12, 34)); // WHERE counter_transaction_id IN (12, 34)
     * $query->filterByCounterTransactionId(array('min' => 12)); // WHERE counter_transaction_id >= 12
     * $query->filterByCounterTransactionId(array('max' => 12)); // WHERE counter_transaction_id <= 12
     * </code>
     *
     * @see       filterByCounterTransaction()
     *
     * @param     mixed $counterTransactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByCounterTransactionId($counterTransactionId = null, $comparison = null)
    {
        if (is_array($counterTransactionId)) {
            $useMinMax = false;
            if (isset($counterTransactionId['min'])) {
                $this->addUsingAlias(TransactionPeer::COUNTER_TRANSACTION_ID, $counterTransactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($counterTransactionId['max'])) {
                $this->addUsingAlias(TransactionPeer::COUNTER_TRANSACTION_ID, $counterTransactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::COUNTER_TRANSACTION_ID, $counterTransactionId, $comparison);
    }

    /**
     * Filter the query on the counter_party_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCounterPartyId(1234); // WHERE counter_party_id = 1234
     * $query->filterByCounterPartyId(array(12, 34)); // WHERE counter_party_id IN (12, 34)
     * $query->filterByCounterPartyId(array('min' => 12)); // WHERE counter_party_id >= 12
     * $query->filterByCounterPartyId(array('max' => 12)); // WHERE counter_party_id <= 12
     * </code>
     *
     * @see       filterByCounterParty()
     *
     * @param     mixed $counterPartyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByCounterPartyId($counterPartyId = null, $comparison = null)
    {
        if (is_array($counterPartyId)) {
            $useMinMax = false;
            if (isset($counterPartyId['min'])) {
                $this->addUsingAlias(TransactionPeer::COUNTER_PARTY_ID, $counterPartyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($counterPartyId['max'])) {
                $this->addUsingAlias(TransactionPeer::COUNTER_PARTY_ID, $counterPartyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::COUNTER_PARTY_ID, $counterPartyId, $comparison);
    }

    /**
     * Filter the query on the parent_transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentTransactionId(1234); // WHERE parent_transaction_id = 1234
     * $query->filterByParentTransactionId(array(12, 34)); // WHERE parent_transaction_id IN (12, 34)
     * $query->filterByParentTransactionId(array('min' => 12)); // WHERE parent_transaction_id >= 12
     * $query->filterByParentTransactionId(array('max' => 12)); // WHERE parent_transaction_id <= 12
     * </code>
     *
     * @see       filterByParentTransaction()
     *
     * @param     mixed $parentTransactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByParentTransactionId($parentTransactionId = null, $comparison = null)
    {
        if (is_array($parentTransactionId)) {
            $useMinMax = false;
            if (isset($parentTransactionId['min'])) {
                $this->addUsingAlias(TransactionPeer::PARENT_TRANSACTION_ID, $parentTransactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentTransactionId['max'])) {
                $this->addUsingAlias(TransactionPeer::PARENT_TRANSACTION_ID, $parentTransactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::PARENT_TRANSACTION_ID, $parentTransactionId, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByAmount($amount = null, $comparison = null)
    {
        if (is_array($amount)) {
            $useMinMax = false;
            if (isset($amount['min'])) {
                $this->addUsingAlias(TransactionPeer::AMOUNT, $amount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($amount['max'])) {
                $this->addUsingAlias(TransactionPeer::AMOUNT, $amount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::AMOUNT, $amount, $comparison);
    }

    /**
     * Filter the query on the isprojected column
     *
     * Example usage:
     * <code>
     * $query->filterByIsprojected(true); // WHERE isprojected = true
     * $query->filterByIsprojected('yes'); // WHERE isprojected = true
     * </code>
     *
     * @param     boolean|string $isprojected The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByIsprojected($isprojected = null, $comparison = null)
    {
        if (is_string($isprojected)) {
            $isprojected = in_array(strtolower($isprojected), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(TransactionPeer::ISPROJECTED, $isprojected, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(TransactionPeer::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(TransactionPeer::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::CREATED_AT, $createdAt, $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(TransactionPeer::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(TransactionPeer::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(TransactionPeer::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related User object
     *
     * @param   User|PropelObjectCollection $user The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByUser($user, $comparison = null)
    {
        if ($user instanceof User) {
            return $this
                ->addUsingAlias(TransactionPeer::USER_ID, $user->getId(), $comparison);
        } elseif ($user instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::USER_ID, $user->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'User', '\Anthem\Auth\Model\UserQuery');
    }

    /**
     * Filter the query by a related TransactionCategory object
     *
     * @param   TransactionCategory|PropelObjectCollection $transactionCategory The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCategory($transactionCategory, $comparison = null)
    {
        if ($transactionCategory instanceof TransactionCategory) {
            return $this
                ->addUsingAlias(TransactionPeer::CATEGORY_ID, $transactionCategory->getId(), $comparison);
        } elseif ($transactionCategory instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::CATEGORY_ID, $transactionCategory->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\Mockingbird\Model\TransactionCategoryQuery');
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(TransactionPeer::ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAccount() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Account relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinAccount($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Account');

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
            $this->addJoinObject($join, 'Account');
        }

        return $this;
    }

    /**
     * Use the Account relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\Mockingbird\Model\AccountQuery');
    }

    /**
     * Filter the query by a related Account object
     *
     * @param   Account|PropelObjectCollection $account The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTargetAccount($account, $comparison = null)
    {
        if ($account instanceof Account) {
            return $this
                ->addUsingAlias(TransactionPeer::TARGET_ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::TARGET_ACCOUNT_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTargetAccount() only accepts arguments of type Account or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TargetAccount relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinTargetAccount($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TargetAccount');

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
            $this->addJoinObject($join, 'TargetAccount');
        }

        return $this;
    }

    /**
     * Use the TargetAccount relation Account object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\AccountQuery A secondary query class using the current class as primary query
     */
    public function useTargetAccountQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTargetAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TargetAccount', '\Mockingbird\Model\AccountQuery');
    }

    /**
     * Filter the query by a related Transaction object
     *
     * @param   Transaction|PropelObjectCollection $transaction The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCounterTransaction($transaction, $comparison = null)
    {
        if ($transaction instanceof Transaction) {
            return $this
                ->addUsingAlias(TransactionPeer::COUNTER_TRANSACTION_ID, $transaction->getId(), $comparison);
        } elseif ($transaction instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::COUNTER_TRANSACTION_ID, $transaction->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCounterTransaction() only accepts arguments of type Transaction or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CounterTransaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinCounterTransaction($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CounterTransaction');

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
            $this->addJoinObject($join, 'CounterTransaction');
        }

        return $this;
    }

    /**
     * Use the CounterTransaction relation Transaction object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useCounterTransactionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCounterTransaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CounterTransaction', '\Mockingbird\Model\TransactionQuery');
    }

    /**
     * Filter the query by a related CounterParty object
     *
     * @param   CounterParty|PropelObjectCollection $counterParty The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCounterParty($counterParty, $comparison = null)
    {
        if ($counterParty instanceof CounterParty) {
            return $this
                ->addUsingAlias(TransactionPeer::COUNTER_PARTY_ID, $counterParty->getId(), $comparison);
        } elseif ($counterParty instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::COUNTER_PARTY_ID, $counterParty->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCounterParty() only accepts arguments of type CounterParty or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CounterParty relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinCounterParty($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CounterParty');

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
            $this->addJoinObject($join, 'CounterParty');
        }

        return $this;
    }

    /**
     * Use the CounterParty relation CounterParty object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\CounterPartyQuery A secondary query class using the current class as primary query
     */
    public function useCounterPartyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCounterParty($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CounterParty', '\Mockingbird\Model\CounterPartyQuery');
    }

    /**
     * Filter the query by a related Transaction object
     *
     * @param   Transaction|PropelObjectCollection $transaction The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByParentTransaction($transaction, $comparison = null)
    {
        if ($transaction instanceof Transaction) {
            return $this
                ->addUsingAlias(TransactionPeer::PARENT_TRANSACTION_ID, $transaction->getId(), $comparison);
        } elseif ($transaction instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(TransactionPeer::PARENT_TRANSACTION_ID, $transaction->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByParentTransaction() only accepts arguments of type Transaction or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ParentTransaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinParentTransaction($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ParentTransaction');

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
            $this->addJoinObject($join, 'ParentTransaction');
        }

        return $this;
    }

    /**
     * Use the ParentTransaction relation Transaction object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useParentTransactionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinParentTransaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ParentTransaction', '\Mockingbird\Model\TransactionQuery');
    }

    /**
     * Filter the query by a related Transaction object
     *
     * @param   Transaction|PropelObjectCollection $transaction  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByBackCounterTransactions($transaction, $comparison = null)
    {
        if ($transaction instanceof Transaction) {
            return $this
                ->addUsingAlias(TransactionPeer::ID, $transaction->getCounterTransactionId(), $comparison);
        } elseif ($transaction instanceof PropelObjectCollection) {
            return $this
                ->useBackCounterTransactionsQuery()
                ->filterByPrimaryKeys($transaction->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBackCounterTransactions() only accepts arguments of type Transaction or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BackCounterTransactions relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinBackCounterTransactions($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BackCounterTransactions');

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
            $this->addJoinObject($join, 'BackCounterTransactions');
        }

        return $this;
    }

    /**
     * Use the BackCounterTransactions relation Transaction object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useBackCounterTransactionsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinBackCounterTransactions($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BackCounterTransactions', '\Mockingbird\Model\TransactionQuery');
    }

    /**
     * Filter the query by a related Transaction object
     *
     * @param   Transaction|PropelObjectCollection $transaction  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterBySubTransactions($transaction, $comparison = null)
    {
        if ($transaction instanceof Transaction) {
            return $this
                ->addUsingAlias(TransactionPeer::ID, $transaction->getParentTransactionId(), $comparison);
        } elseif ($transaction instanceof PropelObjectCollection) {
            return $this
                ->useSubTransactionsQuery()
                ->filterByPrimaryKeys($transaction->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySubTransactions() only accepts arguments of type Transaction or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SubTransactions relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinSubTransactions($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SubTransactions');

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
            $this->addJoinObject($join, 'SubTransactions');
        }

        return $this;
    }

    /**
     * Use the SubTransactions relation Transaction object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useSubTransactionsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSubTransactions($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SubTransactions', '\Mockingbird\Model\TransactionQuery');
    }

    /**
     * Filter the query by a related RefTransactionTag object
     *
     * @param   RefTransactionTag|PropelObjectCollection $refTransactionTag  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 TransactionQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByRefTransactionTag($refTransactionTag, $comparison = null)
    {
        if ($refTransactionTag instanceof RefTransactionTag) {
            return $this
                ->addUsingAlias(TransactionPeer::ID, $refTransactionTag->getTransactionId(), $comparison);
        } elseif ($refTransactionTag instanceof PropelObjectCollection) {
            return $this
                ->useRefTransactionTagQuery()
                ->filterByPrimaryKeys($refTransactionTag->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByRefTransactionTag() only accepts arguments of type RefTransactionTag or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the RefTransactionTag relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function joinRefTransactionTag($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('RefTransactionTag');

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
            $this->addJoinObject($join, 'RefTransactionTag');
        }

        return $this;
    }

    /**
     * Use the RefTransactionTag relation RefTransactionTag object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\RefTransactionTagQuery A secondary query class using the current class as primary query
     */
    public function useRefTransactionTagQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRefTransactionTag($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'RefTransactionTag', '\Mockingbird\Model\RefTransactionTagQuery');
    }

    /**
     * Filter the query by a related TransactionTag object
     * using the ref_transactions_tags table as cross reference
     *
     * @param   TransactionTag $transactionTag the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   TransactionQuery The current query, for fluid interface
     */
    public function filterByTransactionTag($transactionTag, $comparison = Criteria::EQUAL)
    {
        return $this
            ->useRefTransactionTagQuery()
            ->filterByTransactionTag($transactionTag, $comparison)
            ->endUse();
    }

    /**
     * Exclude object from result
     *
     * @param   Transaction $transaction Object to remove from the list of results
     *
     * @return TransactionQuery The current query, for fluid interface
     */
    public function prune($transaction = null)
    {
        if ($transaction) {
            $this->addUsingAlias(TransactionPeer::ID, $transaction->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

  // timestampable behavior

  /**
   * Filter by the latest updated
   *
   * @param      int $nbDays Maximum age of the latest update in days
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function recentlyUpdated($nbDays = 7)
  {
      return $this->addUsingAlias(TransactionPeer::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
  }

  /**
   * Order by update date desc
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function lastUpdatedFirst()
  {
      return $this->addDescendingOrderByColumn(TransactionPeer::UPDATED_AT);
  }

  /**
   * Order by update date asc
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function firstUpdatedFirst()
  {
      return $this->addAscendingOrderByColumn(TransactionPeer::UPDATED_AT);
  }

  /**
   * Filter by the latest created
   *
   * @param      int $nbDays Maximum age of in days
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function recentlyCreated($nbDays = 7)
  {
      return $this->addUsingAlias(TransactionPeer::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
  }

  /**
   * Order by create date desc
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function lastCreatedFirst()
  {
      return $this->addDescendingOrderByColumn(TransactionPeer::CREATED_AT);
  }

  /**
   * Order by create date asc
   *
   * @return     TransactionQuery The current query, for fluid interface
   */
  public function firstCreatedFirst()
  {
      return $this->addAscendingOrderByColumn(TransactionPeer::CREATED_AT);
  }
}

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
use Mockingbird\Model\RefTransactionTag;
use Mockingbird\Model\RefTransactionTagPeer;
use Mockingbird\Model\RefTransactionTagQuery;
use Mockingbird\Model\Transaction;
use Mockingbird\Model\TransactionTag;

/**
 * Base class that represents a query for the 'ref_transactions_tags' table.
 *
 *
 *
 * @method RefTransactionTagQuery orderByTransactionId($order = Criteria::ASC) Order by the transaction_id column
 * @method RefTransactionTagQuery orderByTagId($order = Criteria::ASC) Order by the tag_id column
 *
 * @method RefTransactionTagQuery groupByTransactionId() Group by the transaction_id column
 * @method RefTransactionTagQuery groupByTagId() Group by the tag_id column
 *
 * @method RefTransactionTagQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method RefTransactionTagQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method RefTransactionTagQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method RefTransactionTagQuery leftJoinTransaction($relationAlias = null) Adds a LEFT JOIN clause to the query using the Transaction relation
 * @method RefTransactionTagQuery rightJoinTransaction($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Transaction relation
 * @method RefTransactionTagQuery innerJoinTransaction($relationAlias = null) Adds a INNER JOIN clause to the query using the Transaction relation
 *
 * @method RefTransactionTagQuery leftJoinTransactionTag($relationAlias = null) Adds a LEFT JOIN clause to the query using the TransactionTag relation
 * @method RefTransactionTagQuery rightJoinTransactionTag($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TransactionTag relation
 * @method RefTransactionTagQuery innerJoinTransactionTag($relationAlias = null) Adds a INNER JOIN clause to the query using the TransactionTag relation
 *
 * @method RefTransactionTag findOne(PropelPDO $con = null) Return the first RefTransactionTag matching the query
 * @method RefTransactionTag findOneOrCreate(PropelPDO $con = null) Return the first RefTransactionTag matching the query, or a new RefTransactionTag object populated from the query conditions when no match is found
 *
 * @method RefTransactionTag findOneByTransactionId(int $transaction_id) Return the first RefTransactionTag filtered by the transaction_id column
 * @method RefTransactionTag findOneByTagId(int $tag_id) Return the first RefTransactionTag filtered by the tag_id column
 *
 * @method array findByTransactionId(int $transaction_id) Return RefTransactionTag objects filtered by the transaction_id column
 * @method array findByTagId(int $tag_id) Return RefTransactionTag objects filtered by the tag_id column
 *
 * @package    propel.generator.Mockingbird.Model.om
 */
abstract class BaseRefTransactionTagQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseRefTransactionTagQuery object.
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
            $modelName = 'Mockingbird\\Model\\RefTransactionTag';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new RefTransactionTagQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   RefTransactionTagQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return RefTransactionTagQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof RefTransactionTagQuery) {
            return $criteria;
        }
        $query = new RefTransactionTagQuery(null, null, $modelAlias);

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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$transaction_id, $tag_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   RefTransactionTag|RefTransactionTag[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RefTransactionTagPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(RefTransactionTagPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 RefTransactionTag A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `transaction_id`, `tag_id` FROM `ref_transactions_tags` WHERE `transaction_id` = :p0 AND `tag_id` = :p1';
        try {
            $stmt = $con->prepare($sql);
      $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
      $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new RefTransactionTag();
            $obj->hydrate($row);
            RefTransactionTagPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return RefTransactionTag|RefTransactionTag[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|RefTransactionTag[]|mixed the list of results, formatted by the current formatter
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
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(RefTransactionTagPeer::TAG_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(RefTransactionTagPeer::TRANSACTION_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(RefTransactionTagPeer::TAG_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the transaction_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTransactionId(1234); // WHERE transaction_id = 1234
     * $query->filterByTransactionId(array(12, 34)); // WHERE transaction_id IN (12, 34)
     * $query->filterByTransactionId(array('min' => 12)); // WHERE transaction_id >= 12
     * $query->filterByTransactionId(array('max' => 12)); // WHERE transaction_id <= 12
     * </code>
     *
     * @see       filterByTransaction()
     *
     * @param     mixed $transactionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function filterByTransactionId($transactionId = null, $comparison = null)
    {
        if (is_array($transactionId)) {
            $useMinMax = false;
            if (isset($transactionId['min'])) {
                $this->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $transactionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($transactionId['max'])) {
                $this->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $transactionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $transactionId, $comparison);
    }

    /**
     * Filter the query on the tag_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTagId(1234); // WHERE tag_id = 1234
     * $query->filterByTagId(array(12, 34)); // WHERE tag_id IN (12, 34)
     * $query->filterByTagId(array('min' => 12)); // WHERE tag_id >= 12
     * $query->filterByTagId(array('max' => 12)); // WHERE tag_id <= 12
     * </code>
     *
     * @see       filterByTransactionTag()
     *
     * @param     mixed $tagId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function filterByTagId($tagId = null, $comparison = null)
    {
        if (is_array($tagId)) {
            $useMinMax = false;
            if (isset($tagId['min'])) {
                $this->addUsingAlias(RefTransactionTagPeer::TAG_ID, $tagId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($tagId['max'])) {
                $this->addUsingAlias(RefTransactionTagPeer::TAG_ID, $tagId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RefTransactionTagPeer::TAG_ID, $tagId, $comparison);
    }

    /**
     * Filter the query by a related Transaction object
     *
     * @param   Transaction|PropelObjectCollection $transaction The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RefTransactionTagQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTransaction($transaction, $comparison = null)
    {
        if ($transaction instanceof Transaction) {
            return $this
                ->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $transaction->getId(), $comparison);
        } elseif ($transaction instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RefTransactionTagPeer::TRANSACTION_ID, $transaction->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTransaction() only accepts arguments of type Transaction or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Transaction relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function joinTransaction($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Transaction');

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
            $this->addJoinObject($join, 'Transaction');
        }

        return $this;
    }

    /**
     * Use the Transaction relation Transaction object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionQuery A secondary query class using the current class as primary query
     */
    public function useTransactionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTransaction($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Transaction', '\Mockingbird\Model\TransactionQuery');
    }

    /**
     * Filter the query by a related TransactionTag object
     *
     * @param   TransactionTag|PropelObjectCollection $transactionTag The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 RefTransactionTagQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTransactionTag($transactionTag, $comparison = null)
    {
        if ($transactionTag instanceof TransactionTag) {
            return $this
                ->addUsingAlias(RefTransactionTagPeer::TAG_ID, $transactionTag->getId(), $comparison);
        } elseif ($transactionTag instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(RefTransactionTagPeer::TAG_ID, $transactionTag->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTransactionTag() only accepts arguments of type TransactionTag or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TransactionTag relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function joinTransactionTag($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TransactionTag');

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
            $this->addJoinObject($join, 'TransactionTag');
        }

        return $this;
    }

    /**
     * Use the TransactionTag relation TransactionTag object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Mockingbird\Model\TransactionTagQuery A secondary query class using the current class as primary query
     */
    public function useTransactionTagQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTransactionTag($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TransactionTag', '\Mockingbird\Model\TransactionTagQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   RefTransactionTag $refTransactionTag Object to remove from the list of results
     *
     * @return RefTransactionTagQuery The current query, for fluid interface
     */
    public function prune($refTransactionTag = null)
    {
        if ($refTransactionTag) {
            $this->addCond('pruneCond0', $this->getAliasedColName(RefTransactionTagPeer::TRANSACTION_ID), $refTransactionTag->getTransactionId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(RefTransactionTagPeer::TAG_ID), $refTransactionTag->getTagId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

}

/**
* Returns the root node for a given scope
*
* @param      PropelPDO $con	Connection to use.
* @return     <?php echo $phpName ?>			Propel object for root node
*/
public static function retrieveRoot(PropelPDO $con = null)
{
  $c = new Criteria(<?php echo $peer ?>::DATABASE_NAME);
  $c->add(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, 1, Criteria::EQUAL);

  return <?php echo $peer ?>::doSelectOne($c, $con);
}

/**
* Returns the whole tree node for a given scope
*
* @param      Criteria $criteria	Optional Criteria to filter the query
* @param      PropelPDO $con	Connection to use.
* @return     <?php echo $phpName ?>			Propel object for root node
*/
public static function retrieveTree(Criteria $criteria = null, PropelPDO $con = null)
{
  if ($criteria === null) {
    $criteria = new Criteria(<?php echo $peer ?>::DATABASE_NAME);
  }
  $criteria->addAscendingOrderByColumn(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL);
  $criteria->addAscendingOrderByColumn(<?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL);
  
  return <?php echo $peer ?>::doSelect($criteria, $con);
}

/**
* Delete an entire tree
*
* @param      PropelPDO $con	Connection to use.
*
* @return     int  The number of deleted nodes
*/
public static function deleteTree(PropelPDO $con = null)
{
return <?php echo $peer ?>::doDeleteAll($con);
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param string $parent<?php echo $Level, PHP_EOL ?>
 * @param int $<?php echo $Level, PHP_EOL ?>
 * @param int $<?php echo $Order ?>From
 * @param int $<?php echo $Order ?>To null for last
 * @param $direction +1 or -1
 * @return int rows affected
 */
public static function shiftNodes(PropelPDO $con, $parent<?php echo $Level ?>, $<?php echo $Level ?>, $<?php echo $Order ?>From = 0, $<?php echo $Order ?>To = null, $direction = +1)
{
  $direction = (int)$direction;

  // filter nodes
  $select = <?php echo $query ?>::create()
    ->filterByParent<?php echo $Path ?>($parent<?php echo $Level ?>)
    ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, $<?php echo $Level ?>, Criteria::EQUAL)
    ->_if($<?php echo $Order ?>From > 0)
      ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL, $<?php echo $Order ?>From, Criteria::GREATER_EQUAL)
    ->_endif()
    ->_if($<?php echo $Order ?>To !== null)
      ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL, $<?php echo $Order ?>To, Criteria::LESS_EQUAL)
    ->_endif()
    ->setComment(__METHOD__);

  // shift nodes
  /** @var $update <?php echo $query ?> */
  $update = <?php echo $query ?>::create()->setComment(__METHOD__);
  $ORDER_NUMBER = $update->getAliasedColName(<?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL);
  $update->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL, "$ORDER_NUMBER + ($direction)", Criteria::CUSTOM_EQUAL);

  return BasePeer::doUpdate($select, $update, $con);
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param <?php echo $phpName ?> $root
 * @param string $newParent<?php echo $Path, PHP_EOL ?>
 * @param int $new<?php echo $Level, PHP_EOL ?>
 * @return int affected rows
 */
public static function moveSubtree(PropelPDO $con, $root, $newParent<?php echo $Path ?>, $new<?php echo $Level ?>)
{
  $offset = strlen($root-><?php echo $getParentPath ?>()) + 1;
  $<?php echo $Level ?>Delta = $new<?php echo $Level ?> - $root-><?php echo $getLevel ?>();

  $select = <?php echo $query ?>::create()
    ->branchOf($root)
    ->setComment(__METHOD__);

  $update = <?php echo $query ?>::create();
  $LEVEL = $update->getAliasedColName(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL);
  $PATH = $update->getAliasedColName(<?php echo $peer ?>::MATERIALIZED_PATH_PATH_COL);

  $update
    ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_PATH_COL, "CONCAT('$newParent<?php echo $Path ?>',MID($PATH, $offset))", Criteria::CUSTOM_EQUAL)
    ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, "$LEVEL + ($<?php echo $Level ?>Delta)", Criteria::CUSTOM_EQUAL)
    ->setComment(__METHOD__);

  return BasePeer::doUpdate($select, $update, $con);
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param <?php echo $phpName ?> $node
 * @param array $values
 * @return int affected rows
 */
public static function updateNode(PropelPDO $con, $node, $values)
{
  $select = <?php echo $query ?>::create()
    ->filterByPrimaryKey($node->getPrimaryKey())
    ->setComment(__METHOD__);

  $update = <?php echo $query ?>::create()->setComment(__METHOD__);
  foreach ($values as $key => $value)
  {
    $node->setByName($key, $value, BasePeer::TYPE_COLNAME);
    $node->resetModified($key);
    $update->addUsingAlias($key, $value);
  }
  // TODO: Consider side effects when this throws exception
  // properties will be set and not marked as modified
  return BasePeer::doUpdate($select, $update, $con);
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param $parent <?php echo $phpName, PHP_EOL ?>
 * @param $newNode <?php echo $phpName, PHP_EOL ?>
 * @return int
 */
public static function makeNodeFirstChild(PropelPDO $con, $parent, $newNode)
{
  $shifted = <?php echo $peer ?>:: shiftNodes($con, $parent-><?php echo $getPath ?>(), $parent-><?php echo $getLevel ?>() + 1);
  <?php echo $peer ?>:: updateLoadedNodes($newNode, $con);
  return $shifted;
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param $parent <?php echo $phpName, PHP_EOL ?>
 * @param $newNode <?php echo $phpName, PHP_EOL ?>
 */
public static function makeNodeLastChild(PropelPDO $con, $parent, $newNode)
{
  $childrenAmount = $parent->countChildren(<?php echo $query ?>::create()->setComment(__METHOD__), $con);
  $newNode-><?php echo $setOrder ?>($childrenAmount);
// <?php echo $peer ?>:: updateLoadedNodes($newNode, $con);
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param $pivot <?php echo $phpName, PHP_EOL ?>
 * @param $newNode <?php echo $phpName, PHP_EOL ?>
 * @return int rows affected
 */
public static function insertNodeBefore(PropelPDO $con, $pivot, $newNode)
{
  $shifted = <?php echo $peer ?>:: shiftNodes($con, $pivot-><?php echo $getParentPath ?>(), $pivot-><?php echo $getLevel ?>(), $pivot-><?php echo $getOrder ?>());
  <?php echo $peer ?>:: updateLoadedNodes($newNode, $con);
  return $shifted;
}

/**
 * @internal
 *
 * @param PropelPDO $con
 * @param $pivot <?php echo $phpName, PHP_EOL ?>
 * @param $newNode <?php echo $phpName, PHP_EOL ?>
 * @return int rows affected
 */
public static function insertNodeAfter(PropelPDO $con, $pivot, $newNode)
{
  $shifted = <?php echo $peer ?>:: shiftNodes($con, $pivot-><?php echo $getParentPath ?>(), $pivot-><?php echo $getLevel ?>(), $pivot-><?php echo $getOrder ?>() + 1);
  <?php echo $peer ?>:: updateLoadedNodes($newNode, $con);
  return $shifted;
}

/**
 * @internal
 *
 * Reload all already loaded nodes to sync them with updated db
 *
 * @param      <?php echo $phpName ?> $prune		Object to prune from the update
 * @param      PropelPDO $con		Connection to use.
*/
public static function updateLoadedNodes($prune = null, PropelPDO $con = null)
{
  if (Propel::isInstancePoolingEnabled())
  {
    $keys = array();
    foreach (<?php echo $peer ?>::$instances as $obj)
    {
      if (!$prune || !$prune->equals($obj)) {
        $keys[] = $obj->getPrimaryKey();
      }
    }

    if (!empty($keys))
    {
      // We don't need to alter the object instance pool; we're just modifying these ones
      // already in the pool.
      $criteria = new Criteria(<?php echo $peer ?>::DATABASE_NAME);
      $criteria->add(<?php echo $peer ?>::ID, $keys, Criteria::IN);

      $stmt = <?php echo $peer ?>::doSelectStmt($criteria, $con);
      while ($row = $stmt->fetch(PDO::FETCH_NUM))
      {
        $key = <?php echo $peer ?>::getPrimaryKeyHashFromRow($row, 0);
        if (null !== ($object = <?php echo $peer ?>::getInstanceFromPool($key)))
        {
          $object-><?php echo $setPath ?>($row[<?php echo $pathColumn->getPosition() ?>]);
          $object-><?php echo $setLevel ?>($row[<?php echo $levelColumn->getPosition() ?>]);
          $object->clearMaterializedPathChildren();

          $object->resetModified(<?php echo $peer ?>::MATERIALIZED_PATH_PATH_COL);
          $object->resetModified(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL);
        }
      }
      $stmt->closeCursor();
    }
  }
}
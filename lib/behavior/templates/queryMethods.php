  /**
   * @param $<?php echo $Path ?> string
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function filterByParent<?php echo $Path ?>($<?php echo $Path ?>)
  {
    if ($<?php echo $Path ?>) // only if it is not empty
    {
      $this->filterBy<?php echo $Path ?>($<?php echo $Path ?>.'%', Criteria::LIKE);
    }
    return $this;
  }

  /**
   * @param $<?php echo $phpName ?> <?php echo $phpName, PHP_EOL ?>
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function childrenOf($<?php echo $phpName ?>)
  {
    return $this
      ->filterByParent<?php echo $Path ?>($<?php echo $phpName ?>-><?php echo $getPath ?>())
      ->filterBy<?php echo $Level ?>($<?php echo $phpName ?>-><?php echo $getLevel ?>()+1);
  }

  /**
   * @param $<?php echo $phpName ?> <?php echo $phpName, PHP_EOL ?>
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function descendantsOf($<?php echo $phpName ?>)
  {
    return $this
      ->filterByParent<?php echo $Path ?>($<?php echo $phpName ?>-><?php echo $getPath ?>())
      ->filterBy<?php echo $Level ?>($<?php echo $phpName ?>-><?php echo $getLevel ?>(), Criteria::GREATER_THAN);
  }

  /**
   * @param $<?php echo $phpName ?> <?php echo $phpName, PHP_EOL ?>
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function siblingsOf($<?php echo $phpName ?>)
  {
    return $this
      ->filterByParent<?php echo $Path ?>($<?php echo $phpName ?>-><?php echo $getParentPath ?>())
      ->filterBy<?php echo $Level ?>($<?php echo $phpName ?>-><?php echo $getLevel ?>());
  }

  /**
   * @param $<?php echo $phpName ?> <?php echo $phpName, PHP_EOL ?>
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function branchOf($<?php echo $phpName ?>)
  {
    return $this->filterByParent<?php echo $Path ?>($<?php echo $phpName ?>-><?php echo $getPath ?>());
  }

  /**
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function orderByBranch()
  {
    return $this->orderBy<?php echo $Level ?>()->orderBy<?php echo $Order ?>();
  }

  /**
   * @param $<?php echo $phpName ?> <?php echo $phpName, PHP_EOL ?>
   * @return <?php echo $query, PHP_EOL ?>
   */
  public function ancestorsOf($<?php echo $phpName ?>)
  {
    return $this
      ->filterBy<?php echo $Level ?>($<?php echo $phpName ?>-><?php echo $getLevel ?>(), Criteria::LESS_THAN)
      ->rootsOf($<?php echo $phpName ?>);
  }

  /**
   * Filter the query to restrict the result to roots of an object.
   * Same as ancestorsOf(), except that it includes the object passed as parameter in the result
   *
   * @param     <?php echo $phpName ?> $<?php echo $phpName ?> The object to use for roots search
   *
   * @return    <?php echo $query ?> The current query, for fluid interface
   */
  public function rootsOf($<?php echo $phpName ?>)
  {
    $PATH = <?php echo $peer ?>::alias($this->getModelAliasOrName(), <?php echo $peer ?>::MATERIALIZED_PATH_PATH_COL);
    $mask = <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER.'%';
    return $this
      ->where("? LIKE CONCAT($PATH, '$mask')", $<?php echo $phpName ?>-><?php echo $getPath ?>());
  }

  /**
   * Returns the root node for the tree
   *
   * @param      PropelPDO $con	Connection to use.
   *
   * @return     <?php echo $phpName ?> The tree root object
   */
  public function findRoot($con = null)
  {
    return $this
      ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, 1, Criteria::EQUAL)
      ->findOne($con);
  }

  /**
   * Returns the tree of objects
   *
   * @param      PropelPDO $con	Connection to use.
   *
   * @return     mixed the list of results, formatted by the current formatter
   */
  public function findTree($con = null)
  {
    return $this
      ->orderByBranch()
      ->find($con);
  }
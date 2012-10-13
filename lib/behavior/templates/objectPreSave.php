      if ($this->isNew() && $this->isRoot()) {
        // check if no other root exist in, the tree
        $roots = <?php echo $query ?>::create()
          ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, 1, Criteria::EQUAL)
          ->setComment(__METHOD__)
          ->count($con);
        if ($roots > 0) {
          throw new PropelException('A root node already exists in this tree.');
        }
      }
      if (!$this->isValid())
      {
        throw new PropelException('This node is not valid. Check if you set <?php echo $Crumb ?> value.');
      }
      $this->processMaterializedPathQueries($con);

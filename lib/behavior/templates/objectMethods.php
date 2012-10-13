  /**
   * @return int
   */
  public function calculate<?php echo $Level ?>()
  {
    // one delimiter for each level
    return substr_count($this-><?php echo $getPath ?>(), <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER);
  }

  /**
   * @return string
   */
  public function <?php echo $getParentPath ?>()
  {
    if ($this-><?php echo $getLevel ?>() > 1)
    {
      $<?php echo $Path ?> = $this-><?php echo $getPath ?>();
      // find last delimiter
      $pos = strrpos($<?php echo $Path ?>, <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER);
      // return portion of <?php echo $Path ?> to the last DELIMITER
      return substr($<?php echo $Path ?>, 0, $pos);
    }
  }

  /**
   * @param $v string
   * @return <?php echo $phpName, PHP_EOL ?>
   */
  public function <?php echo $setParentPath ?>($v)
  {
    return $this-><?php echo $setPath ?>($v . <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER . $this-><?php echo $getCrumb ?>());
  }

  /**
   * Creates the supplied node as the root node.
   *
   * @return     <?php echo $phpName ?> The current object (for fluent API support)
   * @throws     PropelException
   */
  public function makeRoot()
  {
    if ($this-><?php echo $getPath ?>())
    {
      throw new PropelException('Cannot turn an existing node into a root node.');
    }
    $this-><?php echo $setCrumb ?>(null);
    $this-><?php echo $setPath ?>(null);
    $this-><?php echo $setLevel ?>(1);
    return $this;
  }

  /**
   * @return bool
   */
  public function isValid()
  {
    if ($this->isRoot() || strlen($this-><?php echo $getCrumb ?>()) > 0)
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Tests if object is a node, i.e. if it is inserted in the tree
   *
   * @return     bool
   */
  public function isInTree()
  {
    return $this-><?php echo $getLevel ?>() > 0 && ($this->isRoot() || strlen($this-><?php echo $getPath ?>()) > 0);
  }

  /**
   * Tests if node is a root
   *
   * @return     bool
   */
  public function isRoot()
  {
    return $this-><?php echo $getPath ?>() == ''
      && $this-><?php echo $getLevel ?>() == 1;
  }

  /**
   * Tests if node is a leaf
   *
   * @param PropelPDO $con
   * @return     bool
   */
  public function isLeaf(PropelPDO $con = null)
  {
    return $this->isInTree() && $this->countChildren(null, $con) == 0;
  }

  /**
   * Tests if node is a descendant of another node
   *
   * @param      <?php echo $phpName ?> $parent Propel node object
   * @return     bool
   */
  public function isDescendantOf($parent)
  {
    // url starts with parent node's <?php echo $Path, PHP_EOL ?>
    return $this->isInTree() && $parent->isInTree() && substr($this-><?php echo $getPath ?>(), 0, strlen($parent-><?php echo $getPath ?>())) == $parent-><?php echo $getPath ?>();
  }

  /**
   * Tests if node is a ancestor of another node
   *
   * @param      <?php echo $phpName ?> $child Propel node object
   * @return     bool
   */
  public function isAncestorOf($child)
  {
    return $child->isDescendantOf($this);
  }

  /**
   * Tests if object has an ancestor
   *
   * @param      PropelPDO $con Connection to use.
   * @return     bool
   */
  public function hasParent(PropelPDO $con = null)
  {
    return $this-><?php echo $getLevel ?>() > 0;
  }

  /**
   * Sets the cache for parent node of the current object.
   * Warning: this does not move the current object in the tree.
   * Use moveTofirstChildOf() or moveToLastChildOf() for that purpose
   *
   * @param      <?php echo $phpName ?> $parent
   * @return     <?php echo $phpName ?> The current object, for fluid interface
   */
  public function setParent($parent = null)
  {
    $this->aMaterializedPathParent = $parent;

    return $this;
  }

  /**
   * Gets parent node for the current object if it exists
   * The result is cached so further calls to the same method don't issue any queries
   *
   * @param      PropelPDO $con Connection to use.
   * @return     <?php echo $phpName ?> 		Propel object if exists else false
   */
  public function getParent(PropelPDO $con = null)
  {
    if ($this->aMaterializedPathParent === null && $this->hasParent()) {
      $this->aMaterializedPathParent = <?php echo $phpName ?>Query::create()
        ->filterBy<?php echo $Path ?>($this-><?php echo $getParentPath ?>())
        ->setComment(__METHOD__)
        ->findOne($con);
    }

    return $this->aMaterializedPathParent;
  }

  /**
   * Determines if the node has previous sibling
   *
   * @param      PropelPDO $con Connection to use.
   * @return     bool
   */
  public function hasPrevSibling(PropelPDO $con = null)
  {
    if (!$this->isValid()) {
      return false;
    }

    return <?php echo $phpName ?>Query::create()
      ->filterByParent<?php echo $Path ?>($this-><?php echo $getParentPath ?>())
      ->filterBy<?php echo $Level ?>($this-><?php echo $getLevel ?>())
      ->filterBy<?php echo $Order ?>($this-><?php echo $getOrder ?>()-1)
      ->setComment(__METHOD__)
      ->count($con) > 0;
  }

  /**
   * Gets previous sibling for the given node if it exists
   *
   * @param      PropelPDO $con Connection to use.
   * @return     mixed 		Propel object if exists else false
   */
  public function getPrevSibling(PropelPDO $con = null)
  {
    if (!$this->isValid()) {
      return null;
    }

    return <?php echo $phpName ?>Query::create()
      >filterByParentUrl($this-><?php echo $getParentPath ?>())
        ->filterBy<?php echo $Level ?>($this-><?php echo $getLevel ?>())
        ->filterBy<?php echo $Order ?>($this-><?php echo $getOrder ?>()-1)
        ->setComment(__METHOD__)
        ->findOne($con);
  }

  /**
   * Determines if the node has next sibling
   *
   * @param      PropelPDO $con Connection to use.
   * @return     bool
   */
  public function hasNextSibling(PropelPDO $con = null)
  {
    if (!$this->isValid()) {
      return false;
    }

    return <?php echo $phpName ?>Query::create()
      ->siblingsOf($this)
      ->filterBy<?php echo $Order ?>($this-><?php echo $getOrder ?>()+1)
      ->setComment(__METHOD__)
      ->count($con) > 0;
  }

  /**
   * Gets next sibling for the given node if it exists
   *
   * @param      PropelPDO $con Connection to use.
   * @return     mixed 		Propel object if exists else false
   */
  public function getNextSibling(PropelPDO $con = null)
  {
    if (!$this->isValid()) {
      return null;
    }

    return <?php echo $phpName ?>Query::create()
      ->siblingsOf($this)
      ->filterBy<?php echo $Order ?>($this-><?php echo $getOrder ?>()+1)
      ->setComment(__METHOD__)
      ->findOne($con);
  }

  /**
   * Clears out the $collMaterializedPathChildren collection
   *
   * This does not modify the database; however, it will remove any associated objects, causing
   * them to be refetched by subsequent calls to accessor method.
   *
   * @return     void
   */
  public function clearMaterializedPathChildren()
  {
    $this->collMaterializedPathChildrenFull = false;
    $this->collMaterializedPathChildren = null;
  }

  /**
   * Initializes the $collMaterializedPathChildren collection.
   *
   * @return     void
   */
  public function initMaterializedPathChildren()
  {
    $this->collMaterializedPathChildren = new PropelObjectCollection();
    $this->collMaterializedPathChildren->setModel('<?php echo $phpName ?>');
  }

  /**
   * Adds an element to the internal $collMaterializedPathChildren collection.
   * Beware that this doesn't insert a node in the tree.
   * This method is only used to facilitate children hydration.
   *
   * @param      <?php echo $phpName ?> $<?php echo $phpName, PHP_EOL ?>
   *
   * @return     void
   */
  public function addMaterializedPathChild($<?php echo $phpName ?>)
  {
    if ($this->collMaterializedPathChildren === null) {
      $this->initMaterializedPathChildren();
    }
    if (!$this->collMaterializedPathChildren->contains($<?php echo $phpName ?>)) { // only add it if the **same** object is not already associated
      $this->collMaterializedPathChildren[]= $<?php echo $phpName ?>;
      $<?php echo $phpName ?>->setParent($this);
    }
  }

  /**
   * Tests if node has children
   *
   * @param PropelPDO $con
   * @return     bool
   */
  public function hasChildren(PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create()
      ->childrenOf($this)
      ->setComment(__METHOD__)
      ->count($con);
  }

  /**
   * Gets the children of the given node
   *
   * @param      Criteria  $criteria Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     array     List of <?php echo $phpName ?> objects
   */
  public function getChildren($criteria = null, PropelPDO $con = null)
  {
    if (null === $this->collMaterializedPathChildren || null !== $criteria) {
      if ($this->isNew() && null === $this->collMaterializedPathChildren) {
        // return empty collection
        $this->initMaterializedPathChildren();
      } else {
        $collMaterializedPathChildren = <?php echo $phpName ?>Query::create(null, $criteria)
          ->childrenOf($this)
          ->orderBy<?php echo $Order ?>()
          ->setComment(__METHOD__)
          ->find($con);
        if (null !== $criteria) {
          return $collMaterializedPathChildren;
        }
        $this->collMaterializedPathChildrenFull = true;
        $this->collMaterializedPathChildren = $collMaterializedPathChildren;
      }
    }

    return $this->collMaterializedPathChildren;
  }

  /**
   * Gets number of children for the given node
   *
   * @param      Criteria  $criteria Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     int       Number of children
   */
  public function countChildren($criteria = null, PropelPDO $con = null)
  {
    if (null === $this->collMaterializedPathChildren || null !== $criteria || !$this->collMaterializedPathChildrenFull) {
      if ($this->isNew() && null === $this->collMaterializedPathChildren) {
        return 0;
      } else {
        return <?php echo $phpName ?>Query::create(null, $criteria)
          ->childrenOf($this)
          ->setComment(__METHOD__)
          ->count($con);
      }
    } else {
      return count($this->collMaterializedPathChildren);
    }
  }

  /**
   * Gets the first child of the given node
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     <?php echo $phpName ?> first child object
   */
  public function getFirstChild($query = null, PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create(null, $query)
      ->childrenOf($this)
      ->orderBy<?php echo $Order ?>()
      ->setComment(__METHOD__)
      ->findOne($con);
  }

  /**
   * Gets the last child of the given node
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     array 		List of <?php echo $phpName ?> objects
   */
  public function getLastChild($query = null, PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create(null, $query)
      ->childrenOf($this)
      ->orderBy<?php echo $Order ?>(Criteria::DESC)
      ->setComment(__METHOD__)
      ->findOne($con);
  }

  /**
   * Gets the siblings of the given node
   *
   * @param      bool			$includeNode Whether to include the current node or not
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   *
   * @return     array 		List of <?php echo $phpName ?> objects
   */
  public function getSiblings($includeNode = false, $query = null, PropelPDO $con = null)
  {
    if ($this->isRoot()) {
      return array();
    } else {
      $query = <?php echo $phpName ?>Query::create(null, $query)
        ->siblingsOf($this)
        ->orderBy<?php echo $Order ?>()
        ->setComment(__METHOD__);

      if (!$includeNode) {
        $query->prune($this);
      }

      return $query->find($con);
    }
  }

  /**
   * Gets descendants for the given node
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     array 		List of <?php echo $phpName ?> objects
   */
  public function getDescendants($query = null, PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create(null, $query)
      ->descendantsOf($this)
      ->orderByBranch()
      ->setComment(__METHOD__)
      ->find($con);
  }

  /**
   * Gets number of descendants for the given node
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     int 		Number of descendants
   */
  public function countDescendants($query = null, PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create(null, $query)
      ->descendantsOf($this)
      ->setComment(__METHOD__)
      ->count($con);
  }

  /**
   * Gets descendants for the given node, plus the current node
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     array 		List of <?php echo $phpName ?> objects
   */
  public function getBranch($query = null, PropelPDO $con = null)
  {
    return <?php echo $phpName ?>Query::create(null, $query)
      ->branchOf($this)
      ->orderByBranch()
      ->setComment(__METHOD__)
      ->find($con);
  }

  /**
   * Gets ancestors for the given node, starting with the root node
   * Use it for breadcrumb paths for instance
   *
   * @param      Criteria $query Criteria to filter results.
   * @param      PropelPDO $con Connection to use.
   * @return     array 		List of <?php echo $phpName ?> objects
   */
  public function getAncestors($query = null, PropelPDO $con = null)
  {
    if ($this->isRoot()) {
      // save one query
      return array();
    } else {
      return <?php echo $phpName ?>Query::create(null, $query)
        ->ancestorsOf($this)
        ->orderByBranch()
        ->setComment(__METHOD__)
        ->find($con);
    }
  }

  /**
   * Inserts the given $child node as first child of current
   * The modifications in the current object and the tree
   * are not persisted until the child object is saved.
   *
   * @param <?php echo $phpName ?> $child Propel object for child node
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function addChild(<?php echo $phpName ?> $child)
  {
    if ($this->isNew()) {
      throw new PropelException('A <?php echo $phpName ?> object must not be new to accept children.');
    }
    $child->insertAsFirstChildOf($this);

    return $this;
  }

  /**
   * Inserts the current node as first child of given $parent node
   * The modifications in the current object and the tree
   * are not persisted until the current object is saved.
   *
   * @param      <?php echo $phpName ?> $parent  Propel object for parent node
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function insertAsFirstChildOf($parent)
  {
    if ($this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must not already be in the tree to be inserted. Use the moveToFirstChildOf() instead.');
    }

    // Update node properties
    $this-><?php echo $setLevel ?>($parent-><?php echo $getLevel ?>()+1);
    $this-><?php echo $setPath ?>($parent-><?php echo $getPath ?>().<?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER.$this-><?php echo $getCrumb ?>());
    $this-><?php echo $setOrder ?>(0);
    // update the children collection of the parent
    $parent->addMaterializedPathChild($this);

    // Keep the tree modification query for the save() transaction
    $this->materializedPathQueries []= array(
      'callable'  => array('<?php echo $peer ?>', 'makeNodeFirstChild'),
      'arguments' => array($parent, $this->isNew() ? null : $this)
    );

    return $this;
  }

  /**
   * Inserts the current node as last child of given $parent node
   * The modifications in the current object and the tree
   * are not persisted until the current object is saved.
   *
   * @param      <?php echo $phpName ?> $parent  Propel object for parent node
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function insertAsLastChildOf($parent)
  {
    if ($this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must not already be in the tree to be inserted. Use the moveToLastChildOf() instead.');
    }
    // Update node properties
    $this-><?php echo $setLevel ?>($parent-><?php echo $getLevel ?>() + 1);
    $this-><?php echo $setPath ?>($parent-><?php echo $getPath ?>().<?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER.$this-><?php echo $getCrumb ?>());
    // update the children collection of the parent
    $parent->addMaterializedPathChild($this);

    // Keep the tree modification query for the save() transaction
    $this->materializedPathQueries[]= array(
      'callable'  => array('<?php echo $peer ?>', 'makeNodeLastChild'),
      'arguments' => array($parent, $this)
    );

    return $this;
  }

  /**
   * Inserts the current node as prev sibling given $sibling node
   * The modifications in the current object and the tree
   * are not persisted until the current object is saved.
   *
   * @param      <?php echo $phpName ?> $sibling  Propel object for parent node
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function insertAsPrevSiblingOf($sibling)
  {
    if ($this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must not already be in the tree to be inserted. Use the moveToPrevSiblingOf() instead.');
    }
    // Update node properties
    $this-><?php echo $setLevel ?>($sibling-><?php echo $getLevel ?>());
    $this-><?php echo $setPath ?>($sibling-><?php echo $getParentPath ?>() . <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER . $this-><?php echo $getCrumb ?>());
    $this-><?php echo $setOrder ?>($sibling-><?php echo $getOrder ?>());
    // Keep the tree modification query for the save() transaction
    $this->materializedPathQueries[]= array(
      'callable'  => array('<?php echo $peer ?>', 'insertNodeBefore'),
      'arguments' => array($sibling, $this)
    );

    return $this;
  }

  /**
   * Inserts the current node as next sibling given $sibling node
   * The modifications in the current object and the tree
   * are not persisted until the current object is saved.
   *
   * @param      <?php echo $phpName ?> $sibling  Propel object for parent node
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function insertAsNextSiblingOf($sibling)
  {
    if ($this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must not already be in the tree to be inserted. Use the moveToNextSiblingOf() instead.');
    }
    // Update node properties
    $this-><?php echo $setLevel ?>($sibling-><?php echo $getLevel ?>());
    $this-><?php echo $setPath ?>($sibling-><?php echo $getParentPath ?>() . <?php echo $peer ?>::MATERIALIZED_PATH_DELIMITER . $this-><?php echo $getCrumb ?>());
    $this-><?php echo $setOrder ?>($sibling-><?php echo $getOrder ?>()+1);
    // Keep the tree modification query for the save() transaction
    $this->materializedPathQueries[]= array(
      'callable'  => array('<?php echo $peer ?>', 'insertNodeAfter'),
      'arguments' => array($sibling, $this)
    );

    return $this;
  }

  /**
   * Moves current node and its subtree to be the first child of $parent
   * The modifications in the current object and the tree are immediate
   *
   * @param      <?php echo $phpName ?> $parent  Propel object for parent node
   * @param      PropelPDO $con  Connection to use.
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function moveToFirstChildOf($parent, PropelPDO $con = null)
  {
    if (!$this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must be already in the tree to be moved. Use the insertAsFirstChildOf() instead.');
    }
    if ($parent->isDescendantOf($this)) {
      throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
    }

    $this->moveSubtreeTo('first-child-of', $parent, $con);

    return $this;
  }

  /**
   * Moves current node and its subtree to be the last child of $parent
   * The modifications in the current object and the tree are immediate
   *
   * @param      <?php echo $phpName ?> $parent  Propel object for parent node
   * @param      PropelPDO $con  Connection to use.
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function moveToLastChildOf($parent, PropelPDO $con = null)
  {
    if (!$this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must be already in the tree to be moved. Use the insertAsLastChildOf() instead.');
    }
    if ($parent->isDescendantOf($this)) {
      throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
    }

    $this->moveSubtreeTo('last-child-of', $parent, $con);

    return $this;
  }

  /**
   * Moves current node and its subtree to be the previous sibling of $sibling
   * The modifications in the current object and the tree are immediate
   *
   * @param      <?php echo $phpName ?> $sibling  Propel object for sibling node
   * @param      PropelPDO $con  Connection to use.
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function moveToPrevSiblingOf($sibling, PropelPDO $con = null)
  {
    if (!$this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must be already in the tree to be moved. Use the insertAsPrevSiblingOf() instead.');
    }
    if ($sibling->isRoot()) {
      throw new PropelException('Cannot move to previous sibling of a root node.');
    }
    if ($sibling->isDescendantOf($this)) {
      throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
    }

    $this->moveSubtreeTo('before', $sibling, $con);

    return $this;
  }

  /**
   * Moves current node and its subtree to be the next sibling of $sibling
   * The modifications in the current object and the tree are immediate
   *
   * @param      <?php echo $phpName ?> $sibling  Propel object for sibling node
   * @param      PropelPDO $con  Connection to use.
   *
   * @throws PropelException
   * @return     <?php echo $phpName ?> The current Propel object
   */
  public function moveToNextSiblingOf($sibling, PropelPDO $con = null)
  {
    if (!$this->isInTree()) {
      throw new PropelException('A <?php echo $phpName ?> object must be already in the tree to be moved. Use the insertAsNextSiblingOf() instead.');
    }
    if ($sibling->isRoot()) {
      throw new PropelException('Cannot move to next sibling of a root node.');
    }
    if ($sibling->isDescendantOf($this)) {
      throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
    }

    $this->moveSubtreeTo('after', $sibling, $con);

    return $this;
  }

  /**
   * Move current node and its children to defined location and updates rest of tree
   *
   * @param      string  $position position
   * @param      <?php echo $phpName ?>  $pivot pivot node
   * @param      PropelPDO $con    Connection to use.
   * @throws PropelException
   * @return bool
   */
  protected function moveSubtreeTo($position, $pivot, PropelPDO $con = null)
  {
    if ($con === null) {
      $con = Propel::getConnection(<?php echo $peer ?>::DATABASE_NAME, Propel::CONNECTION_WRITE);
    }

    $moveNewSiblingsRight = true;

    $con->beginTransaction();
    switch ($position)
    {
      case 'first-child-of':
        $newParent<?php echo $Url ?> = $pivot-><?php echo $getPath ?>();
        $new<?php echo $Level ?> = $pivot-><?php echo $getLevel ?>()+1;
        $new<?php echo $Order ?> = 0;
        break;
      case 'last-child-of':
        $newParent<?php echo $Url ?> = $pivot-><?php echo $getPath ?>();
        $new<?php echo $Level ?> = $pivot-><?php echo $getLevel ?>()+1;
        $new<?php echo $Order ?> = $pivot->countChildren(null, $con); // last
        $moveNewSiblingsRight = false; // there is no new siblings that should be moved - node will be last
        break;
      case 'before':
        $newParent<?php echo $Url ?> = $pivot-><?php echo $getParentPath ?>();
        $new<?php echo $Level ?> = $pivot-><?php echo $getLevel ?>();
        $new<?php echo $Order ?> = $pivot-><?php echo $getOrder ?>();
        break;
      case 'after':
        $newParent<?php echo $Url ?> = $pivot-><?php echo $getParentPath ?>();
        $new<?php echo $Level ?> = $pivot-><?php echo $getLevel ?>();
        $new<?php echo $Order ?> = $pivot-><?php echo $getOrder ?>()+1;
        break;
      default:
        throw new PropelException('Unknown position for moving subtree: '.$position);
    }

    if ($this-><?php echo $getParentPath ?>() == $newParent<?php echo $Url ?> && $this-><?php echo $getOrder ?>() == $new<?php echo $Order ?>)
    {
      // nothing to do
      return true;
    }

    try {

      if ($this-><?php echo $getParentPath ?>() == $newParent<?php echo $Url ?>) // just reorder
      {
        if ($new<?php echo $Order ?> < $this-><?php echo $getOrder ?>()) // move node left
        {
          // shift others right
          <?php echo $peer ?>::shiftNodes($con, $newParent<?php echo $Url ?>, $new<?php echo $Level ?>, $new<?php echo $Order ?>, $this-><?php echo $getOrder ?>() - 1, +1);
        }
        elseif ($new<?php echo $Order ?> > $this-><?php echo $getOrder ?>()) // move node right
        {
          // shift others left
          <?php echo $peer ?>::shiftNodes($con, $newParent<?php echo $Url ?>, $new<?php echo $Level ?>, $this-><?php echo $getOrder ?>()+1, $new<?php echo $Order ?>, -1);
        }
        // update current node order_number
        <?php echo $peer ?>::updateNode($con, $this, array(
          <?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL => $new<?php echo $Order ?>
        ));
      }
      else // full moving
      {
        // shift source siblings left
        <?php echo $peer ?>::shiftNodes($con, $this-><?php echo $getParentPath ?>(), $this-><?php echo $getLevel ?>(), $this-><?php echo $getOrder ?>(), null, -1);

        if ($moveNewSiblingsRight)
        {
          // shift destination siblings right
          <?php echo $peer ?>::shiftNodes($con, $newParent<?php echo $Url ?>, $new<?php echo $Level ?>, $new<?php echo $Order ?>);
        }

        // update current node order_number
        <?php echo $peer ?>::updateNode($con, $this, array(
          <?php echo $peer ?>::MATERIALIZED_PATH_ORDER_COL => $new<?php echo $Order ?>
        ));

        // update urls and levels
        <?php echo $peer ?>::moveSubtree($con, $this, $newParent<?php echo $Url ?>, $new<?php echo $Level ?>);
      }

      // update all loaded nodes
      <?php echo $peer ?>::updateLoadedNodes(null, $con);

      $con->commit();
    } catch (PropelException $e) {
      $con->rollback();
      throw $e;
    }
  }

  /**
   * Deletes all descendants for the given node
   * Instance pooling is wiped out by this command,
   * so existing <?php echo $phpName ?> instances are probably invalid (except for the current one)
   *
   * @param      PropelPDO $con Connection to use.
   *
   * @return     int 		number of deleted nodes
   */
  public function deleteDescendants(PropelPDO $con = null)
  {
    if ($con === null) {
      $con = Propel::getConnection(<?php echo $peer ?>::DATABASE_NAME, Propel::CONNECTION_READ);
    }

    // delete descendant nodes (will empty the instance pool)
    $ret = <?php echo $phpName ?>Query::create()
      ->descendantsOf($this)
      ->setComment(__METHOD__)
      ->delete($con);

    return $ret;
  }

  /**
   * Returns a pre-order iterator for this node and its children.
   *
   * @return     RecursiveIterator
   */
  public function getIterator()
  {
    return new AxisMaterializedPathRecursiveIterator($this);
  }

  public function preSave(PropelPDO $con = null)
  {
    if ($ret = parent::preSave($con))
    {
      // nested_set behavior
      if ($this->isNew() && $this->isRoot()) {
        // check if no other root exist in, the tree
        $roots = <?php echo $phpName ?>Query::create()
          ->addUsingAlias(<?php echo $peer ?>::MATERIALIZED_PATH_LEVEL_COL, 1, Criteria::EQUAL)
          ->setComment(__METHOD__)
          ->count($con);
        if ($roots > 0) {
          throw new PropelException('A root node already exists in this tree.');
        }
      }
      $this->processMaterializedPathQueries($con);
    }
    return $ret;
  }

  public function preDelete(PropelPDO $con = null)
  {
    if ($ret = parent::preDelete($con))
    {
      if ($this->isRoot()) {
        throw new PropelException('Deletion of a root node is disabled for materialized path tree. Use <?php echo $peer ?>::deleteTree() instead to delete an entire tree');
      }

      if ($this->isInTree()) {
        $this->deleteDescendants($con);
      }
    }
    return $ret;
  }

  public function postDelete(PropelPDO $con = null)
  {
    parent::postDelete($con);
    // shift next siblings left
    <?php echo $peer ?>::shiftNodes($con, $this-><?php echo $getParentPath ?>(), $this-><?php echo $getLevel ?>(), $this-><?php echo $getOrder ?>(), null, -1);
    <?php echo $peer ?>::updateLoadedNodes($this, $con);
  }

  /**
   * Execute queries that were saved to be run inside the save transaction
   */
  protected function processMaterializedPathQueries(PropelPDO $con)
  {
    foreach ($this->materializedPathQueries as $query) {
      array_unshift($query['arguments'], $con);
      call_user_func_array($query['callable'], $query['arguments']);
    }
    $this->materializedPathQueries = array();
  }

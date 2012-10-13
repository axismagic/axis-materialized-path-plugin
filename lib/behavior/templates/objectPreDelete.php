      if ($this->isRoot()) {
        throw new PropelException('Deletion of a root node is disabled for materialized path tree. Use <?php echo $peer ?>::deleteTree() instead to delete an entire tree');
      }

      if ($this->isInTree()) {
        $this->deleteDescendants($con);
      }
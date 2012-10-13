    // shift next siblings left
    <?php echo $peer ?>::shiftNodes($con, $this-><?php echo $getParentPath ?>(), $this-><?php echo $getLevel ?>(), $this-><?php echo $getOrder ?>(), null, -1);
    <?php echo $peer ?>::updateLoadedNodes($this, $con);

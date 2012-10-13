const MATERIALIZED_PATH_DELIMITER = <?php echo var_export($delimiter, true) ?>;

const MATERIALIZED_PATH_LEVEL_COL = self::<?php echo $levelColumnConstName ?>;
const MATERIALIZED_PATH_PATH_COL = self::<?php echo $pathColumnConstName ?>;
const MATERIALIZED_PATH_CRUMB_COL = self::<?php echo $crumbColumnConstName ?>;
const MATERIALIZED_PATH_ORDER_COL = self::<?php echo $orderColumnConstName ?>;
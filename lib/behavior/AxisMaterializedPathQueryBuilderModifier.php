<?php
/**
 * Date: 13.10.12
 * Time: 3:44
 * Author: Ivan Voskoboynyk
 */
class AxisMaterializedPathQueryBuilderModifier
{
  /**
   * @var AxisMaterializedPathBehavior
   */
  protected $behavior;
  /**
   * @var Table
   */
  protected $table;
//  protected $builder;

  public function __construct($behavior)
  {
    $this->behavior = $behavior;
    $this->table = $behavior->getTable();
  }

  public function queryMethods()
  {
    return $this->behavior->renderTemplate('queryMethods', array(
      'phpName' => $this->table->getPhpName(),
      'peer' => $this->table->getPhpName().'Peer',
      'query' => $this->table->getPhpName().'Query',

      'Path' => $this->behavior->getPathColumn()->getPhpName(),
      'Level' => $this->behavior->getLevelColumn()->getPhpName(),
      'Crumb' => $this->behavior->getCrumbColumn()->getPhpName(),
      'Order' => $this->behavior->getOrderColumn()->getPhpName(),

      'getParentPath' => 'getParent'.$this->behavior->getPathColumn()->getPhpName(),
      'getPath' => 'get'.$this->behavior->getPathColumn()->getPhpName(),
      'setPath' => 'set'.$this->behavior->getPathColumn()->getPhpName(),
      'getLevel' => 'get'.$this->behavior->getLevelColumn()->getPhpName(),
      'setLevel' => 'set'.$this->behavior->getLevelColumn()->getPhpName(),
      'getOrder' => 'get'.$this->behavior->getOrderColumn()->getPhpName(),
      'setOrder' => 'set'.$this->behavior->getOrderColumn()->getPhpName(),

      'pathColumn' => $this->behavior->getPathColumn(),
      'levelColumn' => $this->behavior->getLevelColumn()
    ));
  }
}

<?php
/**
 * Date: 13.10.12
 * Time: 3:45
 * Author: Ivan Voskoboynyk
 */
class AxisMaterializedPathPeerBuilderModifier
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

  public function staticConstants()
  {
    return $this->behavior->renderTemplate('peerAttributes', array(
      'delimiter' => $this->behavior->getDelimiter(),
      'levelColumnConstName' => $this->behavior->getLevelColumn()->getConstantColumnName(),
      'crumbColumnConstName' => $this->behavior->getCrumbColumn()->getConstantColumnName(),
      'orderColumnConstName' => $this->behavior->getOrderColumn()->getConstantColumnName(),
      'pathColumnConstName' => $this->behavior->getPathColumn()->getConstantColumnName(),
    ));
  }

  public function staticMethods()
  {
    return $this->behavior->renderTemplate('peerMethods', $this->behavior->getTemplateVars());
  }
}

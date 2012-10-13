<?php
/**
 * Date: 10.10.12
 * Time: 2:12
 * Author: Ivan Voskoboynyk
 */
class AxisMaterializedPathBehavior extends Behavior
{
  protected $parameters = array(
    'path_column' => 'url',
    'crumb_column' => 'slug',
    'delimiter' => '/',
    'level_column' => 'level',
    'order_column' => 'order_number',
  );

  protected
    $objectBuilderModifier,
    $queryBuilderModifier,
    $peerBuilderModifier;

  /**
   * @param string $configKey
   * @return Column
   */
  protected function getConfiguredColumn($configKey)
  {
    $name = $this->getParameter($configKey);
    return $this->getTable()->getColumn($name);
  }

  /**
   * @return Column
   */
  public function getPathColumn()
  {
    return $this->getConfiguredColumn('path_column');
  }

  /**
   * @return Column
   */
  public function getOrderColumn()
  {
    return $this->getConfiguredColumn('order_column');
  }

  /**
   * @return Column
   */
  public function getLevelColumn()
  {
    return $this->getConfiguredColumn('level_column');
  }

  /**
   * @return Column
   */
  public function getCrumbColumn()
  {
    return $this->getConfiguredColumn('crumb_column');
  }

  /**
   * @return Column
   */
  public function getSlugColumn()
  {
    return $this->getConfiguredColumn('slug_column');
  }

  /**
   * @return string
   */
  public function getDelimiter()
  {
    return $this->getParameter('delimiter');
  }

  /**
   * @return AxisMaterializedPathPeerBuilderModifier
   */
  public function getPeerBuilderModifier()
  {
    if (is_null($this->peerBuilderModifier)) {
      $this->peerBuilderModifier = new AxisMaterializedPathPeerBuilderModifier($this);
    }

    return $this->peerBuilderModifier;
  }

  /**
   * @return AxisMaterializedPathQueryBuilderModifier
   */
  public function getQueryBuilderModifier()
  {
    if (is_null($this->queryBuilderModifier)) {
      $this->queryBuilderModifier = new AxisMaterializedPathQueryBuilderModifier($this);
    }

    return $this->queryBuilderModifier;
  }

  public function getObjectBuilderModifier()
  {
    if (is_null($this->objectBuilderModifier)) {
      $this->objectBuilderModifier = new AxisMaterializedPathObjectBuilderModifier($this);
    }

    return $this->objectBuilderModifier;
  }

  public function getTemplateVars()
  {
    return array(
      'phpName' => $this->getTable()->getPhpName(),
      'peer' => $this->getTable()->getPhpName().'Peer',
      'query' => $this->getTable()->getPhpName().'Query',

      'Path' => $this->getPathColumn()->getPhpName(),
      'Level' => $this->getLevelColumn()->getPhpName(),
      'Crumb' => $this->getCrumbColumn()->getPhpName(),
      'Order' => $this->getOrderColumn()->getPhpName(),

      'getParentPath' => 'getParent'.$this->getPathColumn()->getPhpName(),
      'setParentPath' => 'setParent'.$this->getPathColumn()->getPhpName(),

      'getPath' => 'get'.$this->getPathColumn()->getPhpName(),
      'setPath' => 'set'.$this->getPathColumn()->getPhpName(),
      'getCrumb' => 'get'.$this->getCrumbColumn()->getPhpName(),
      'setCrumb' => 'set'.$this->getCrumbColumn()->getPhpName(),
      'getLevel' => 'get'.$this->getLevelColumn()->getPhpName(),
      'setLevel' => 'set'.$this->getLevelColumn()->getPhpName(),
      'getOrder' => 'get'.$this->getOrderColumn()->getPhpName(),
      'setOrder' => 'set'.$this->getOrderColumn()->getPhpName(),

      'pathColumn' => $this->getPathColumn(),
      'levelColumn' => $this->getLevelColumn()
    );
  }
}

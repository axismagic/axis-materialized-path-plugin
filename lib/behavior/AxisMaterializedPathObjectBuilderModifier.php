<?php
/**
 * Date: 13.10.12
 * Time: 3:45
 * Author: Ivan Voskoboynyk
 */
class AxisMaterializedPathObjectBuilderModifier
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

  public function objectAttributes()
  {
    return $this->behavior->renderTemplate('objectAttributes', $this->behavior->getTemplateVars());
  }

  public function objectMethods()
  {
    return $this->behavior->renderTemplate('objectMethods', $this->behavior->getTemplateVars());
  }

  public function preSave()
  {
    return $this->behavior->renderTemplate('objectPreSave', $this->behavior->getTemplateVars());
  }

  public function preDelete()
  {
    return $this->behavior->renderTemplate('objectPreDelete', $this->behavior->getTemplateVars());
  }

  public function postDelete()
  {
    return $this->behavior->renderTemplate('objectPostDelete', $this->behavior->getTemplateVars());
  }

  public function objectFilter(&$script)
  {
    $peer = $this->table->getPhpName().'Peer';
    $Crumb = $this->behavior->getCrumbColumn()->getPhpName();

    $setCrumbInjection = <<<PHP
    // injected by axis_materialized_path behavior
    if (strpos(\$v, $peer::MATERIALIZED_PATH_DELIMITER) !== FALSE)
    {
      throw new PropelException('$Crumb cannot contain delimiter');
    }
PHP;

    $clearReferencesInjection = <<<PHP

      // injected by axis_materialized_path_behavior
	    \$this->clearMaterializedPathChildren();
	    \$this->aMaterializedPathParent = null;

PHP;


    $class = new sfClassManipulator('<?php'.$script);

    $class->wrapMethod('set'.$this->behavior->getCrumbColumn()->getPhpName(), $setCrumbInjection);
    $class->wrapMethod('clearAllReferences', '', $clearReferencesInjection);

    $script = substr($class->getCode(), 5);
  }
}

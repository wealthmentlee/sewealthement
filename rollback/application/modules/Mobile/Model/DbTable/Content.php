<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Content.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Model_DbTable_Content extends Engine_Db_Table
{
  protected $_serializedColumns = array('params');
  
  /*
  //protected $_name = 'content';

  protected $_rowClass = 'Engine_Db_Table_Row';


  protected $_blockOrder = array(
    'before' => 1,
    'left' => 2,
    'right' => 3,
    'middle' => 4,
    'main' => 4,
    'after' => 5
  );
  
  public function loadAreas($sources)
  {
    $sources = (array) $sources;
    $select = $this->select();
    
    foreach( $sources as $source )
    {
      $select->orWhere('source = ?', $source);
    }
    
    return $this->fetchAll($select);
  }

  public function loadContentElement(Engine_Content $content, $name)
  {
    // Already loaded
    if( isset($content->$name) )
    {
      return;
    }
    
    // Load
    //$elements = $this->fetchAll($this->select()->where('source LIKE ?', str_replace('_', '.', $name).'%'));
    $elements = $this->fetchAll($this->select()->where('source LIKE ?', $name.'%')->order('order ASC'));
    
    foreach( $elements as $element )
    {
      list($page, $area, $block) = explode('.', $element->source);

      $current = $content;

      // Add main
      if( !isset($current->$page) )
      {
        $current->addElement('Container', $page);
      }
      $current = $content->$page;

      // Add area
      if( !isset($current->$area) )
      {
        $current->addElement('Container', $area);
      }
      $current = $current->$area;

      // Add block
      if( !isset($current->$block) )
      {
        $current->addElement('Container', $block, array(
          'order' => @$this->_blockOrder[$block]
        ));
      }
      $current = $current->$block;

      // Add cell
      $cellName = $element->name;
      $cellParams = $content->getSupportedCell($cellName);
      if( !isset($current->$cellName) && $cellParams )
      {
        $cellParams['order'] = $element->order;
        $cellParams['params'] = (array) $element->params;
        $current->addElement(ucfirst($cellParams['type']), $cellName, $cellParams);
      }
    }
  }
   */
}
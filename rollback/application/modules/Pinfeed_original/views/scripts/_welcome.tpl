<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _welcome.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<div class="wall_welcome_widgets">

<?php

  $page_table = Engine_Api::_()->getDbTable('pages', 'core');
  $content_table = Engine_Api::_()->getDbTable('content', 'core');

  $select = $content_table->select()
      ->from(array('p' => $page_table->info('name')), array('c.content_id'))
      ->joinLeft(array('c' => $content_table->info('name')), 'c.page_id = p.page_id AND c.name = "middle"', array())
      ->where('p.name = ?', 'wall_index_welcome')
      ;

  $content_id = 0;
  $content = $content_table->fetchRow($select);
  if ($content){
    $content_id = $content->content_id;
  }

  $select = $content_table->select()
      ->where('parent_content_id = ?', $content_id)
      ->order('order ASC')
      ;

  $widgets = $content_table->fetchAll($select);

  foreach ($widgets as $widget){

      try {

        $page_id = $widget->page_id;
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $content = $content_table->fetchAll($content_table->select()->where('page_id = ?', $page_id));
        $structure = $page_table->createElementParams($widget);
        $children = $page_table->prepareContentArea($content, $widget);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        //$structure['request'] = $this->getRequest();
        //$structure['action'] = $view;

        if (!Engine_Api::_()->wall()->checkWidgetIsEnabled($structure['name'])){
          continue ;
        }

        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

  /*      if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }*/

        $content = $element->render();

        echo $content;

      } catch (Exception $e){

      }

  }



?>

</div>
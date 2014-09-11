<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Categories.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Blog_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Blog_Model_Category';
  
  public function getCategoriesAssoc()
  {
    $stmt = $this->select()
        ->from($this, array('category_id', 'category_name'))
        ->order('category_name ASC')
        ->query();
    
    $data = array();
    foreach( $stmt->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['category_name'];
    }
    
    return $data;
  }
  
  public function getUserCategoriesAssoc($user)
  {
    if( $user instanceof User_Model_User ) {
      $user = $user->getIdentity();
    } else if( !is_numeric($user) ) {
      return array();
    }
    
    $stmt = $this->getAdapter()
        ->select()
        ->from('engine4_blog_categories', array('category_id', 'category_name'))
        ->joinLeft('engine4_blog_blogs', "engine4_blog_blogs.category_id = engine4_blog_categories.category_id")
        ->group("engine4_blog_categories.category_id")
        ->where('engine4_blog_blogs.owner_id = ?', $user)
        ->where('engine4_blog_blogs.draft = ?', "0")
        ->order('category_name ASC')
        ->query();
    
    $data = array();
    foreach( $stmt->fetchAll() as $category ) {
      $data[$category['category_id']] = $category['category_name'];
    }
    
    return $data;
  }
}

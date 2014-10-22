<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Review.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_Pagereview extends Core_Model_Item_Abstract
{
  protected $_owner_type = 'user';
  protected $_type = 'pagereview';
  protected $_parent_type = 'page';
  protected $_searchColumns = array('title', 'body');

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',
      'page_id' => $this->getPage()->url,
      'content' => 'review',
      'content_id' => $this->getIdentity(),
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }

  public function getLink()
	{
		return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
	}

  public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

  public function delete(){

    // Delete Votes
    $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $tbl->delete(array(
      'review_id = ?' => $this->getIdentity()
    ));

    $tbl_search = Engine_Api::_()->getDbTable('search', 'page');
    $tbl_search->delete(array(
      'object = ?' => 'pagereview',
      'object_id = ?' => $this->getIdentity()
    ));

    // Delete Actions
    $tbl = Engine_Api::_()->getDbTable('attachments', 'activity');
    $action_ids = $tbl->select()
        ->from($tbl->info('name'), 'action_id')
        ->where('type = ?', $this->getType())
        ->where('id = ?', $this->getIdentity())
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $tbl->delete(array(
      'type = ?' => $this->getType(),
      'id = ?' => $this->getIdentity()
    ));

    if ($action_ids){
      Engine_Api::_()->getDbTable('actions', 'activity')->delete(array(
        'action_id IN (?)' => $action_ids
      ));
    }

    return parent::delete();
  }

  public function getAuthorizationItem()
  {
    return $this->getPage('page');
  }

  public function getDescription()
  {
    $tmpBody = Engine_String::strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }


}
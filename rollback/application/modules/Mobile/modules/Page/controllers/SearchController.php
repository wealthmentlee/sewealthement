<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SearchController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_SearchController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this->view->content = $content = $this->_getParam('content');
		$this->view->keyword = $keyword = trim(Engine_String::strip_tags($this->_getParam('keyword')));
		$this->view->page_id = $page_id = $this->_getParam('page_id');
	}

	public function indexAction()
	{
		$page_id = $this->_getParam('page_id');
		$keyword = trim(Engine_String::strip_tags($this->_getParam('keyword')));

		if (!$page_id || !$keyword){
			$this->view->html = false;
			return ;
		}

		$api = Engine_Api::_()->getDbTable('search', 'page');
		$apiTag = Engine_Api::_()->getDbTable('tags', 'page');
		
		$params = array('page_id' => $page_id, 'keyword' => $keyword);
		$this->view->items = $items = $api->getItems($params, true);

		$params = array('page_id' => $page_id, 'keyword' => $keyword, 'group' => $apiTag->info('name').'.tag_id');
		$this->view->tags = $tags = $apiTag->getPaginator($params);
		$this->view->tags->setItemCountPerPage(4);

		if (!count($items)){
			$this->view->html = false;
			$this->view->tab_html = "<ul class='form-errors'><li>".$this->view->translate('There is no items matching your criteria.')."</li></ul>	";
			return ;
		}

		foreach ($items as $key => $paginator){
			$paginator->setItemCountPerPage(2);
		}

		$this->view->html = $this->view->render('_searchItems.tpl');

		foreach ($items as $key => $paginator){
			$paginator->setItemCountPerPage(100);
		}

		$this->view->tab_html = $this->view->render('_searchTab.tpl');
	}

	public function tagAction()
	{
		$params = array(
			'page_id' => (int)$this->_getParam('page_id'),
			'tag_id' => (int)$this->_getParam('tag_id'),
		);

		$api = Engine_Api::_()->getDbTable('tags', 'page');
		$data = $api->getItems($params);

		$this->view->items = $items = $data['data'];
		$this->view->tag = $tag = $data['tag'];
		
		foreach ($items as $key => $paginator){
			$paginator->setItemCountPerPage(100);
		}

		$this->view->html = $this->view->render('_tagTab.tpl');
	}
}
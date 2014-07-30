<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Pageblog_IndexController extends Core_Controller_Action_Standard
{
	protected $params;
	protected $_subject;
	
  public function init()
  {
    $page_id = (int)$this->_getParam('page_id');
    $subject = null;
    $navigation = new Zend_Navigation();

    if ($page_id){
      $subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
    }

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)){
      $subject = null;
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject){

      Engine_Api::_()->core()->setSubject($subject);

      $navigation->addPage(array(
        'label' => 'Browse Blogs',
        'route' => 'page_blog',
        'action' => 'index',
        'params' => array(
          'page_id' => $subject->getIdentity()
        )
      ));

      if ($subject->authorization()->isAllowed($viewer, 'posting')){

        $navigation->addPage(array(
          'label' => 'Manage Blogs',
          'route' => 'page_blog',
          'action' => 'manage',
          'params' => array(
            'page_id' => $subject->getIdentity()
          )
        ));

      }

    }

    $this->_subject = $this->view->subject = $subject;
    $this->view->navigation = $navigation;

  }


	public function indexAction() 
	{
    if (!$this->_subject){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }
		$table = $this->getTable();

    $params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => 10,
      'p' => $this->_getParam('page')
    );

		$this->view->paginator = $table->getBlogs($params);

	}
	
	public function manageAction()
	{
    if (!$this->_subject){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }
		$table = $this->getTable();

    $params = array(
      'page_id' => $this->_subject->getIdentity(),
      'ipp' => 10,
      'p' => $this->_getParam('page'),
      'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
    );

		$this->view->paginator = $table->getBlogs($params);
	}
	
	public function deleteAction()
	{
    $blog_id = (int)$this->_getParam('blog_id');

    $this->view->form = $form = new Engine_Form;

    $form->setTitle('Delete Blog')
      ->setDescription('Are you sure you want to delete this blog?')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $form->setAction($this->view->url(array(
      'action' => 'delete',
      'blog_id' => $blog_id,
      'return_url' => $this->_getParam('return_url')
    ), 'page_blog'));

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $table = $this->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    $blog = $table->findRow($blog_id);
    $subject = $blog->getParent();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->isSelf($blog->getOwner()) && !$subject->isTeamMember($viewer)){
      return ;
    }

    try
    {
      $search_api = Engine_Api::_()->getDbTable('search', 'page');
			$search_api->deleteData($blog);
      $blog->delete();
      $db->commit();
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $return_url = $this->view->url(array(
      'action' => 'manage',
      'page_id' => $subject->getIdentity()
    ), 'page_blog', true);

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => $this->view->translate('Blog was deleted.'),
      'return_url'=>$return_url,
    ));

	}
	
	public function viewAction()
  {
    $blog_id = (int)$this->_getParam('blog_id');
    $blog = $this->getTable()->findRow($blog_id);

    if (!$blog){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->subject = $blog->getParent();

    if ($viewer->getIdentity() != $blog->user_id){
      $blog->view_count++;
      $blog->save();
    }

    $this->view->blog = $blog;
    $this->view->owner = $blog->getOwner();
    
  }
	
	protected function getApi()
  {
  	return Engine_Api::_()->getApi('core', 'pageblog'); 
  }
	
	protected function getTable() 
	{
		return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
	}
	
}
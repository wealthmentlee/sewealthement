<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Article
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Article_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('article_id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('article', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('article');
    
    if (Engine_Api::_()->core()->hasSubject())
    {    
	    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
	      $subject,
	      Engine_Api::_()->user()->getViewer(),
	      'view'
	    );
    }
  }

  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->article = $article = Engine_Api::_()->core()->getSubject('article');

    //return $this->_forward('requireauth', 'error', 'core');
    // require log in --- and -- not logged in => show log in screen
    if ( !Engine_Api::_()->getApi('settings', 'core')->getSetting('article.public', 1) && !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // logged in && no view permission => show no permission
    if ( $this->_helper->requireUser()->checkRequire() && !$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->isValid()) {
      return;
    }
    else if (!$this->_helper->requireUser()->checkRequire()) {
      if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
        return $this->_forward('requireuser', 'error', 'core');
      }
    }
    //if (!$this->_helper->requireAuth()->setAuthParams($article, null, 'view')->checkRequire()) {
    //  return $this->_forward('requireauth', 'error', 'core');
   // }


    $this->view->owner = $owner = Engine_Api::_()->user()->getUser($article->owner_id);

    $this->view->canEdit = $this->_helper->requireAuth()->setAuthParams($article, null, 'edit')->checkRequire();
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    $this->view->canDelete = $this->_helper->requireAuth()->setAuthParams($article, null, 'delete')->checkRequire();
    $this->view->canPublish = $article->isOwner($viewer) && !$article->isPublished();

    $suggestEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest');

    $this->view->suggestUrl = '';

    if ($suggestEnabled){

      $router = Zend_Controller_Front::getInstance()->getRouter();
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Mobile" . DIRECTORY_SEPARATOR .
        "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

      $paramStr = '?m=suggest&l=getSuggestItems&nli=0&params[object_type]=article&params[object_id]='.$article->getIdentity() .
        '&action_url='.urlencode($router->assemble(array('action' => 'suggest'), 'suggest_general')) .
        '&params[suggest_type]=article&params[scriptpath]=' . $path;

      $this->view->suggestUrl = $router->assemble(array('controller' => 'index', 'action' => 'contacts', 'module' => 'hecore', 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'default', true) . $paramStr;

    }

    $archiveList = Engine_Api::_()->article()->getArchiveList(array('user_id'=>$article->owner_id,'published'=>1));

    $article->view_count++;
    $article->save();

    $this->view->article = $article;
    if ($article->photo_id)
    {
      $this->view->main_photo = $article->getPhoto($article->photo_id);
    }
    // get tags
    $this->view->articleTags = $article->tags()->getTagMaps();
    $this->view->userTags = $article->tags()->getTagsByTagger($article->getOwner());


    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($article);

    // album material
    $this->view->album = $album = $article->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('article.gallery', 4));

    if($article->category_id !=0) $this->view->category = Engine_Api::_()->article()->getCategory($article->category_id);
    $this->view->userCategories = Engine_Api::_()->article()->getUserCategories($this->view->article->owner_id);
  }
}
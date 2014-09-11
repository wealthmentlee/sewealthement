<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 2011-02-14 12:38:39 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
 
class Article_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('article_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($article_id = (int) $this->_getParam('article_id')) &&
          null !== ($article = Engine_Api::_()->getItem('article', $article_id)) )
      {
        Engine_Api::_()->core()->setSubject($article);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'article',
      'view' => 'article_photo',
    ));
  }

  public function listAction()
  {
    $this->view->article = $article = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $article->getSingletonAlbum();

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->owner = $owner = Engine_Api::_()->getItem('user', $article->owner_id);
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();

    $album->view_count++;
    $album->save();  
  }

  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->article = $article = $photo->getArticle();
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->canUpload = $this->_helper->requireAuth()->setAuthParams($article, null, 'photo')->checkRequire();
    
    $photo->view_count++;
    $photo->save();
  }
  

}
<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 12.06.12
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PageblogController
    extends Apptouch_Controller_Action_Bridge
{
    public function blogsBrowseAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('page', null, 'view')->isValid()) return;

        //Get settings
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //Get Params
        $params = $this->_request->getParams();
        $params['ipp'] = $settings->getSetting('pageblog.page', 10);
        $params['page'] = $this->_getParam('page', 1);
        /**
         * @var $paginator Zend_Paginator
         * */

        $paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        try {
            $this
                ->setPageTitle($this->view->translate('Browse Blogs'))
                ->addPageInfo('type', 'browse')
                ->add($this->component()->itemSearch($this->getSearchForm()))
                ->add($this->component()->navigation('blog_main', true), -1)
                ->add($this->component()->quickLinks('blog_quick', true))
                ->add($this->component()->customComponent('itemList', $this->prepareBrowseList($paginator)))
//                ->add($this->component()->paginator($paginator))
                ->renderContent();
        } catch (Exception $e) {
        }
    }

    public function blogsManageAction()
    {
        if (!$this->_helper->requireUser->isValid()) return;

        //Get settings
        $settings = Engine_Api::_()->getApi('settings', 'core');

        //Get Params
        $params = $this->_request->getParams();
        $params['ipp'] = $settings->getSetting('pageblog.page', 10);
        $params['show'] = 3;
        $params['owner'] = Engine_Api::_()->user()->getViewer();
        $params['page'] = $this->_getParam('page', 1);

        //    $this->view->paginator
        $paginator = Engine_Api::_()->getApi('core', 'pageblog')->getBlogsPaginator($params);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
//    $paginator->setItemCountPerPage(2);

        $this
            ->setPageTitle($this->view->translate('My Blogs'))
            ->addPageInfo('type', 'manage')
            ->add($this->component()->itemSearch($this->getSearchForm()))
            ->add($this->component()->navigation('blog_main', true), -1)
            ->add($this->component()->quickLinks('blog_quick', true))
            ->add($this->component()->customComponent('itemList', $this->prepareManageList($paginator)))
//            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    private function prepareBrowseList(Zend_Paginator $paginator)
    {
        $items = array();
        foreach ($paginator as $p_item) {
            $page_pref = '';

            if( is_object($p_item) && in_array(get_class($p_item), array('Pageblog_Model_Pageblog')) ) {
                $p_item = $p_item->toArray();
            }
            
            if (!is_array($p_item)) {
                throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');
            }

            if ($p_item['type'] == 'page') {
                $page_pref = 'page';
            }

            $item = Engine_Api::_()->getItem($page_pref . 'blog', $p_item['blog_id']);
            $owner = $item->getOwner();

            $std = array(
                'title' => $item->getTitle(),
                'descriptions' => array(
                    $this->view->translate('Posted') . ' ' . $this->view->translate('By') . ' ' . $owner->getTitle()
                ),
                'href' => $item->getHref(),
                'photo' => ($page_pref == 'page') ? $item->getPhotoUrl('thumb.normal') : $owner->getPhotoUrl('thumb.normal'),
                'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
                'owner_id' => $owner->getIdentity(),
                'owner' => $this->subject($owner)
            );

            if ($page_pref)
                $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


            $items[] = $std;
        }

      $paginatorPages = $paginator->getPages();
        $component = array(
          'listPaginator' => true,
          'pageCount' => $paginatorPages->pageCount,
          'next' => @$paginatorPages->next,
          'paginationParam' => 'page',

          'items' => $items
        );
        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        return $component;
    }

    private function prepareManageList(Zend_Paginator $paginator)
    {
        $items = array();
        foreach ($paginator as $p_item) {
            $page_pref = '';

            if (!is_array($p_item))
                throw new Apptouch_Controller_Action_BridgeException('Invalid items stored into Paginator');

            if ($p_item['type'] == 'page') {
                $page_pref = 'page';
            }

            $item = Engine_Api::_()->getItem($page_pref . 'blog', $p_item['blog_id']);

            $owner = $item->getOwner();
            $options = array();
            if ($page_pref) {
                $options[] = array(
                    'label' => $this->view->translate('Edit Entry'),
                    'href' => $item->getHref(),
                    'class' => 'buttonlink icon_blog_edit'
                );

                $options[] = array(
                    'label' => $this->view->translate('Delete Entry'),
                    'href' => $this->view->url(array('action' => 'delete', 'pageblog_id' => $item->getIdentity()), 'page_blogs', true),
                    'class' => 'buttonlink smoothbox icon_blog_delete'
                );
            } else {
                $options[] = $this->getOption($item, 0);
                $options[] = $this->getOption($item, 1);
            }

            $std = array(
                'title' => $item->getTitle(),
                'descriptions' => array(),
                'href' => $item->getHref(),
                'photo' => ($page_pref == 'page') ? $item->getPhotoUrl('thumb.normal') : $owner->getPhotoUrl('thumb.normal'),
                'creation_date' => $this->view->timestamp(strtotime($item->creation_date)),
                'owner_id' => null,
                'owner' => null,
                'manage' => $options
            );

            if ($page_pref)
                $std['descriptions'][] = $this->view->translate('On page ') . $this->view->htmlLink($item->getPage()->getHref(), $item->getPage()->getTitle());


            $items[] = $std;
        }

      $paginatorPages = $paginator->getPages();
        $component = array(
          'listPaginator' => true,
          'pageCount' => $paginatorPages->pageCount,
          'next' => @$paginatorPages->next,
          'paginationParam' => 'page',
          'items' => $items
        );
        $searchKeyword = $this->_getParam('search', false);
        if ($searchKeyword) {
            $component['search'] = array(
                'keyword' => $searchKeyword . '', // to string
                'count' => $paginator->getTotalItemCount(),
            );
        }

        return $component;
    }

    public function indexInit()
    {
        $this->page_id = $page_id = $this->_getParam('page_id');
        $this->viewer = Engine_Api::_()->user()->getViewer();

        if (!$page_id) {
            return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
        }

        $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

        if (!$page) {
            return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
        }

        $this->isAllowedView = $this->getPageApi()->isAllowedView($page);

        if (!$this->isAllowedView) {
            $this->isAllowedPost = false;
            $this->isAllowedComment = false;
            return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
        }

        $this->isAllowedPost = $this->getApi()->isAllowedPost($page);
        $this->isAllowedComment = $this->getPageApi()->isAllowedComment($page);

        $this->addPageInfo('contentTheme', 'd');
    }

    public function indexIndexAction()
    {
        if (!$this->isAllowedView) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
            return;
        }

        // Ulan O: Setting Subject By page_id
        if ($this->page_id)
            Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

        $select = Engine_Api::_()->getDbTable('pageblogs', 'pageblog')
            ->getSelect(array('page_id' => $this->page_id));
        if ($this->_getParam('search', false)) {
            $select->where('title LIKE ? OR body LIKE ?', '%' . $this->_getParam('search') . '%');
        }
        $blogs = Zend_Paginator::factory($select);
        $blogs->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $blogs->setCurrentPageNumber($this->_getParam('page', 1));


        if ($this->isAllowedPost)
            $this->add($this->component()->quickLinks('pageblog_quick', true));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->add($this->component()->subjectPhoto($this->pageObject))
            ->add($this->component()->navigation('pageblog', true))
            ->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($blogs, 'browseItemList', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($blogs))
            ->renderContent();
    }

    public function indexMineAction()
    {
        if (!$this->isAllowedView) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
            return;
        }
        // Ulan O: Setting Subject By page_id
        if ($this->page_id)
            Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('page', $this->page_id));

        $select = Engine_Api::_()->getDbTable('pageblogs', 'pageblog')->getSelect(array(
            'page_id' => $this->page_id,
            'user_id' => $this->viewer->getIdentity()
        ));

        if ($this->_getParam('search', false)) {
            $select->where('title LIKE ? OR body LIKE ?', '%' . $this->_getParam('search') . '%');
        }
        $blogs = Zend_Paginator::factory($select);
        $blogs->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $blogs->setCurrentPageNumber($this->_getParam('page', 1));

        if ($this->isAllowedPost)
            $this->add($this->component()->quickLinks('pageblog_quick', true));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));


        $this->add($this->component()->subjectPhoto($this->pageObject))
            ->add($this->component()->navigation('pageblog', true))
            ->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($blogs, 'manageBlogList', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($blogs))
            ->renderContent();
    }

    public function indexCreateAction()
    {
        if (!$this->isAllowedPost)
            return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

        $page_id = $this->page_id;
        $form = new Pageblog_Form_Create();
        $form->removeElement('file');
        $form->addElement('File', 'file', array(
            'label' => 'Pageblog_Add_Photo',
            'order' => 3
        ));
        $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        Engine_Api::_()->core()->setSubject($this->pageObject);
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($this->pageObject))
                ->add($this->component()->navigation('pageblog', true))
                ->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->subjectPhoto($this->pageObject))
                ->add($this->component()->navigation('pageblog', true))
                ->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $values = $form->getValues();
        $table = $this->getTable();
        $user = Engine_Api::_()->user()->getViewer();

        try {
            $blog = $table->createRow();

            $blog->title = $values['blog_title'];
            $blog->body = $values['blog_body'];
            $blog->page_id = $page_id;
            $blog->user_id = $user->getIdentity();

            $photo = $this->getPicupFiles('file');
            // Set photo

            if (!empty($values['file'])) {
                $blog_photo = Engine_Api::_()->getApi('core', 'pageblog')->uploadPhoto($form->file);
                $blog->photo_id = $blog_photo->getIdentity();
            } else if (!empty($photo)) {
                $photo = $photo[0];
                $blog_photo = Engine_Api::_()->getApi('core', 'pageblog')->uploadPhoto($photo);
                $blog->photo_id = $blog_photo->getIdentity();
            }

            $blog->save();

            $tags = preg_split('/[,]+/', $values['blog_tags']);
            if ($tags) {
                $blog->tags()->setTagMaps($user, $tags);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirect($blog);
    }

    public function indexEditAction()
    {
        $this->add($this->component()->navigation('pageblog', true));
        $blog = Engine_Api::_()->getItem('pageblog', $this->_getParam('blog_id'));
        $page = $blog->getPage();
        $user = $blog->getOwner();
        $form = new Pageblog_Form_Create();
        $form->removeElement('file');
        $form->setTitle('Edit Blog Entry');

        $form->getElement('blog_title')->setValue($blog->title);

        $tags = $blog->tags()->getTagMaps();
        $tagString = '';
        foreach ($tags as $tagmap) {
            if ($tagString !== '') $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $form->getElement('blog_tags')->setValue($tagString);
        $form->getElement('blog_body')->setValue($blog->body);

        Engine_Api::_()->core()->setSubject($this->pageObject);
        if (!$this->getRequest()->isPost()) {
            if ($this->isAllowedPost)
                $this->add($this->component()->quickLinks('pageblog_quick', true));

            $this->add($this->component()->subjectPhoto($blog))
                ->add($this->component()->navigation('pageblog', true))
                ->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($values = $this->getRequest()->getPost())) {
            if ($this->isAllowedPost)
                $this->add($this->component()->quickLinks('pageblog_quick', true));

            $this->add($this->component()->subjectPhoto($blog))
                ->add($this->component()->navigation('pageblog', true))
                ->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        try {
            $blog->title = $values['blog_title'];
            $blog->body = $values['blog_body'];
            $tags = preg_split('/[,]+/', $values['blog_tags']);
            if ($tags) {
                $blog->tags()->setTagMaps($user, $tags);
            }
            $blog->save();
        } catch (Exception $e) {
            throw $e;
        }

        return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'blogs'), 'page_view'));
    }

    public function indexDeleteAction()
    {
        $blog = Engine_Api::_()->getItem('pageblog', $this->_getParam('blog_id'));
        $page = $blog->getPage();

        $form = new Pageblog_Form_Delete();

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $search_api = Engine_Api::_()->getDbTable('search', 'page');
        $search_api->deleteData($blog);

        $blog->delete();

        return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'blogs'), 'page_view'), Zend_Registry::get('Zend_Translate')->_("Page Blog was deleted."), true);
    }

    protected function getTable()
    {
        return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
    }

    protected function getPageApi()
    {
        return Engine_Api::_()->getApi('core', 'page');
    }

    protected function getApi()
    {
        return Engine_Api::_()->getApi('core', 'pageblog');
    }

    //=------------------------------------------ PageBlog Customizer Functions ---------------------------------
    public function manageBlogList(Core_Model_Item_Abstract $item)
    {
        $options = array();
        $page_id = $item->getPage()->getIdentity();

        $options[] = array(
            'label' => $this->view->translate('Edit Entry'),
            'attrs' => array(
                'href' => $this->view->url(array('action' => 'edit', 'page_id' => $page_id, 'blog_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_blog', true),
                'class' => 'buttonlink icon_album_edit'
            )
        );

        $options[] = array(
            'label' => $this->view->translate('Delete Blog'),
            'attrs' => array(
                'href' => $this->view->url(array('action' => 'delete', 'page_id' => $page_id, 'blog_id' => $item->getIdentity(), 'no_cache' => rand(0, 1000)), 'page_blog', true),
                'class' => 'buttonlink smoothbox icon_album_delete',
            )
        );

        if (isset($item->photo_id)) {
            $photoUrl = $item->getPhotoUrl('thumb.normal');
        } else {
            $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
        }

        $customize_fields = array(
            'title' => $item->getTitle(),
            'descriptions' => array(
                $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
            ),
            'photo' => $photoUrl,
            'manage' => $options
        );

        return $customize_fields;
    }

    //=------------------------------------------ PageBlog Customizer Functions ---------------------------------

    public function browseItemList(Core_Model_Item_Abstract $item)
    {
        if (isset($item->photo_id)) {
            $photoUrl = $item->getPhotoUrl('thumb.normal');
        } else {
            $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
        }
        $customize_fields = array(
            'title' => $item->getTitle(),
            'descriptions' => array(
                $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date) . ' ' . $this->view->translate('By') . ' ' . $item->getOwner()->getTitle()
            ),
            'photo' => $photoUrl
        );

        return $customize_fields;
    }

}

<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 06.06.12
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_YnblogController
    extends Apptouch_Controller_Action_Bridge
{

    private function getBlogsSelect($params = array())
    {
        $table = new Ynblog_Model_DbTable_Blogs();
        $rName = $table->info('name');

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $select = $table->select()
            ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : $rName . '.creation_date DESC');

        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (!empty($params['users'])) {
            $str = (string)(is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users']);
            $select->where($rName . '.owner_id in (?)', new Zend_Db_Expr($str));
        }

        if (!empty($params['tag'])) {
            $select
                ->setIntegrityCheck(false)
                ->from($rName)
                ->joinLeft($tmName, "$tmName.resource_id = $rName.blog_id")
                ->where($tmName . '.resource_type = ?', 'blog')
                ->where($tmName . '.tag_id = ?', $params['tag']);
        }

        if (!empty($params['category'])) {
            $select->where($rName . '.category_id = ?', $params['category']);
        }

        if (isset($params['draft'])) {
            $select->where($rName . '.draft = ?', $params['draft']);
        }

        //else $select->group("$rName.blog_id");

        // Could we use the search indexer for this?
        if (!empty($params['search'])) {
            $select->where($rName . ".title LIKE ? OR " . $rName . ".body LIKE ?", '%' . $params['search'] . '%');
        }

        if (!empty($params['start_date'])) {
            $select->where($rName . ".creation_date > ?", date('Y-m-d', $params['start_date']));
        }

        if (!empty($params['end_date'])) {
            $select->where($rName . ".creation_date < ?", date('Y-m-d', $params['end_date']));
        }

        if (!empty($params['visible'])) {
            $select->where($rName . ".search = ?", $params['visible']);
        }

        return $select;
    }

    private function getBlogsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getBlogsSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }

        if (empty($params['limit'])) {
            $page = (int)Engine_Api::_()->getApi('settings', 'core')->getSetting('blog.page', 10);
            $paginator->setItemCountPerPage($page);
        }

        return $paginator;
    }

    public function indexInit()
    {
        $this->addPageInfo('contentTheme', 'd');
        // only show to member_level if authorized
        if (!$this->_helper->requireAuth()->setAuthParams('blog', null, 'view')->isValid()) return;
    }

    public function indexIndexAction()
    {
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();

        // Permissions
        $canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();


        $form = $this->getSearchForm();

        // Process form
        $form->isValid($this->getAllParams());
        $values = $form->getValues();
        $values['draft'] = "0";
        $values['visible'] = "1";
        $values['page'] = $this->_getParam('page', 0);

        // Do the show thingy
        if (@$values['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $values['users'] = $ids;
        }

        $this->view->assign($values);

        // Get blogs

        $paginator = $this->getBlogsPaginator($values);

        //todo $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);
        $paginator->setCurrentPageNumber($values['page']);

        $this->setFormat('browse')
            ->add($this->component()->itemSearch($form));
        if ($paginator->getTotalItemCount()) {
            $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//                ->add($this->component()->paginator($paginator))
            ;
        } elseif ($this->view->search) {
            $this->add($this->component()->tip(
                $this->view->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'blog_general') . '">', '</a>'),
                $this->view->translate('Nobody has written a blog entry with that criteria.')
            ));
        } else {
            if ($canCreate)
                $this->add($this->component()->tip(
                    $this->view->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->view->url(array('action' => 'create'), 'blog_general') . '">', '</a>'),
                    $this->view->translate('Nobody has written a blog entry yet.')
                ));
            else
                $this->add($this->component()->tip(
                    $this->view->translate('Nobody has written a blog entry yet.')
                ));

        }
        $this->renderContent();
    }

    // USER SPECIFIC METHODS
    public function indexManageAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        $canCreate = $this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->checkRequire();

        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $form = new Apptouch_Form_Search();

        $form->removeElement('show');

        // Process form
        $form->isValid($this->getAllParams());
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();

        $this->view->assign($values);

        // Get paginator
        $paginator = $this->getBlogsPaginator($values);
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);
        if ($this->_hasParam('page'))
            $paginator->setCurrentPageNumber($this->_getParam('page'));

        $this->setFormat('manage')
            ->add($this->component()->itemSearch($form));
        if ($paginator->getTotalItemCount()) {
            $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//                ->add($this->component()->paginator($paginator))
            ;
        } elseif ($this->view->category || $this->view->search) {
            $this->add($this->component()->tip(
                $this->view->translate('You do not have any   blog entries that match your search criteria.')
            ));
        } else {
            $this->add($this->component()->tip(
                $this->view->translate('You do not have any blog entries.')
            ));
        }
        $this->renderContent();

    }

    public function indexDeleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $blog = Engine_Api::_()->getItem('blog', $this->getRequest()->getParam('blog_id'));
        if (!$this->_helper->requireAuth()->setAuthParams($blog, null, 'delete')->isValid()) return;

        $form = new Apptouch_Form_Ynblog_Delete();
        if (!$blog) {
            $this->add($this->component()->form($form))
                ->renderContent();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Blog entry doesn't exist or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            $this->view->status = false;
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        }

        $db = $blog->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $blog->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->redirect(
            $this->view->url(array('action' => 'manage')),
            Zend_Registry::get('Zend_Translate')->_('Your blog entry has been deleted.'),
            true
        );
    }

    /**
     * @param $item Core_Model_Item_Abstract
     * @return array
     */
    public function browseItemData(Core_Model_Item_Abstract $item)
    {
        $owner = $item->getOwner();
        $customize_fields = array(
            'descriptions' => array(
                $this->view->translate('Posted') . ' ' . $this->view->translate('By') . ' ' . $owner->getTitle()
            ),
            'photo' => $owner->getPhotoUrl('thumb.normal'),
        );
        return $customize_fields;
    }

    /**
     * @param $item Core_Model_Item_Abstract
     * @return array
     */
    public function manageItemData(Core_Model_Item_Abstract $item)
    {
        $owner = Engine_Api::_()->getItem('user', $item->owner_id);
        $customize_fields = array(
            'description' => null,
            'owner_id' => null,
            'owner' => null,
            'photo' => $owner->getPhotoUrl('thumb.normal'),
            'manage' => $this->getOptions($item)
        );
        return $customize_fields;
    }

    public function indexViewAction()
    {
        // Check permission
        $viewer = Engine_Api::_()->user()->getViewer();
        $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));
        if ($blog) {
            Engine_Api::_()->core()->setSubject($blog);
        }

        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'view')->isValid()) {
            return;
        }
        if (!$blog || !$blog->getIdentity() ||
            ($blog->draft && !$blog->owner_id == $viewer->getIdentity())
        ) {
            return $this->_helper->requireSubject->forward();
        }

        // Prepare data
        $blogTable = new Ynblog_Model_DbTable_Blogs();

        //    $this->view->blog = $blog;
        /*$this->view->owner = */
        $owner = $blog->getOwner();
        //    $this->view->viewer = $viewer;

        if (!$blog->isOwner($viewer)) {
            $blogTable->update(array(
                'view_count' => new Zend_Db_Expr('view_count + 1'),
            ), array(
                'blog_id = ?' => $blog->getIdentity(),
            ));
        }

        // Get tags
        $blogTags = $blog->tags()->getTagMaps();

        // Get category
        if (!empty($blog->category_id)) {
            $cTbl = new Ynblog_Model_DbTable_Categories();
            $category = $cTbl->find($blog->category_id)->current();
        }

        // Get styles
        $table = Engine_Api::_()->getDbtable('styles', 'core');
        $style = $table->select()
            ->from($table, 'style')
            ->where('type = ?', 'user_blog')
            ->where('id = ?', $owner->getIdentity())
            ->limit(1)
            ->query()
            ->fetchColumn();
        if (!empty($style)) {
            try {
                $this->view->headStyle()->appendStyle($style);
            } // silence any exception, exceptin in development mode
            catch (Exception $e) {
                if (APPLICATION_ENV === 'development') {
                    throw $e;
                }
            }
        }
        $owner_ = $this->subject($owner);
        $subject = $this->subject($blog);

        $subject['photo'] = $owner_['photo'];

        $this->setFormat('view');

        if (Engine_Api::_()->user()->getViewer()->getIdentity())
            $this->add($this->component()->quickLinks('gutter'));

        $blogBody = '<div class="blog_body">' . $blog->body . '</div>';

        $this
            ->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($blog->creation_date), 'count' => null)))
            ->add($this->component()->subjectPhoto($subject))
            ->add($this->component()->html($blogBody));
        $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
        if ($viewer && $viewer->getIdentity()) {
            $controlGroup->append($this->dom()->new_('a',
                array(
                    'data-role' => 'button',
                    'data-icon' => 'chat',
                    'data-rel' => 'dialog',
                    'href' => $this->view->url(array(
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'share',
                            'type' => 'blog',
                            'id' => $blog->getIdentity()), 'default', true)), $this->view->translate('Share')))

                ->append($this->dom()->new_('a',
                    array(
                        'data-role' => 'button',
                        'data-icon' => 'flag',
                        'data-rel' => 'dialog',
                        'href' => $this->view->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $blog->getGuid(),
                                'id' => $blog->getIdentity()), 'default', true)), $this->view->translate('Report')));

            $this->add($this->component()->html($controlGroup . '<br />'));
        }

        $this->renderContent();
    }

    //Subscription Controller {
/*    public function subscriptionInit()
    {
        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();

        // only show to member_level if authorized
        if (!$this->_helper->requireAuth()->setAuthParams('blog', $viewer, 'view')->isValid()) {
            return;
        }

        // Get subject
        if (($blog_id = $this->_getParam('blog_id')) &&
            ($blog = Engine_Api::_()->getItem('blog', $blog_id)) instanceof Blog_Model_Blog
        ) {
            $subject = $blog->getOwner('user');
            Engine_Api::_()->core()->setSubject($subject);
        } else if (($user_id = $this->_getParam('user_id')) &&
            ($user = Engine_Api::_()->getItem('user', $user_id)) instanceof User_Model_User
        ) {
            $subject = $user;
            Engine_Api::_()->core()->setSubject($subject);
        } else {
            $subject = null;
        }

        // Must have a subject
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }

        // Must be allowed to view this member
        if (!$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) {
            return;
        }
    }

    public function subscriptionAddAction()
    {
        // Must have a viewer
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        // Get viewer and subject
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = Engine_Api::_()->core()->getSubject('user');

        // Get subscription table
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');

        // Check if they are already subscribed
        if ($subscriptionTable->checkSubscription($user, $viewer)) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')
                ->_('You are already subscribed to this member\'s blog.');

            return $this->redirect('refresh');
        }

        // Make form
        $form = new Core_Form_Confirm(array(
            'title' => 'Subscribe?',
            'description' => 'Would you like to subscribe to this member\'s blog?',
            'class' => 'global_form_popup',
            'submitLabel' => 'Subscribe',
            'cancelHref' => 'javascript:parent.Smoothbox.close();',
        ));

        // Check method
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        // Check valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }


        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subscriptionTable->createSubscription($user, $viewer);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')
            ->_('You are now subscribed to this member\'s blog.');

        return $this->redirect('refresh');
    }

    public function subscriptionRemoveAction()
    {
        // Must have a viewer
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }

        // Get viewer and subject
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = Engine_Api::_()->core()->getSubject('user');

        // Get subscription table
        $subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'blog');

        // Check if they are already not subscribed
        if (!$subscriptionTable->checkSubscription($user, $viewer)) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')
                ->_('You are already not subscribed to this member\'s blog.');

            return $this->redirect('refresh');
        }

        // Make form
        $this->view->form = $form = new Core_Form_Confirm(array(
            'title' => 'Unsubscribe?',
            'description' => 'Would you like to unsubscribe from this member\'s blog?',
            'class' => 'global_form_popup',
            'submitLabel' => 'Unsubscribe',
            'cancelHref' => 'javascript:parent.Smoothbox.close();',
        ));

        // Check method
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }


        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subscriptionTable->removeSubscription($user, $viewer);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')
            ->_('You are no longer subscribed to this member\'s blog.');

        return $this->redirect('refresh');
    }*/
    // } Subscription Controller

}

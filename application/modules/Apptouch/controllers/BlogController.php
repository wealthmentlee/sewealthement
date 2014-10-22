<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 06.06.12
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_BlogController
    extends Apptouch_Controller_Action_Bridge
{
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
        $form->isValid($this->_getAllParams());
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
        $blogTable = Engine_Api::_()->getItemTable('blog');
        $paginator = $blogTable->getBlogsPaginator($values);
        //todo $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $items_per_page = Engine_Api::_()->getApi('settings', 'core')->blog_page;
        $paginator->setItemCountPerPage($items_per_page);
        $paginator->setCurrentPageNumber($values['page']);

        $this->setFormat('browse')
            ->add($this->component()->itemSearch($form));
        if ($paginator->getTotalItemCount()) {
            $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true)))
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
        $form->isValid($this->_getAllParams());
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();

        $this->view->assign($values);

        // Get paginator
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator($values);
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
            if ($canCreate)
                $this->add($this->component()->tip(
                    $this->view->translate('Get started by %1$swriting%2$s a new entry.', '<a href="' . $this->view->url(array('action' => 'create'), 'blog_general') . '">', '</a>'),
                    $this->view->translate('You do not have any blog entries.')
                ));
            else
                $this->add($this->component()->tip(
                    $this->view->translate('You do not have any blog entries.')
                ));
        }
        $this->renderContent();

    }

    public function indexCreateAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        if (!$this->_helper->requireAuth()->setAuthParams('blog', null, 'create')->isValid()) return;

        //    // Get navigation
        ////    $this->view->navigation =
        //    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
        //      ->getNavigation('blog_main');

        // Prepare form
        //    $this->view->form =
        $form = new Blog_Form_Create();
        $form->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
        ));

        $form->removeElement('token'); //if you have any questions about it ask for Ulan L :)

        $this->setFormat('create');
        // If not post or form not valid, return
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }


        // Process
        $table = Engine_Api::_()->getItemTable('blog');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create blog
            $viewer = Engine_Api::_()->user()->getViewer();
            $values = array_merge($form->getValues(), array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            ));

            $blog = $table->createRow();
            $values['body'] = '<div style="clear: both;">' . nl2br($values['body']) . '</div>';
            $blog->setFromArray($values);
            $blog->save();

            // Auth
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $blog->tags()->addTagMaps($viewer, $tags);

            // Add activity only if blog is published
            if ($values['draft'] == 0) {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new', null, array('is_mobile' => true));

                // make sure action exists before attaching the blog to the activity
                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                }

            }

            // Send notifications for subscribers
            Engine_Api::_()->getDbtable('subscriptions', 'blog')
                ->sendNotifications($blog);

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        //    $this->add($this->component()->form($form))
        //      ->renderContent();

        return $this->redirect($this->view->url(array('action' => 'manage', 'format' => 'json'), 'blog_general'));

    }

    public function indexEditAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $blog = Engine_Api::_()->getItem('blog', $this->_getParam('blog_id'));
        if (!Engine_Api::_()->core()->hasSubject('blog')) {
            Engine_Api::_()->core()->setSubject($blog);
        }

        if (!$this->_helper->requireSubject()->isValid()) return;
        if (!$this->_helper->requireAuth()->setAuthParams($blog, $viewer, 'edit')->isValid()) return;

        // Prepare form
        $form = new Blog_Form_Edit();
        $form->removeElement('token'); //if you have any questions about it ask for Ulan L :)

        // Populate form
        $form->populate($blog->toArray());

        $tagStr = '';
        foreach ($blog->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap->getTag();
            if (!isset($tag->text)) continue;
            if ('' !== $tagStr) $tagStr .= ', ';
            $tagStr .= $tag->text;
        }
        $form->populate(array(
            'tags' => $tagStr,
            'body' => strip_tags($blog->body) //
        ));
        //    $this->view->tagNamePrepared = $tagStr;

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        foreach ($roles as $role) {
            if ($form->auth_view) {
                if ($auth->isAllowed($blog, $role, 'view')) {
                    $form->auth_view->setValue($role);
                }
            }

            if ($form->auth_comment) {
                if ($auth->isAllowed($blog, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
            }
        }

        // hide status change if it has been already published
        if ($blog->draft == "0") {
            $form->removeElement('draft');
        }
        $this->setFormat('create');

        // Check post/form
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }


        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $values['body'] = '<div style="clear: both;">' . nl2br($values['body']) . '</div>';
            $blog->setFromArray($values);
            $blog->modified_date = date('Y-m-d H:i:s');
            $blog->save();

            // Auth
            if (empty($values['auth_view'])) {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
                $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
            }

            // handle tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $blog->tags()->setTagMaps($viewer, $tags);

            // insert new activity if blog is just getting published
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($blog);
            if (count($action->toArray()) <= 0 && $values['draft'] == '0') {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new', null, array('is_mobile' => true));
                // make sure action exists before attaching the blog to the activity
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                }
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($blog) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            // Send notifications for subscribers
            Engine_Api::_()->getDbtable('subscriptions', 'blog')
                ->sendNotifications($blog);

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->redirect($this->view->url(array('action' => 'manage')));
    }

    public function indexDeleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $blog = Engine_Api::_()->getItem('blog', $this->getRequest()->getParam('blog_id'));
        if (!$this->_helper->requireAuth()->setAuthParams($blog, null, 'delete')->isValid()) return;

        $form = new Blog_Form_Delete();
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

    public function indexUploadPhotoAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->_helper->layout->disableLayout();

        if (!Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create')) {
            return false;
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) return;

        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            //      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity()
            ));
            $photo->save();

            $photo->setPhoto($_FILES['Filedata']);

            $this->view->status = true;
            $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_id = $photo->photo_id;
            $this->view->photo_url = $photo->getPhotoUrl();

            $table = Engine_Api::_()->getDbtable('albums', 'album');
            $album = $table->getSpecialAlbum($viewer, 'blog');

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);


            $db->commit();

        } catch (Album_Model_Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = $this->view->translate($e->getMessage());
            throw $e;
            return;

        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            throw $e;
            return;
        }
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
        $owner = $item->getOwner();
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
            ($blog->draft && !$blog->isOwner($viewer))
        ) {
            return $this->_helper->requireSubject->forward();
        }

        // Prepare data
        $blogTable = Engine_Api::_()->getDbtable('blogs', 'blog');

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
            $category = Engine_Api::_()->getDbtable('categories', 'blog')
                ->find($blog->category_id)->current();
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
    public function subscriptionInit()
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
    }
    // } Subscription Controller

}

<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:36
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_TimelineController extends Apptouch_Controller_Action_Bridge
{
    /* profile controller */
    public function profileInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->id = $id = $this->_getParam('id');

            if (null !== $id) {
                $subject = Engine_Api::_()->user()->getUser($id);
                if ($subject->getIdentity()) {
                    $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
                    Engine_Api::_()->core()->setSubject($subject);
                }
            }
        }
    }

    public function profileIndexAction()
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->add($this->component()->html('No subject.'));
            return;
        }
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
        if (!$require_check && !$this->_helper->requireUser()->isValid()) {
            $this->add($this->component()->html('1.'))->renderContent();
            return;
        }

        // Check enabled
        if (!$subject->enabled && !$viewer->isAdmin()) {
            $this->add($this->component()->html('2.'))->renderContent();
            return;
            //return $this->_forward('requireauth', 'error', 'core');
        }

        // Check block
        if ($viewer->isBlockedBy($subject)) {
            $this->add($this->component()->html('3.'))->renderContent();
            return;
            //return $this->_forward('requireauth', 'error', 'core');
        }

        // Increment view count
        if (!$subject->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        if (Engine_Api::_()->getApi('core', 'apptouch')->isTabletMode()) {
            $this->addPageInfo('fields', $this->getHelper('fields')->toArray($subject));
            $this->attrPage('class', 'tablet_timeline');
        }

        $this->attrPage('class', 'timeline_page');


        $this->add($this->component()->quickLinks('profile'))
            ->add($this->component()->timelineCover(), 0)
            ->add($this->component()->like(array('subject' => $this->view->subject())), 1)
            ->add($this->component()->rate(array('subject' => $this->view->subject())), 2)
            ->add($this->component()->tabs(), 5)
            ->addPageInfo('contentTheme', 'd');
        $this->renderContent();
    }

    /* profile controller */

    /* photo controller */
    protected $_type;

    public function photoInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (!Engine_Api::_()->core()->hasSubject()) {
            // Can specifiy custom id
            $id = $this->_getParam('id', null);
            $subject = null;
            if (null === $id) {
                $subject = Engine_Api::_()->user()->getViewer();
            } else {
                $subject = Engine_Api::_()->getItem('user', $id);
            }

            /**
             * @var $subject Timeline_Model_User
             */
            $subject = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($subject->getIdentity());
            Engine_Api::_()->core()->setSubject($subject);

        } else {
            $subject = Engine_Api::_()->core()->getSubject();
        }

        $this->_type = $this->_getParam('type', 'cover');

        $this->subject = $subject;
        $this->item_id = $subject->getIdentity();
        $this->item_type = $subject->getType();
        $this->setting_name = Engine_Api::_()->timeline()->getCoverPhotoSetting($this->item_id, $this->item_type, $this->_type);
        $this->position_setting_name = Engine_Api::_()->timeline()->getCoverPhotoPositionSetting($this->item_id, $this->item_type, $this->_type);
        $this->cover_parent_setting_name = Engine_Api::_()->timeline()->getCoverParentSetting($this->item_id, $this->item_type, $this->_type);

        // Set up require's
        //        $this->_helper->requireUser();
        //        $this->_helper->requireSubject('user');
        //        $this->_helper->requireAuth()->setAuthParams(
        //            null,
        //            null,
        //            'edit'
        //        );


    }


    public function photoAlbumsAction()
    {
        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            $this->add($this->component()->html('Error 1'))->renderContent();
            return;
            //            return $this->_helper->content->setNoRender();
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
            $this->add($this->component()->html('Error 2'))->renderContent();
            return;
            //            return $this->_helper->content->setNoRender();
        }

        $page = $this->_getParam('page', 1);

        $subject = Engine_Api::_()->core()->getSubject('user');
        $table = Engine_Api::_()->getItemTable('album');

        $select = $table->select()
            ->where('owner_id = ?', $subject->getIdentity())
            ->order('view_count DESC');

        $just_items = $this->_getParam('just_items', false);
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $paginator->setCurrentPageNumber($page);

        $this->add($this->component()->timelineCoverAlbums($paginator, $subject))->renderContent();
        return;
    }

    public function photoPhotosAction()
    {

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            $this->add($this->component()->html('Error 1'))->renderContent();
            return;
            //            return $this->_helper->content->setNoRender();
        }

        $page = $this->_getParam('page', 1);

        $album_id = $this->_getParam('album_id');
        $album = Engine_Api::_()->getItem('album', $album_id);
        if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->isValid()) {
            $this->add($this->component()->html('Error 2'))->renderContent();
            return;
            //            return $this->_helper->content->setNoRender();
        }

        $subject = Engine_Api::_()->core()->getSubject('user');

        // Prepare data
        $photoTable = Engine_Api::_()->getItemTable('album_photo');
        $paginator = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());
        $paginator->setCurrentPageNumber($page);

        $just_items = $this->_getParam('just_items', false);
        $this->add($this->component()->timelineCoverPhotos($paginator, $subject))->renderContent();
        return;
    }

    public function photoGetAction()
    {
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->hasTimelinePhoto($this->_type)) {
            return $this->_helper->content->setNoRender();
        }

        $this->view->albumPhoto = $subject->getTimelineAlbumPhoto($this->_type);
    }

    public function photoSetAction()
    {
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('settings', 'hecore');

        if (!$subject->isSelf(Engine_Api::_()->user()->getViewer())) {
            return;
        }

        if (
            !$this->_getParam('photo_id', false) ||
            null == ($photo = Engine_Api::_()->getItem('album_photo', $this->_getParam('photo_id'))) ||
            !$subject->setTimelinePhoto($photo, $this->_type)
        ) {
            $this->add($this->component()->html('Error 1'))->renderContent();
            return;
        }

        $cover_parent = 'subject';
        if ($photo->getType() == 'pagealbumphoto') {
            $cover_parent = 'pagealbum';
        }
        if ($photo->getType() == 'album_photo') {
            $cover_parent = 'album';
        }

        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $table->setSetting($this->cover_parent_setting_name, $cover_parent);
        $table->setSetting($this->setting_name, $photo->getIdentity());

        return $this->redirect(
            $this->view->url(array('id' => $subject->getIdentity()), 'user_profile')
        );
        //        $this->view->status = true;
        //        $row_name = $this->_type . '_id';
        //        $this->view->photo_id = $subject->$row_name;
    }


    public function photoUploadAction()
    {
        /**
         * @var $subject Timeline_Model_User
         */
        $subject = Engine_Api::_()->core()->getSubject();

        if (!$subject->isSelf(Engine_Api::_()->user()->getViewer())) {
            return;
        }

        // Get form
        $form = new Timeline_Form_Photo_Upload();
        $form->setAction($this->view->url(array('action' => 'upload', 'id' => $subject->getIdentity()), 'timeline_photo'));
        $form->getElement('Filedata')->setAttrib('onchange', '');
        $form->getElement('Filedata')->clearValidators();
        $form->getElement('Filedata')->setValidators(array(array('Extension', false, 'jpg,jpeg,png,gif')));

        $form->addElement('submit', 'Submit', array('label' => 'Submit'));

        $url = $this->view->url(array('id' => $subject->getIdentity()), 'user_profile');
        $form->addElement('Cancel', 'cancel', array(
            'label' => $this->view->translate('cancel'),
            'link' => true,
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'href' => $url,
            'decorators' => array('ViewHelper')
        ));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }

        $photo = $this->getPicupFiles('Filedata');
        if ($form->Filedata->getValue() !== null) {
            $photo = $form->Filedata;
        } else if (!empty($photo)) {
            $photo = $photo[0];
        } else {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }
        // Uploading a new photo
        if ($photo) {
            $db = $subject->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $iMain = $subject->setTimelinePhoto($photo, $this->_type);

                // Insert activity
                $activity_type = 'post_self';
                $body = '';

                if ($this->_type == 'cover') {
                    $activity_type = 'cover_photo_update';
                    $body = '{item:$subject} added a new cover photo.';
                } elseif ($this->_type == 'born') {
                    $activity_type = 'birth_photo_update';
                    $body = '{item:$subject} added a new birth photo.';
                }
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($subject, $subject, $activity_type, $body, array('is_mobile' => true));
                // Hooks to enable albums to work
                $attachment = null;
                if ($action) {
                    $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onSubjectTimelinePhotoUpload', array(
                            'subject' => $subject,
                            'file' => $iMain,
                            'type' => $this->_type
                        ));
                    $attachment = $event->getResponse();
                }

                $cover_parent = 'album';
                if (!$attachment) {
                    $attachment = $iMain;
                    $cover_parent = 'storage';
                } else {
                    if ($attachment->getType() == 'pagealbumphoto') {
                        $cover_parent = 'pagealbum';
                    }

                    if ($this->item_type == 'user') {
                        if ($this->_type == 'cover') {
                            $activity_type = 'cover_photo_update';
                            $body = '{item:$subject} added a new cover photo.';
                        } elseif ($this->_type == 'born') {
                            $activity_type = 'birth_photo_update';
                            $body = '{item:$subject} added a new birth photo.';
                        }

                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($this->subject, $this->subject, $activity_type, $body);
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                    }

                    $_type = $this->_getParam('type', 'cover');
                    $setting_name = Engine_Api::_()->timeline()->getCoverPhotoSetting($subject->getIdentity(), $subject->getType(), $_type);
                    $cover_parent_setting_name = Engine_Api::_()->timeline()->getCoverParentSetting($subject->getIdentity(), $subject->getType(), $_type);

                    $table = Engine_Api::_()->getDbTable('settings', 'core');
                    $table->setSetting($setting_name, $attachment->getIdentity());
                    $table->setSetting($cover_parent_setting_name, $cover_parent);
                }

                $db->commit();
                $this->redirect(
                    $this->view->url(array('id' => $subject->getIdentity()), 'user_profile')
                );
                return;
            } // If an exception occurred within the image adapter, it's probably an invalid image
            catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } // Otherwise it's probably a problem with the database or the storage system (just throw it)
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function photoRemoveAction()
    {
        // Get form
        $form = new Timeline_Form_Photo_Remove();
        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }
        $user = Engine_Api::_()->core()->getSubject();

        if (!$user->isSelf(Engine_Api::_()->user()->getViewer())) {
            return;
        }

        $type = $this->_getParam('type', 'cover');
        $setting_name = Engine_Api::_()->timeline()->getCoverPhotoSetting($user->getIdentity(), $user->getType(), $type);
        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $table->setSetting($setting_name, 0);

        return $this->redirect(
            $this->view->url(array('id' => $user->getIdentity()), 'user_profile')
        );
    }

    /* photo controller */


    /*
    ** user-settings controller
    */

    public function userSettingsInit()
    {
        // Can specifiy custom id
        $id = $this->_getParam('id', null);
        $subject = null;
        if (null === $id) {
            $subject = Engine_Api::_()->user()->getViewer();
            Engine_Api::_()->core()->setSubject($subject);
        } else {
            $subject = Engine_Api::_()->getItem('user', $id);
            Engine_Api::_()->core()->setSubject($subject);
        }

        // Set up require's
        $this->_helper->requireUser();
        $this->_helper->requireSubject();
        $this->_helper->requireAuth()->setAuthParams(
            $subject,
            null,
            'edit'
        );

        // Set up navigation
        $param = $this->_getParam('param', false);
        //        if (!$param) {
        //            $this->view->navigation = $navigation = Engine_Api::_()
        //                ->getApi('menus', 'core')
        //                ->getNavigation('user_settings', ($id ? array('params' => array('id' => $id)) : array()));
        //        } else {
        //            $this->view->navigation = $navigation = Engine_Api::_()
        //                ->getApi('menus', 'core')
        //                ->getNavigation('user_edit', ($id ? array('params' => array('id' => $id)) : array()));
        //        }

    }

    public function userSettingsIndexAction()
    {
        /**
         * @var $settings Core_Api_Settings
         * @var $subject User_Model_User
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($settings->__get('timeline.usage') == 'force') {
            return $this->_helper->redirector->gotoUrl($subject->getHref(), array('prependBase' => false));
        }

        /**
         * @var $settings User_Model_DbTable_Settings
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'hecore');

        $form = new Timeline_Form_User_Settings();

        $selected = $settings->getSetting($subject, 'timeline-usage');

        $form->getElement('usage')->setValue($selected);

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))->renderContent();
            return;
        }

        $usage = $form->getValue('usage');
        $settings->setSetting($subject, 'timeline-usage', $usage);
        $form->addNotice('TIMELINE_Settings have been successfully saved');

        if ($viewer->isSelf($subject)) {
            $title = $this->view->translate('My Settings');
        } else {
            $title = $this->view->translate('%1$s\'s Settings', $this->htmlLink($subject->getHref(), $subject->getTitle()));
        }

        $this->setPageTitle($title);
        $this->add($this->component()->html("<h3>{$title}</h3>"));
        $this->add($this->component()->form($form));
        $this->renderContent();
    }


    // Tabs {
    public function tabFriends($active = false)
    {
        // Don't render this if friendships are disabled
        if (!Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible) {
            return;
        }

        // Get subject and check auth
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return;
        }

        // Multiple friend mode
        $select = $subject->membership()->getMembersOfSelect();
        //$this->view->friends =
        $friends = $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(5);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Set item count per page and current page number
        //    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }
        //    $this->view->friendIds =
        $ids;

        // Get the items
        $friendUsers = array();
        foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser) {
            $friendUsers[$friendUser->getIdentity()] = $friendUser;
        }

        // Get lists if viewing own profile
        if ($viewer->isSelf($subject)) {
            // Get lists
            $listTable = Engine_Api::_()->getItemTable('user_list');
            //      $this->view->lists =
            $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

            $listIds = array();
            foreach ($lists as $list) {
                $listIds[] = $list->list_id;
            }

            // Build lists by user
            $listItems = array();
            $listsByUser = array();
            if (!empty($listIds)) {
                $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
                $listItemSelect = $listItemTable->select()
                    ->where('list_id IN(?)', $listIds)
                    ->where('child_id IN(?)', $ids);
                $listItems = $listItemTable->fetchAll($listItemSelect);
                foreach ($listItems as $listItem) {
                    //$list = $lists->getRowMatching('list_id', $listItem->list_id);
                    //$listsByUser[$listItem->child_id][] = $list;
                    $listsByUser[$listItem->child_id][] = $listItem->list_id;
                }
            }
            //      $this->view->listItems = $listItems;
            //      $this->view->listsByUser = $listsByUser;
        }

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

      $paginatorPages = $paginator->getPages();
        $items = array(
          'listPaginator' => true,
          'pageCount' => $paginatorPages->pageCount,
          'next' => @$paginatorPages->next,
          'paginationParam' => 'page',

        );
        foreach ($paginator as $item) {

            if (!isset($friendUsers[$item->resource_id])) continue;
            $item = $friendUsers[$item->resource_id];

            $std = array(
                'title' => $item->getTitle(),
                'descriptions' => array(
                    $item->status()
                ),
                'href' => $item->getHref(),
                'photo' => $item->getPhotoUrl('thumb.normal'),
            );

            $items['items'][] = $std;
        }
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        if ($active)
            $this->add($this->component()->customComponent('itemList', $items), 7)
//                ->add($this->component()->paginator($paginator), 8)
            ;
        //    } prepare
        return array(
            'showContent' => false,
            'response' => $paginator //Zend_Paginator::factory($friendUsers)
        );
    }

    public function tabGroups($active = false)
    {
        // Get paginator
        $subject = Engine_Api::_()->core()->getSubject('user');
        $membership = Engine_Api::_()->getDbtable('membership', 'group');
        //    $this->view->paginator =
        $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }
        $paginator->setItemCountPerPage(5); // todo tmp

        return $paginator;
    }

    public function tabMusic($active = false)
    {
        // Get paginator
        $paginator = Engine_Api::_()->music()->getPlaylistPaginator(array(
            'user' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
            'sort' => 'creation_date',
            'searchBit' => 1,
            //'limit' => 10, // items per page
        ));

        // Set item count per page and current page number
        //    $paginator->setItemCountPerPage(2);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

        return $paginator;
    }

    public function tabPolls($active = false)
    {
        // Get paginator
        $paginator = Engine_Api::_()->getItemTable('poll')->getPollsPaginator(array(
            'user_id' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
            'sort' => "creation_date",
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

        return $paginator;
    }

    public function tabVideos($active = false)
    {
        $table = Engine_Api::_()->getItemTable('video');
        $params = array(
            'user_id' => Engine_Api::_()->core()->getSubject('user')->getIdentity(),
            'status' => 1,
            'search' => 1
        );

        $select = Engine_Api::_()->video()->getVideosSelect($params);

        if ($this->_getParam('search', false)) {
            $select->where($table->info('name') . '.title LIKE ? OR ' . $table->info('name') . '.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        // Get paginator
        $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0 && !$this->_getParam('search', false)) {
            return;
        } else {
            $this->_childCount = $paginator->getTotalItemCount(); // todo how to show count?
        }

        if ($active) {
            $form = $this->getSearchForm();
            $form->setMethod('get');
            $form->getElement('search')->setValue($this->_getParam('search'));


            $this->add($this->component()->itemSearch($form), 10)
                ->add($this->component()->itemList($paginator, 'browseVideoList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 11)
//                ->add($this->component()->paginator($paginator), 12)
            ;
        }

        return array(
            'showContent' => false,
            'response' => $paginator
        );
    }

    public function tabForumPosts($active = false)
    {

        // Get paginator
        //    $this->view->subject =
        $subject = Engine_Api::_()->core()->getSubject('user');
        $postsTable = Engine_Api::_()->getDbtable('posts', 'forum');
        $postsSelect = $postsTable->select()
            ->where('user_id = ?', $subject->getIdentity())
            ->order('creation_date DESC');

        //    $this->view->paginator =
        $paginator = Zend_Paginator::factory($postsSelect);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

        //    prepare {

      $paginatorPages = $paginator->getPages();
        $items = array(
          'listPaginator' => true,
          'pageCount' => $paginatorPages->pageCount,
          'next' => @$paginatorPages->next,
          'paginationParam' => 'page',
        );
        foreach ($paginator as $item) {
            $topic = $item->getParent();
            $forum = $topic->getParent();

            $std = array(
                'title' => ucfirst($this->view->translate('in the topic %1$s', $topic->__toString()) . '<br/>' .
                    $this->view->translate('in the forum %1$s', $forum->__toString())),
                'descriptions' => array(
                    $item->getDescription()
                ),
                'href' => $item->getHref(),
                'creation_date' => $this->view->locale()->toDateTime(strtotime($item->creation_date)),
            );
            $items[] = $std;
        }
        if ($active)
            $this->add($this->component()->customComponent('itemList', $items), 7)
//                ->add($this->component()->paginator($paginator), 8)
            ;
        //    } prepare
        return array(
            'showContent' => false,
            'response' => $paginator
        );
    }

    public function tabEvents($active = false)
    {
        // Get paginator
        $membership = Engine_Api::_()->getDbtable('membership', 'event');
        $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect(Engine_Api::_()->core()->getSubject('user'))->order('starttime DESC'));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

        return $paginator;
    }

    public function tabBlogs($active = false)
    {
        // Get paginator
        $paginator = Engine_Api::_()->getItemTable('blog')->getBlogsPaginator(array(
            'orderby' => 'creation_date',
            'draft' => '0',
            'user_id' => Engine_Api::_()->core()->getSubject()->getIdentity(),
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2)); // todo count per page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }

        return $paginator;
    }

    public function tabClassifieds($active = false)
    {
        // Get paginator
        $paginator = Engine_Api::_()->getItemTable('classified')->getClassifiedsPaginator(array(
            'orderby' => 'creation_date',
            'user_id' => Engine_Api::_()->core('user')->getSubject()->getIdentity(),
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2)); //todo count per page
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }
        return $paginator;
    }

    public function tabAlbums($active = false)
    {
        $select = Engine_Api::_()->getItemTable('album')->getAlbumSelect(array(
            'owner' => Engine_Api::_()->core('user')->getSubject(),
            'search' => 1
        ));

        if ($this->_getParam('search', false)) {
            $select->where('title LIKE ? OR description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        // Get paginator
        $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0 && !$this->_getParam('search', false)) {
            return;
        }

        if ($active) {
            $form = $this->getSearchForm();
            $form->setMethod('get');
            $form->getElement('search')->setValue($this->_getParam('search'));


            $this->add($this->component()->itemSearch($form), 10)
                ->add($this->component()->itemList($paginator, 'browseAlbumList', array('listPaginator' => true, 'attrs' => array('class' => 'tile-view'))), 11)
//                ->add($this->component()->paginator($paginator), 12)
            ;
        }

        return array(
            'showContent' => false,
            'response' => $paginator
        );
    }

    public function tabAdvalbum($active = false)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return;
        }

        // Get paginator
        $paginator = Engine_Api::_()->getApi('core', 'advalbum')
            ->getAlbumPaginator(array('owner' => $subject, 'search' => 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $paginator->setItemCountPerPage($settings->getSetting('album_page', 25));

        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        return $paginator;
    }

    public function tabPages($active = false)
    {
        if (!Engine_Api::_()->core()->hasSubject()) {
            return false;
        }

        $subject = Engine_Api::_()->core()->getSubject('user');
        $viewer = Engine_Api::_()->user()->getViewer();

        if ($subject->getType() != 'user') {
            return false;
        }

        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return false;
        }

        $table = Engine_Api::_()->getDbtable('membership', 'page');
        $itemTable = Engine_Api::_()->getDbTable('pages', 'page');

        $itName = $itemTable->info('name');
        $mtName = $table->info('name');
        $col = current($itemTable->info('primary'));

        $select = $itemTable->select()
            ->setIntegrityCheck(false)
            ->from($itName)
            ->joinLeft($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", array('admin_title' => "{$mtName}.title"))
            ->where("`{$mtName}`.`user_id` = ?", $subject->getIdentity())
            ->where("`{$mtName}`.`active` = 1")
            ->where("`{$itName}`.`approved` = 1");

        if ($active && $this->_getParam('search', false)) {
            $select->where($itName . '.title LIKE ? OR ' . $itName . '.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return false;
        }

        $ids = array();
        foreach ($paginator as $page) {
            $ids[] = $page->getIdentity();
        }

        return $paginator;
    }

    public function tabGifts($active = false)
    {
        /**
         * @var $table Hegift_Model_DbTable_Recipients
         * @var $subject User_Model_User
         */

        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            if (!($user = Engine_Api::_()->getItem('user', $this->_getParam('user_id', 0)))) {
                return false;
            }
            Engine_Api::_()->core()->setSubject($user);
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return false;
        }

        // Member type
        $subject = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('recipients', 'hegift');

        $page = $this->_getParam('page', 1);
        $paginator = $table->getPaginator(array('user_id' => $subject->getIdentity(), 'action_name' => 'received', 'page' => $page, 'ipp' => 20));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return false;
        }

        if ($active) {
          $paginatorPages = $paginator->getPages();
            $data = array(
              'listPaginator' => true,
              'pageCount' => $paginatorPages->pageCount,
              'next' => @$paginatorPages->next,
              'paginationParam' => 'page',
            );
            $data['items'] = array();
            $description = $this->view->translate('HEGIFT_sent you this gift ') . '<b>';

            foreach ($paginator as $rs) {
                $user = $rs->getUser('received');
                $gift = $rs->getGift();

                $des = $description . $rs->getPrivacy() . '</b> <br>' .
                    $this->view->translate('HEGIFT_Sent %s ', $this->view->timestamp($rs->send_date));

                if ($rs->getMessage()) {
                    $des = $des . '<br><i>' . $rs->getMessage() . '</i>';
                }

                $url = $this->view->url(array('action' => $gift->getTypeName(), 'gift_id' => $gift->getIdentity(), 'sender_id' => $rs->subject_id), 'hegift_own', true);

                $data['items'][] = array(
                    'title' => $user->getTitle(),
                    'descriptions' => array($des),
                    'href' => $url,
                    'photo' => $gift->getPhotoUrl('thumb.normal'),
                    'attrsA' => array('data-rel' => 'dialog'),
                );
            }

            $this->add($this->component()->customComponent('itemList', $data), 15)
//                ->add($this->component()->paginator($paginator), 16)
            ;
        }

        return array(
            'showContent' => false,
            'response' => $paginator
        );
    }

    public function tabCheckin($active = false)
    {
        return $this->component()->checkin();
    }

    public function tabArticles($active = false)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $user_type = $this->_getParam('user_type', 'owner');

        if ($user_type == 'viewer') {
            if (!$viewer->getIdentity()) {
                return false;
            } else {
                $user = $viewer;
            }
        } else {
            // owner mode
            if (!Engine_Api::_()->core()->hasSubject()) {
                return false;
            }

            // Get subject and check auth
            $subject = Engine_Api::_()->core()->getSubject();

            if (!($subject instanceof Core_Model_Item_Abstract)) {
                return false;
            }

            if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                return false;
            }

            if (!($subject instanceof User_Model_User)) {
                $user = $subject->getOwner('user');
            } else {
                $user = $subject;
            }
        }

        if (!($user instanceof User_Model_User) || !$user->getIdentity()) {
            return false;
        }

        $params = array(
            'published' => 1,
            'search' => 1,
            'limit' => $this->_getParam('max', 5),
            'order' => $this->_getParam('order', 'recent'),
            'period' => $this->_getParam('period'),
            'keyword' => $this->_getParam('search'),
            'category' => $this->_getParam('category'),
        );

        if ($this->_getParam('featured', 0)) {
            $params['featured'] = 1;
        }

        if ($this->_getParam('sponsored', 0)) {
            $params['sponsored'] = 1;
        }

        $params['user'] = $user;

        $paginator = Engine_Api::_()->article()->getArticlesPaginator($params);

        $showphoto = $this->_getParam('showphoto', $this->view->display_style == 'narrow' ? 0 : 1);
        $showmeta = $this->_getParam('showmeta', $this->view->display_style == 'narrow' ? 0 : 1);
        $showdescription = $this->_getParam('showdescription', $this->view->display_style == 'narrow' ? 0 : 1);

        $showmemberarticleslink = $this->_getParam('showmemberarticleslink', $this->view->display_style == 'narrow' ? 0 : 1);

        $order = $params['order'];

        // Add count to title if configured

        if ($paginator->getTotalItemCount() <= 0) {
            return false;
        }

        return $paginator;
    }

    public function tabLikes($active = false)
    {
        if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
        } else {
            $subject = $viewer;
        }

        if ($subject->getType() != 'user') {
            return false;
        }

        if (!$subject->authorization()->isAllowed($viewer, 'interest')) {
            return false;
        }


        $settings = Engine_Api::_()->getApi('settings', 'core');
        $period = $settings->getSetting('like.profile_period', 1);
        $type = $this->_getParam('type', 'all');

        $item_count = Engine_Api::_()->like()->getLikedCount($subject);
        if (!$item_count) {
            return false;
        }

        if ($active) {
            $items = Engine_Api::_()->like()->getLikedItems($subject);
            shuffle($items);

            if ($period) { //for week and month
                $item_count = Engine_Api::_()->like()->getLikedCount($subject, $type);
                $items = Engine_Api::_()->like()->getLikedItems($subject, false, $type);
                shuffle($items);

                $all_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/all'), $this->view->translate('LIKE_Overall'));
                $month_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/month'), $this->view->translate('LIKE_This Month'));
                $week_btn = $this->dom()->new_('a', array('data-role' => 'button', 'href' => $subject->getHref() . '/tab/likes/type/week'), $this->view->translate('LIKE_This Week'));
                $group = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal'));

                $group->append($all_btn)->append($month_btn)->append($week_btn);

                $this->add($this->component()->html($group), 10);
            }

            $data = array();
            $data['items'] = array();

            foreach ($items as $item) {
                $data['items'][] = array(
                    'title' => $item->getTitle(),
                    'descriptions' => array($item->getDescription()),
                    'href' => $item->getHref(),
                    'photo' => $item->getPhotoUrl('thumb.normal'),
                );
            }

            $count_txt = $this->view->translate(array("like_%s item", "like_%s items", $item_count), ($item_count));
            $href = $this->view->url(array('action' => 'see-liked', 'user_id' => $subject->getIdentity(), 'period_type' => $type), 'like_default');
            $html_el = $this->dom()->new_('a', array('href' => $href, 'data-rel' => 'dialog'), $count_txt);
            $this->add($this->component()->html($html_el), 11)
                ->add($this->component()->customComponent('itemList', $data), 12);
        }

        $href = $this->view->url(array('action' => 'see-liked', 'user_id' => $subject->getIdentity(), 'period_type' => $type), 'like_default');

        $dialog_title = $this->view->translate("like_%s's likes", $subject->getTitle());
        return true;
    }

    // } Tabs

    public function browseAlbumList(Core_Model_Item_Abstract $item)
    {
        $photo_type = 'thumb.normal';
        if (Engine_Api::_()->apptouch()->isTabletMode()) {
            $photo_type = 'thumb.profile';
        }

        if (isset($item->photo_id)) {
            $photoUrl = $item->getPhotoUrl($photo_type);
        } else {
            $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
        }

        $customize_fields = array(
            'title' => $item->getTitle(),
            'descriptions' => array(
                $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date)
            ),
            'photo' => $photoUrl,
            'creation_date' => null,
            'counter' => strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count()))),
        );

        return $customize_fields;
    }

    public function browseVideoList(Core_Model_Item_Abstract $item)
    {
        $photo_type = 'thumb.normal';
        if (Engine_Api::_()->apptouch()->isTabletMode()) {
            $photo_type = 'thumb.profile';
        }

        if (isset($item->photo_id)) {
            $photoUrl = $item->getPhotoUrl($photo_type);
        } else {
            $photoUrl = $item->getOwner()->getPhotoUrl($photo_type);
        }

        $customize_fields = array(
            'title' => $item->getTitle(),
            'descriptions' => array(
                $this->view->translate('Posted') . ' ' . $this->view->timestamp($item->creation_date)
            ),
            'photo' => $photoUrl,
            'counter' => strtoupper($this->view->translate(array('%1$s view', '%1$s views', $item->view_count), $this->view->locale()->toNumber($item->view_count))),
        );

        return $customize_fields;
    }
}

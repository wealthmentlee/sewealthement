<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ForumController.php 9282 2011-09-21 00:42:22Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Apptouch_ForumController
    extends Apptouch_Controller_Action_Bridge
{

    public function indexInit()
    {
        $this->addPageInfo('contentTheme', 'd');
    }

//  IndexController {
    public function indexIndexAction()
    {
        if (!$this->_helper->requireAuth()->setAuthParams('forum', null, 'view')->isValid()) {
            return;
        }

        $categoryTable = Engine_Api::_()->getItemTable('forum_category');
        $categories = $categoryTable->fetchAll($categoryTable->select()->order('order ASC'));

        $forumTable = Engine_Api::_()->getItemTable('forum_forum');
        $forums = array();
        foreach ($forumTable->fetchAll() as $forum) {
            if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'view')) {
                $order = $forum->order;
                while (isset($forums[$forum->category_id][$order])) {
                    $order++;
                }
                $forums[$forum->category_id][$order] = $forum;
                ksort($forums[$forum->category_id]);
            }
        }

        foreach ($categories as $category) {

            if (empty($forums[$category->category_id])) {
                continue;
            }
            $this
                ->add($this->component()->html('<ul data-role="listview"><li data-role="list-divider" data-theme="b">' . $this->view->translate($category->getTitle()) . '</li></ul>'))
                ->add($this->component()->itemList(Zend_Paginator::factory($forums[$category->category_id]), 'forumCustomize'));

        }
        $this->renderContent();
    }

    public function forumCustomize(Forum_Model_Forum $item)
    {
        $customize_fields = array(
            'photo' => $this->view->baseUrl() . '/application/modules/Forum/externals/images/forum.png',
        );
        return $customize_fields;
    }
//  } IndexController


//  ForumController {
    public function forumInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (0 !== ($forum_id = (int)$this->_getParam('forum_id')) &&
            null !== ($forum = Engine_Api::_()->getItem('forum_forum', $forum_id))
        ) {
            Engine_Api::_()->core()->setSubject($forum);
        } else if (0 !== ($category_id = (int)$this->_getParam('category_id')) &&
            null !== ($category = Engine_Api::_()->getItem('forum_category', $category_id))
        ) {
            Engine_Api::_()->core()->setSubject($category);
        }
    }

    public function forumViewAction()
    {
        if (!$this->_helper->requireSubject('forum')->isValid()) {
            return;
        }
        $forum = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth->setAuthParams($forum, null, 'view')->isValid()) {
            return;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        $forum = Engine_Api::_()->core()->getSubject();

        // Increment view count
        $forum->view_count = new Zend_Db_Expr('view_count + 1');
        $forum->save();

        $canPost = $forum->authorization()->isAllowed(null, 'topic.create');

        // Make paginator
        $table = Engine_Api::_()->getItemTable('forum_topic');
        $select = $table->select()
            ->where('forum_id=?', $forum->getIdentity())
            ->order('sticky DESC')
            ->order('modified_date DESC');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($settings->getSetting('forum_forum_pagelength'));
        $forum_topic_pagelength = $settings->getSetting('forum_topic_pagelength');

        $list = $forum->getModeratorList();
        $moderators = $list->getAllChildren();
        $links = array(
            array(
                'label' => $this->view->translate("Forums"),
                'attrs' => array(
                    'href' => $this->view->url(array(), 'forum_general')
                )
            ),
            array(
                'label' => $forum->getTitle(),
                'attrs' => array(
                    'href' => $this->view->url(array('forum_id' => $forum->getIdentity()), 'forum_forum'),
                    'class' => 'ui-btn-active',
                    'data-icon' => 'arrow-d'
                )
            ),
        );

        // html element
        $div = $this->dom()->new_('div');
        $div->attrs = array(
            'data-role' => 'collapsible'
        );
        $h3 = $this->dom()->new_('h3');
        $h3->text = $this->view->translate('Moderators:');
        $div->append($h3);
        $ul = $this->dom()->new_('ul', array('data-role' => 'listview'));
        $p = $this->dom()->new_('p');
        $div->append($p);
        foreach ($moderators as $moderator) {
            $li = $this->dom()->new_('li')
                ->append($this->dom()->new_('a', array(
                    "href" => $moderator->getHref()
                ), $moderator->getTitle()));
            $ul->append($li);
        }
        $p->append($ul);
        // Render
        $this
            ->add($this->component()->crumb($links));
        if ($canPost) {
            $this
                ->add($this->component()->html($this->view->htmlLink($forum->getHref(array(
                    'action' => 'topic-create',
                )), $this->view->translate('Post New Topic'), array(
                    'data-icon' => 'plus',
                    'data-role' => 'button'
                ))));
        }
        $this
            ->add($this->component()->html($div))
            ->add($this->component()->itemList($paginator))
            ->renderContent();

    }

    public function forumTopicCreateAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireSubject('forum')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $forum = Engine_Api::_()->core()->getSubject();
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.create')->isValid()) {
            return;
        }

        $form = new Forum_Form_Topic_Create();

        $form->removeElement('photo');
        $form->addElement('File', 'photo', array(
            'label' => 'Attach a Photo',
            'size' => '40',
            'order' => 2
        ));

        // Remove the file element if there is no file being posted
        if ($this->getRequest()->isPost() && empty($_FILES['photo'])) {
            $form->removeElement('photo');
        }

        $links = array(
            array(
                'label' => $this->view->translate("Forums"),
                'attrs' => array(
                    'href' => $this->view->url(array(), 'forum_general')
                )
            ),
            array(
                'label' => $forum->getTitle(),
                'attrs' => array(
                    'href' => $this->view->url(array('forum_id' => $forum->getIdentity()), 'forum_forum'),
                    'class' => 'ui-btn-active',
                    'data-icon' => 'arrow-d'
                )
            )
        );

        $this
            ->add($this->component()->crumb($links));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this
                ->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        // Process
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();
        $values['forum_id'] = $forum->getIdentity();

        $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
        $postTable = Engine_Api::_()->getDbtable('posts', 'forum');

        $db = $topicTable->getAdapter();
        $db->beginTransaction();

        try {

            // Create topic
            $topic = $topicTable->createRow();
            $topic->setFromArray($values);
            $topic->title = htmlspecialchars($values['title']);
            $topic->description = $values['body'];
            $topic->save();

            // Create post
            $values['topic_id'] = $topic->getIdentity();

            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            if (!empty($values['photo'])) {
                $post->setPhoto($form->photo);
            }

            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($topic, 'registered', 'create', true);

            // Create topic watch
            $topicWatchesTable->insert(array(
                'resource_id' => $forum->getIdentity(),
                'topic_id' => $topic->getIdentity(),
                'user_id' => $viewer->getIdentity(),
                'watch' => (bool)$values['watch'],
            ));

            // Add activity
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $action = $activityApi->addActivity($viewer, $topic, 'forum_topic_create', null, array('is_mobile' => true));
            if ($action) {
                $action->attach($topic);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->redirect($post);
    }

// } ForumController


//  TopicController {
    public function topicInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (0 !== ($topic_id = (int)$this->_getParam('topic_id')) &&
            null !== ($topic = Engine_Api::_()->getDbTable('topics', 'forum')->fetchRow(array('topic_id = ?' => $topic_id))) &&
            $topic instanceof Forum_Model_Topic
        ) {
            Engine_Api::_()->core()->setSubject($topic);
        }
    }

    public function topicDeleteAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));

        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.delete')->isValid()) {
            return;
        }

        $form = new Forum_Form_Topic_Delete();

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
        $table = Engine_Api::_()->getItemTable('forum_topic');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        Zend_Registry::get('Zend_Translate')->_('Topic deleted.');
        $this->redirect($forum, Zend_Registry::get('Zend_Translate')->_('Topic deleted.'));

    }

    public function topicEditAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid()) {
            return;
        }

        $form = new Forum_Form_Topic_Edit();

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
        $table = Engine_Api::_()->getItemTable('forum_topic');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();

            $topic->setFromArray($values);
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function topicViewAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }

        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->topic = $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));

        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'view')->isValid()) {
            return;
        }

        // Settings
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $post_id = (int)$this->_getParam('post_id');
        $settings->getSetting('forum_bbcode');

        // Views
        if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
            $topic->view_count = new Zend_Db_Expr('view_count + 1');
            $topic->save();
        }

        // Check watching
        $isWatching = null;
        if ($viewer->getIdentity()) {
            $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
            $isWatching = $topicWatchesTable
                ->select()
                ->from($topicWatchesTable->info('name'), 'watch')
                ->where('resource_id = ?', $forum->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->limit(1)
                ->query()
                ->fetchColumn(0);
            if (false === $isWatching) {
                $isWatching = null;
            } else {
                $isWatching = (bool)$isWatching;
            }
        }
        //    $this->view->isWatching = $isWatching;

        // Auth for topic
        $canPost = false;
        $canEdit = false;
        $canDelete = false;
        if (!$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create')) {
            $canPost = true;
        }
        if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit')) {
            $canEdit = true;
        }
        if (Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.delete')) {
            $canDelete = true;
        }
        $this->canPost = $canPost;
        $this->canEdit = $canEdit;
        $this->canDelete = $canDelete;

        // Auth for posts
        $canEdit_Post = false;
        $canDelete_Post = false;
        if ($viewer->getIdentity()) {
            $canEdit_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.edit');
            $canDelete_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.delete');
        }
        $this->canEdit_Post = $canEdit_Post;
        $this->canDelete_Post = $canDelete_Post;


        // Make form
        if ($canPost) {
            $form = new Forum_Form_Post_Quick();
            $form->setAction($topic->getHref(array('action' => 'post-create')));
            $form->populate(array(
                'topic_id' => $topic->getIdentity(),
                'ref' => $topic->getHref(),
                'watch' => (false === $isWatching ? '0' : '1'),
            ));
        }

        // Keep track of topic user views to show them which ones have new posts
        if ($viewer->getIdentity()) {
            $topic->registerView($viewer);
        }

        $table = Engine_Api::_()->getItemTable('forum_post');
        $select = $topic->getChildrenSelect('forum_post', array('order' => 'post_id ASC'));
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($settings->getSetting('forum_topic_pagelength'));

        // set up variables for pages
        $page_param = (int)$this->_getParam('page');
        $post = Engine_Api::_()->getItem('forum_post', $post_id);

        // if there is a post_id
        if ($post_id && $post && !$page_param) {
            $icpp = $paginator->getItemCountPerPage();
            $post_page = ceil(($post->getPostIndex() + 1) / $icpp);

            $paginator->setCurrentPageNumber($post_page);
        } // Use specified page
        else if ($page_param) {
            $paginator->setCurrentPageNumber($page_param);
        }
        if (isset($form))
            $form->getElement('photo')->setLabel('APPTOUCH_Upload Photo')->setAttrib('style', '');

        $options = array();
        if ($canPost)
            $options[] = array(
                'label' => $this->view->translate('Post Reply'),
                'attrs' => array(
                    'href' => $this->topic->getHref(array('action' => 'post-create')),
                    'data-icon' => 'chat'
                )
            );

        if ($viewer->getIdentity()) {
            if (!$isWatching)
                $options[] = array(
                    'label' => $this->view->translate('Watch Topic'),
                    'attrs' => array(
                        'href' => $this->view->url(array('action' => 'watch', 'watch' => '1')),
                        'data-icon' => 'wifi'
                    )
                );
            else
                $options[] = array(
                    'label' => $this->view->translate('Stop Watching Topic'),
                    'attrs' => array(
                        'href' => $this->view->url(array('action' => 'watch', 'watch' => '0')),
                        'data-icon' => 'wifi'
                    )
                );
        }

        if ($this->canEdit || $this->canDelete) {
            if ($this->canEdit) {
                if (!$topic->sticky)
                    $options[] = array(
                        'label' => $this->view->translate('Make Sticky'),
                        'attrs' => array(
                            'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '1', 'reset' => false)),
                            'data-icon' => 'page'
                        )
                    );
                else
                    $options[] = array(
                        'label' => $this->view->translate('Remove Sticky'),
                        'attrs' => array(
                            'href' => $this->view->url(array('action' => 'sticky', 'sticky' => '0', 'reset' => false)),
                            'data-icon' => 'remove-sign'
                        )
                    );
                if (!$topic->closed)
                    $options[] = array(
                        'label' => $this->view->translate('Close'),
                        'attrs' => array(
                            'href' => $this->view->url(array('action' => 'close', 'close' => '1', 'reset' => false)),
                            'data-icon' => 'lock'
                        )
                    );
                else
                    $options[] = array(
                        'label' => $this->view->translate('Open'),
                        'attrs' => array(
                            'href' => $this->view->url(array('action' => 'close', 'close' => '0', 'reset' => false)),
                            'data-icon' => 'wifi'
                        )
                    );

                $options[] = array(
                    'label' => $this->view->translate('Rename'),
                    'attrs' => array(
                        'href' => $this->view->url(array('action' => 'rename', 'reset' => false)),
                        'data-icon' => 'edit',
                        'data-rel' => 'dialog'
                    )
                );
                $options[] = array(
                    'label' => $this->view->translate('Move'),
                    'attrs' => array(
                        'href' => $this->view->url(array('action' => 'move', 'reset' => false)),
                        'data-icon' => 'arrow-r',
                        'data-rel' => 'dialog'
                    )
                );
                $options[] = array(
                    'label' => $this->view->translate('Delete'),
                    'attrs' => array(
                        'href' => $this->view->url(array('action' => 'delete', 'reset' => false)),
                        'data-icon' => 'remove-sign',
                        'data-rel' => 'dialog'
                    )
                );
            }
        }
        $links = array(
            array(
                'label' => $this->view->translate("Forums"),
                'attrs' => array(
                    'href' => $this->view->url(array(), 'forum_general')
                )
            ),
            array(
                'label' => $forum->getTitle(),
                'attrs' => array(
                    'href' => $this->view->url(array('forum_id' => $forum->getIdentity()), 'forum_forum')
                )
            ),
            array(
                'label' => $topic->getTitle(),
                'attrs' => array(
                    'href' => $topic->getHref(),
                    'class' => 'ui-btn-active',
                    'data-icon' => 'arrow-d'
                )
            )
        );

        $this
            ->add($this->component()->crumb($links))
            ->add($this->component()->discussion($topic, $paginator, array(
                'options' => $options,
                'postForm' => isset($form) ? $form : null
            ), 'forumPostCustomize'))
            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    public function forumPostCustomize($post)
    {
        $postFormat = array();
        $options = array();
        if ($this->canPost)
            $options[] = array(
                'label' => $this->view->translate('Quote'),
                'attrs' => array(
                    'href' => $this->view->url(array(
                            'route' => 'forum_topic',
                            'action' => 'post-create',
                            'topic_id' => $this->view->subject()->getIdentity(),
                            'quote_id' => $post->getIdentity(),
                        ), 'forum_topic'),
                    'data-icon' => 'chat'
                )
            );
        if ($this->canEdit || ($post->user_id != 0 && $post->isOwner($this->viewer) && !$this->topic->closed)) {

            if ($this->canEdit || $this->canEdit_Post)
                $options[] = array(
                    'label' => $this->view->translate('Edit'),
                    'attrs' => array(
                        'href' => $this->view->url(array('post_id' => $post->getIdentity(), 'action' => 'edit'), 'forum_post'),
                        'data-icon' => 'edit'
                    )
                );

            if ($this->canEdit || $this->canDelete_Post)
                $options[] = array(
                    'label' => $this->view->translate('Delete'),
                    'attrs' => array(
                        'href' => $this->view->url(array('post_id' => $post->getIdentity(), 'action' => 'delete'), 'forum_post'),
                        'data-icon' => 'remove-sign',
                        'data-rel' => 'dialog'
                    )
                );
        }
        if ($this->view->viewer()->getIdentity() && $post->user_id != $this->view->viewer()->getIdentity()) {
            $options[] = array(
                'label' => $this->view->translate('Report'),
                'attrs' => array(
                    'href' => $this->view->url(array(
                            'module' => 'core',
                            'controller' => 'report',
                            'action' => 'create',
                            'subject' => $post->getGuid(),
                        ), 'default'),
                    'data-icon' => 'edit'
                )
            );
        }
        $postFormat['options'] = $options;
        $postFormat['owner'] = array();

        $signature = $post->getSignature();
        if ($signature) {
            $postFormat['owner']['postCount'] = $signature->post_count . ' ' . $this->view->translate('posts');
        }

        $postFormat['owner']['status'] = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $this->topic->forum_id))->isModerator($post->getOwner()) ? $this->view->translate('Moderator') : null;

        return $postFormat;
    }

    public function topicStickyAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid()) {
            return;
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->sticky = (null === $this->_getParam('sticky') ? !$topic->sticky : (bool)$this->_getParam('sticky'));
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->redirect($topic);
    }

    public function topicCloseAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid()) {
            return;
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->closed = (null === $this->_getParam('closed') ? !$topic->closed : (bool)$this->_getParam('closed'));
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->redirect($topic);
    }

    public function topicRenameAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid()) {
            return;
        }

        $form = new Forum_Form_Topic_Rename();

        if (!$this->getRequest()->isPost()) {
            $form->title->setValue(htmlspecialchars_decode(($topic->title)));
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $title = htmlspecialchars($form->getValue('title'));
            $topic = Engine_Api::_()->core()->getSubject();
            $topic->title = $title;
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->redirect($topic, Zend_Registry::get('Zend_Translate')->_('Topic renamed.'));

    }

    public function topicMoveAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->isValid()) {
            return;
        }

        $form = new Forum_Form_Topic_Move();

        // Populate with options
        $multiOptions = array();
        foreach (Engine_Api::_()->getItemTable('forum')->fetchAll() as $forum) {
            $multiOptions[$forum->getIdentity()] = $this->view->translate($forum->getTitle());
        }
        $form->getElement('forum_id')->setMultiOptions($multiOptions);

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

        $values = $form->getValues();

        $table = $topic->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Update topic
            $topic->forum_id = $values['forum_id'];
            $topic->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->redirect($topic, Zend_Registry::get('Zend_Translate')->_('Topic moved.'));
    }

    public function topicPostCreateAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, null, 'post.create')->isValid()) {
            return;
        }
        if ($topic->closed) {
            return;
        }
        $form = new Forum_Form_Post_Create();
        if ($form->getElement('photo')) {
            $form->getElement('photo')->setLabel('APPTOUCH_Upload Photo');
        }
        // Remove the file element if there is no file being posted
        if ($this->getRequest()->isPost() && empty($_FILES['photo'])) {
            $form->removeElement('photo');
        }

        $allowHtml = (bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_html', 0);
        $allowBbcode = (bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0);

        $quote_id = $this->getRequest()->getParam('quote_id');
        if (!empty($quote_id)) {
            $quote = Engine_Api::_()->getItem('forum_post', $quote_id);
            if ($quote->user_id == 0) {
                $owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
            } else {
                $owner_name = $quote->getOwner()->__toString();
            }
            if ($allowHtml || !$allowBbcode) {
                $form->body->setValue("<blockquote><strong>" . $this->view->translate('%1$s said:', $owner_name) . "</strong><br />" . $quote->body . "</blockquote><br />");
            } else {
                $form->body->setValue("[blockquote][b]" . strip_tags($this->view->translate('%1$s said:', $owner_name)) . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n");
            }
        }
        $links = array(
            array(
                'label' => $this->view->translate("Forums"),
                'attrs' => array(
                    'href' => $this->view->url(array(), 'forum_general')
                )
            ),
            array(
                'label' => $forum->getTitle(),
                'attrs' => array(
                    'href' => $this->view->url(array('forum_id' => $forum->getIdentity()), 'forum_forum')
                )
            ),
            array(
                'label' => $topic->getTitle(),
                'attrs' => array(
                    'href' => $topic->getHref(),
                )
            ),
            array(
                'label' => $this->view->translate('Post Reply'),
                'attrs' => array(
                    'class' => 'ui-btn-active',
                    'data-icon' => 'arrow-d'
                )
            )
        );
        $this->add($this->component()->crumb($links));
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
        $values = $form->getValues();
        $values['body'] = $values['body'];
        $values['user_id'] = $viewer->getIdentity();
        $values['topic_id'] = $topic->getIdentity();
        $values['forum_id'] = $forum->getIdentity();

        $topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
        $postTable = Engine_Api::_()->getDbtable('posts', 'forum');
        $userTable = Engine_Api::_()->getItemTable('user');
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

        $viewer = Engine_Api::_()->user()->getViewer();
        $topicOwner = $topic->getOwner();
        $isOwnTopic = $viewer->isSelf($topicOwner);

        $watch = (bool)$values['watch'];
        $isWatching = $topicWatchesTable
            ->select()
            ->from($topicWatchesTable->info('name'), 'watch')
            ->where('resource_id = ?', $forum->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1)
            ->query()
            ->fetchColumn(0);

        $db = $postTable->getAdapter();
        $db->beginTransaction();

        try {

            $post = $postTable->createRow();
            $post->setFromArray($values);
            $post->save();

            if (!empty($values['photo'])) {
                try {
                    $post->setPhoto($form->photo);
                } catch (Engine_Image_Adapter_Exception $e) {
                }
            } elseif (count($picupPhoto = $this->getPicupFiles('photo'))) {
                try {
                    $post->setPhoto($picupPhoto[0]);
                } catch (Engine_Image_Adapter_Exception $e) {
                }
            }

            // Watch
            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool)$watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool)$watch,
                ), array(
                    'resource_id = ?' => $forum->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            // Activity
            $action = $activityApi->addActivity($viewer, $topic, 'forum_topic_reply', null, array('is_mobile' => true));
            if ($action) {
                $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
            }

            // Notifications
            $notifyUserIds = $topicWatchesTable->select()
                ->from($topicWatchesTable->info('name'), 'user_id')
                ->where('resource_id = ?', $forum->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('watch = ?', 1)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

            foreach ($userTable->find($notifyUserIds) as $notifyUser) {
                // Don't notify self
                if ($notifyUser->isSelf($viewer)) {
                    continue;
                }
                if ($notifyUser->isSelf($topicOwner)) {
                    $type = 'forum_topic_response';
                } else {
                    $type = 'forum_topic_reply';
                }
                $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
                    'message' => $this->view->BBCode($post->body), // @todo make sure this works
                ));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->redirect($post);
    }

    public function topicWatchAction()
    {
        if (!$this->_helper->requireSubject('forum_topic')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $topic = Engine_Api::_()->core()->getSubject('forum_topic');
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($forum, $viewer, 'view')->isValid()) {
            return;
        }

        $watch = $this->_getParam('watch', true);

        $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
        $db = $topicWatchesTable->getAdapter();
        $db->beginTransaction();

        try {
            $isWatching = $topicWatchesTable
                ->select()
                ->from($topicWatchesTable->info('name'), 'watch')
                ->where('resource_id = ?', $forum->getIdentity())
                ->where('topic_id = ?', $topic->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity())
                ->limit(1)
                ->query()
                ->fetchColumn(0);

            if (false === $isWatching) {
                $topicWatchesTable->insert(array(
                    'resource_id' => $forum->getIdentity(),
                    'topic_id' => $topic->getIdentity(),
                    'user_id' => $viewer->getIdentity(),
                    'watch' => (bool)$watch,
                ));
            } else if ($watch != $isWatching) {
                $topicWatchesTable->update(array(
                    'watch' => (bool)$watch,
                ), array(
                    'resource_id = ?' => $forum->getIdentity(),
                    'topic_id = ?' => $topic->getIdentity(),
                    'user_id = ?' => $viewer->getIdentity(),
                ));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->redirect($topic);
    }

//  } TopicController

//  PostController {
    public function postInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (0 !== ($post_id = (int)$this->_getParam('post_id')) &&
            null !== ($post = Engine_Api::_()->getDbTable('posts', 'forum')->fetchRow(array('post_id = ?' => $post_id))) &&
            $post instanceof Forum_Model_Post
        ) {
            Engine_Api::_()->core()->setSubject($post);
        }
    }

    public function postDeleteAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireSubject('forum_post')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $post = Engine_Api::_()->core()->getSubject('forum_post');
        $topic = Engine_Api::_()->getDbTable('topics', 'forum')->fetchRow(array('topic_id = ?' => $post->topic_id));
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($post, null, 'delete')->checkRequire() &&
            !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.delete')->checkRequire()
        ) {
            return $this->_helper->requireAuth()->forward();
        }

        $form = new Forum_Form_Post_Delete();

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
        $table = Engine_Api::_()->getItemTable('forum_post');
        $db = $table->getAdapter();
        $db->beginTransaction();

        $topic_id = $post->topic_id;

        try {
            $post->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $topic = Engine_Api::_()->getItem('forum_topic', $topic_id);
        $href = (null === $topic ? $forum->getHref() : $topic->getHref());
        return $this->redirect($href, Zend_Registry::get('Zend_Translate')->_('Post deleted.'));
    }

    public function postEditAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireSubject('forum_post')->isValid()) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $post = Engine_Api::_()->core()->getSubject('forum_post');
        $topic = Engine_Api::_()->getDbTable('topics', 'forum')->fetchRow(array('topic_id = ?' => $post->topic_id));
        $forum = Engine_Api::_()->getDbTable('forums', 'forum')->fetchRow(array('forum_id=?' => $topic->forum_id));
        if (!$this->_helper->requireAuth()->setAuthParams($post, null, 'edit')->checkRequire() &&
            !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->checkRequire()
        ) {
            return $this->_helper->requireAuth()->forward();
        }

        $form = new Forum_Form_Post_Edit(array('post' => $post));

        $allowHtml = (bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_html', 0);
        $allowBbcode = (bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0);

        if ($allowHtml) {
            $body = $post->body;
        } else {
            $body = htmlspecialchars_decode($post->body, ENT_COMPAT);
        }

        $form->body->setValue($body);
        $form->photo->setValue($post->file_id);
        $this->add($this->component()->html('<h2>' . $this->view->translate('Edit Post') . '</h2>'));
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form));
            return $this->renderContent();
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form));
            return;
        }

        // Process
        $table = Engine_Api::_()->getItemTable('forum_post');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();

            $post->body = $values['body'];
            $post->edit_id = $viewer->getIdentity();

            //DELETE photo here.
            if (!empty($values['photo_delete']) && $values['photo_delete']) {
                $post->deletePhoto();
            }

            if (!empty($values['photo'])) {
                $post->setPhoto($form->photo);
            }

            $post->save();

            $db->commit();

            return $this->redirect($this->view->url(array('post_id' => $post->getIdentity(), 'topic_id' => Engine_Api::_()->getDbTable('topics', 'forum')->fetchRow(array('topic_id = ?' => $post->topic_id))->getIdentity()), 'forum_topic', true));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
//  } PostController


}
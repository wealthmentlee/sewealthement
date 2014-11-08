<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:40
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_ActivityController
    extends Apptouch_Controller_Action_Bridge
{

    public $isWall = 0;

    public function indexInit()
    {
        $this->isWall = 0;
        if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')) {
            $this->isWall = 1;
        }
    }


    public function indexPostAction()
    {

        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Get subject if necessary
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        $subject_guid = $this->_getParam('subject', null);
        if ($subject_guid) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        }
        // Use viewer as subject if no subject
        if (null === $subject) {
            $subject = $viewer;
        }

        // Make form
        $form = $this->view->form = new Activity_Form_Post();

        // Check auth
        if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
            return $this->_helper->requireAuth()->forward();
        }

        // Check if post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
            return;
        }

        /*    // Check token
        if( !($token = $this->_getParam('token')) ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
          return;
        }
        $session = new Zend_Session_Namespace('ActivityFormToken');
        if( $token != $session->token ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid token, please try again');
          return;
        }
        $session->unsetAll();*/

        // Check if form is valid
        $postData = $this->getRequest()->getPost();
        $body = @$postData['body'];
        $cat = @$postData['cat'];
		
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
        $postData['body'] = $body;
        if (!$form->isValid($postData)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data1');
            return;
        }

        // Check one more thing
        if ($form->body->getValue() === '' && $form->getValue('attachment_type') === '') {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data2');
            return;
        }

        // set up action variable
        $action = null;

        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $this->getRequest()->getParam('attachment');


            /**
             * Attachment photo
             */

            $photo = $this->getPicupFiles('photo');
            $upload_photo = false;
            // Set photo
            if (!empty($photo)) {
                $photo = $photo[0];
                $upload_photo = $photo;
            } elseif (!empty($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
                $upload_photo = $_FILES['photo'];
            }


            if ($upload_photo) {

                if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album')) {

                    $viewer = Engine_Api::_()->user()->getViewer();
                    $table = Engine_Api::_()->getDbtable('albums', 'album');
                    $db = $table->getAdapter();
                    $db->beginTransaction();

                    try {
                        $type = $this->_getParam('type', 'wall');

                        if (empty($type)) $type = 'wall';

                        $album = $table->getSpecialAlbum($viewer, $type);

                        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                        $photo = $photoTable->createRow();
                        $photo->setFromArray(array(
                            'owner_type' => 'user',
                            'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
                        ));
                        $photo->save();
                        $photo->setPhoto($upload_photo);

                        if ($type == 'message') {
                            $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
                        }

                        $photo->order = $photo->photo_id;
                        $photo->album_id = $album->album_id;
                        $photo->save();

                        if (!$album->photo_id) {
                            $album->photo_id = $photo->getIdentity();
                            $album->save();
                        }

                        if ($type != 'message') {
                            // Authorizations
                            $auth = Engine_Api::_()->authorization()->context;
                            $auth->setAllowed($photo, 'everyone', 'view', true);
                            $auth->setAllowed($photo, 'everyone', 'comment', true);
                        }

                        $db->commit();

                        $attachmentData['type'] = 'photo';
                        $attachmentData['photo_id'] = $photo->photo_id;

                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->view->status = false;
                        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data3');
                        return;
                    }

                } else if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum')) {
                    $viewer = Engine_Api::_()->user()->getViewer();
                    $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
                    $db = $table->getAdapter();
                    $db->beginTransaction();

                    try {
                        $type = $this->_getParam('type', 'wall');

                        if (empty($type)) $type = 'wall';

                        $album = $table->getSpecialAlbum($viewer, $type);

                        $photoTable = Engine_Api::_()->getDbtable('photos', 'advalbum');
                        $photo = $photoTable->createRow();


                        if (is_string($upload_photo)) {
                            $file = array(
                                'tmp_name' => $upload_photo,
                                'name' => basename($upload_photo)
                            );
                        } else {
                            $file = $upload_photo;
                        }
                        $params = array(
                            'owner_type' => 'user',
                            'owner_id' => $viewer->getIdentity()
                        );
                        $photo = Engine_Api::_()->advalbum()->createPhoto($params, $file);

                        if ($type == 'message') {
                            $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
                        }

                        $photo->order = $photo->photo_id;
                        $photo->album_id = $album->album_id;
                        $photo->save();

                        if (!$album->photo_id) {
                            $album->photo_id = $photo->getIdentity();
                            $album->save();
                        }

                        if ($type != 'message') {
                            // Authorizations
                            $auth = Engine_Api::_()->authorization()->context;
                            $auth->setAllowed($photo, 'everyone', 'view', true);
                            $auth->setAllowed($photo, 'everyone', 'comment', true);
                        }

                        $db->commit();

                        $attachmentData['type'] = 'photo';
                        $attachmentData['photo_id'] = $photo->photo_id;
                        //////////////////////////////////////////////////////////////////////

                    } catch (Exception $e) {
                        $db->rollBack();
                        $this->view->status = false;
                        $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
                        // throw $e;
                    }

                }


            } else if (!empty($attachmentData['type']) && $attachmentData['type'] == 'photo') {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data4');
                return;
            }


            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                    $config = null;
                }
                if ($config) {
                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin->$method($attachmentData);
                }
            }

            $actionTable = Engine_Api::_()->getDbTable('actions', 'activity');
            if ($this->isWall) {
                $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');
            }


            // Get body
            $body = $form->getValue('body');
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);

            // Is double encoded because of design mode
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');

            // Special case: status
            if (!$attachment && $viewer->isSelf($subject)) {
                if ($body != '') {
                    $viewer->status = $body;
                    $viewer->status_date = date('Y-m-d H:i:s');
                    $viewer->save();

                    $viewer->status()->setStatus($body);
                }


                $action = $actionTable->addActivity($viewer, $subject, 'status', $body, array(
                    'is_mobile' => true
                ), $this->_getParam('privacy'));

            } else { // General post

                $type = 'post';
                if ($viewer->isSelf($subject)) {
                    $type = 'post_self';
                }

                // Add notification for <del>owner</del> user
                $subjectOwner = $subject->getOwner();

                if (!$viewer->isSelf($subject) &&
                    $subject instanceof User_Model_User
                ) {
                    $notificationType = 'post_' . $subject->getType();
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                        'url1' => $subject->getHref(),
                    ));
                }

                // Add activity
                $action = $actionTable->addActivity($viewer, $subject, $type, $body, array(
                    'is_mobile' => true
                ), $this->_getParam('privacy'));

                // Try to attach if necessary
                if ($action && $attachment) {
                    $actionTable->attachActivity($action, $attachment);
                }
            }


            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($form->getValue('body'));
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment->getHref();
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                    preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))
                ) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = !$action ? null : $action->getHref();
            }
            // Check to ensure proto/host
            if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')
            ) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')
            ) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                    . ": " . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }


            // Publish to facebook, if checked & enabled
            if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable
            ) {
                try {

                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebook = $facebookApi = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid &&
                        $fb_uid->facebook_uid &&
                        $facebookApi &&
                        $facebookApi->getUser() &&
                        $facebookApi->getUser() == $fb_uid->facebook_uid
                    ) {
                        $fb_data = array(
                            'message' => $publishMessage,
                        );
                        if ($publishUrl) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if ($publishName) {
                            $fb_data['name'] = $publishName;
                        }
                        if ($publishDesc) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if ($publishPicUrl) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            } // end Facebook

            // Publish to twitter, if checked & enabled
            if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable
            ) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {
                        // @todo truncation?
                        // @todo attachment
                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($publishMessage);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }

            // Publish to janrain
            if ( //$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable
            ) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();

                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;

                } catch (Exception $e) {
                    // Silence
                }
            }


            if ($this->isWall) {

                $composerData = $this->getRequest()->getParam('composer');
                if (!empty($composerData)) {

                    foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $config) {

                        if (empty($config['composer'])) {
                            continue;
                        }
                        $plugin = Engine_Api::_()->loadClass($config['plugin']);
                        $method = 'onComposer' . ucfirst($config['type']);

                        if (method_exists($plugin, $method)) {
                            $plugin->$method($composerData, array('action' => $action));
                        }
                    }

                }

                if ($action) {
                    Engine_Api::_()->getDbTable('userSettings', 'wall')->saveLastPrivacy($action, $this->_getParam('privacy'), $viewer);
                }

            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e; // This should be caught by error handler
        }
		
		$action->cat = $postData['cat'];
		$action->save();
		
        if ($this->isWall) {
            if ($action) {
                Engine_Api::_()->getDbTable('userSettings', 'wall')->saveLastPrivacy($action, $this->_getParam('privacy'), $viewer);
            }
        }

        if ($action) {
            $this->view->action = $this->getHelper('activity')->direct()->activity($action, array());
            $this->view->action_id = $action->getIdentity();
        }


        // If we're here, we're done
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');

        // Check if action was created
        $post_fail = "";
        if (!$action) {
            $post_fail = "?pf=1";
        }

        // Redirect if in normal context
        $return_url = $form->getValue('return_url', false);
        if ($return_url) {
            return $this->redirect($return_url . $post_fail);
        }
    }


    public function indexPostServiceAction()
    {

        $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        $composerData = $this->getRequest()->getParam('composer');

        // I'm owner
        if ($action->subject_type == 'user' && $action->subject_id == $viewer->getIdentity()) {

        } else {
            die('Opps ...');
        }

        if ($action && $this->isWall) {

            $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall');
            $stream_services = $this->_getParam('share');

            try {

                if (!empty($stream_services)) {

                    foreach ($stream_services as $provider => $enabled) {

                        if (!$enabled) {
                            continue;
                        }
                        $tokenRow = $tableToken->getUserToken($viewer, $provider);
                        if (!$tokenRow) {
                            continue;
                        }
                        $service = Engine_Api::_()->wall()->getServiceClass($provider);
                        if (!$service->check($tokenRow)) {
                            continue;
                        }
                        if (
                            !empty($composerData) && !empty($composerData['fbpage_id']) && $composerData['fbpage_id'] != 'undefined' && $provider == 'facebook'
                        ) {

                            $fbpage_id = $composerData['fbpage_id'];

                            $fbpageTable = Engine_Api::_()->getDbTable('fbpages', 'wall');
                            $select = $fbpageTable->select()
                                ->where('user_id = ?', $viewer->getIdentity())
                                ->where('fbpage_id = ?', $fbpage_id);

                            $fbpage = $fbpageTable->fetchRow($select);

                            if ($fbpage) {
                                $tokenRow->oauth_token = $fbpage->access_token;
                                $service->postAction($tokenRow, $action, $viewer);
                            }

                        } else {
                            $service->postAction($tokenRow, $action, $viewer);

                        }
                    }
                }

            } catch (Exception $e) {

            }

        }
    }


    public function indexViewAction()
    {
        // Collect params
        $action_id = $this->_getParam('action_id');
        $this->view->message = $action_id;
        $action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($action_id);
        if (!$action) {
            $this
                ->add($this->component()->html($this->view->translate('Activity Item Not Found')))
                ->renderContent();
            return;
        }


        Engine_Api::_()->core()->setSubject($action);


        if ($this->_getParam('comments', false) == 'write' && (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') || !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'view')))
            $this->add($this->component()->tip($this->view->translate('You are not authorized to access this resource.')))
                ->renderContent();
        else
            $this
                ->add($this->component()->feed())
                ->add($this->component()->comments())
                ->renderContent();
    }

    /**
     * Handles HTTP request to get an activity feed item's likes and returns a
     * Json as the response
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/viewlike
     *
     * @return void
     */
    public function indexViewlikeAction()
    {
        // Collect params
        $action_id = $this->_getParam('action_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);


        // Redirect if not json context
        if (null === $this->_getParam('format', null)) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_getParam('format', null)) {
            $this->view->body = $this->view->activity($action, array('viewAllLikes' => true, 'noList' => $this->_getParam('nolist', false)));
        }
    }

    /**
     * Handles HTTP request to like an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/like
     *   *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function indexLikeAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);

            // Action
            if (!$comment_id) {

                // Check authorization
                if ($action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to like this item');
                }

                $action->likes()->addLike($viewer);

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
                        'label' => 'post'
                    ));
                }

            } // Comment
            else {
                $comment = $action->comments()->getComment($comment_id);

                // Check authorization
                if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to like this item');
                }

                $comment->likes()->addLike($viewer);

                // @todo make sure notifications work right
                if ($comment->poster_id != $viewer->getIdentity()) {
                    Engine_Api::_()->getDbtable('notifications', 'activity')
                        ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                            'label' => 'comment'
                        ));
                }

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

                }
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
            if (Engine_Api::_()->apptouch()->isApp()) {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.likes');
            } else {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.likes');
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->redirect($this->view->url(array(), 'default', true));
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->view->liked = $action->likes()->isLike($this->view->viewer());
            $this->view->like_count = $action->likes()->getLikeCount();;
        }
    }

    /**
     * Handles HTTP request to remove a like from an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/unlike
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function indexUnlikeAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);

            // Action
            if (!$comment_id) {

                // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to unlike this item');
                }

                $action->likes()->removeLike($viewer);
            } // Comment
            else {
                $comment = $action->comments()->getComment($comment_id);

                // Check authorization
                if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to like this item');
                }

                $comment->likes()->removeLike($viewer);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->redirect($this->view->url(array(), 'default', true));
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->view->liked = $action->likes()->isLike($this->view->viewer());
            $this->view->like_count = $action->likes()->getLikeCount();;
        }
    }

    /**
     * Handles HTTP request to get an activity feed item's comments and returns
     * a Json as the response
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/viewcomment
     *
     * @return void
     */
    public function indexViewcommentAction()
    {
        // Collect params
        $action_id = $this->_getParam('action_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
        $form = $this->view->form = new Activity_Form_Comment();
        $form->setActionIdentity($action_id);


        // Redirect if not json context
        if (null === $this->_getParam('format', null)) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_getParam('format', null)) {
            $this->view->body = $this->view->activity($action, array('viewAllComments' => true, 'noList' => $this->_getParam('nolist', false)));
        }
    }

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/comment
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function indexCommentAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Make form
        $this->view->form = $form = new Activity_Form_Comment();

        // Not post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }

        // Not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data5');
            return;
        }

        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
            if (!$action) {
                $this->view->status = false;
                $this->view->error = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
                return;
            }
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            $body = $form->getValue('body');

            // Check authorization
            if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
                throw new Engine_Exception('This user is not allowed to comment on this item.');

            // Add the comment
            $action->comments()->addComment($viewer, $body);

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
                    'label' => 'post'
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
                        'label' => 'post'
                    ));
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
                        'label' => 'post'
                    ));
                }
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
            if (Engine_Api::_()->apptouch()->isApp()) {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.comments');
            } else {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.comments');
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Assign message for json
        $this->view->status = true;
        $this->view->message = 'Comment posted';

        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
            $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
            $this->view->body = $this->view->activity($action, array('noList' => true));
        }
    }

    /**
     * Handles HTTP POST request to share an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/share
     *
     * @return void
     */
    public function indexShareAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $type = $this->_getParam('type');
        if ($this->_hasParam('id'))
            $id = $this->_getParam('id');
        else if ($this->_hasParam($type . '_id'))
            $id = $this->_getParam($type . '_id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $attachment = Engine_Api::_()->getItem($type, $id);
        $form = new Apptouch_Form_Share();

        if (!$attachment) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
            return;
        }


        // hide facebook and twitter option if not logged in
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        if (!$facebookTable->isConnected()) {
            $form->removeElement('post_to_facebook');
        }

        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        if (!$twitterTable->isConnected()) {
            $form->removeElement('post_to_twitter');
        }


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

        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            // Get body
            $body = $form->getValue('body');

            $params = array(
                'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
            );

            // Add activity
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
            $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
            if ($action) {
                $api->attachActivity($action, $attachment);
            }
            $db->commit();

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
                    'label' => $attachment->getMediaType(),
                ));
            }


            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($form->getValue('body'));
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment->getHref();
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                    preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))
                ) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = $action->getHref();
            }
            // Check to ensure proto/host
            if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')
            ) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')
            ) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                    . ": " . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }


            // Publish to facebook, if checked & enabled
            if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable
            ) {
                try {

                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebookApi = $facebook = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid &&
                        $fb_uid->facebook_uid &&
                        $facebookApi &&
                        $facebookApi->getUser() &&
                        $facebookApi->getUser() == $fb_uid->facebook_uid
                    ) {
                        $fb_data = array(
                            'message' => $publishMessage,
                        );
                        if ($publishUrl) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if ($publishName) {
                            $fb_data['name'] = $publishName;
                        }
                        if ($publishDesc) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if ($publishPicUrl) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            } // end Facebook

            // Publish to twitter, if checked & enabled
            if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable
            ) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {

                        // Get attachment info
                        $title = $attachment->getTitle();
                        $url = $attachment->getHref();
                        $picUrl = $attachment->getPhotoUrl();

                        // Check stuff
                        if ($url && false === stripos($url, 'http://')) {
                            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
                        }
                        if ($picUrl && false === stripos($picUrl, 'http://')) {
                            $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
                        }

                        // Try to keep full message
                        // @todo url shortener?
                        $message = html_entity_decode($form->getValue('body'));
                        if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                            if ($picUrl) {
                                $message .= ' - ' . $picUrl;
                            }
                        } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        } else {
                            if (strlen($title) > 24) {
                                $title = Engine_String::substr($title, 0, 21) . '...';
                            }
                            // Sigh truncate I guess
                            if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
                            }
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        }

                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($message);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }


            // Publish to janrain
            if ( //$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable
            ) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();

                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;

                } catch (Exception $e) {
                    // Silence
                }
            }


            if ($this->isWall) {

                $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall');
                $stream_services = $this->_getParam('share');

                foreach ($stream_services as $provider => $enabled) {

                    if (!$enabled) {
                        continue;
                    }
                    $tokenRow = $tableToken->getUserToken($viewer, $provider);
                    if (!$tokenRow) {
                        continue;
                    }
                    $service = Engine_Api::_()->wall()->getServiceClass($provider);
                    if (!$service->check($tokenRow)) {
                        continue;
                    }
                    $service->postAction($tokenRow, $action, $viewer);

                }

            }


        } catch (Exception $e) {
            $db->rollBack();
            throw $e; // This should be caught by error handler
        }

        // If we're here, we're done
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');

        // Redirect if in normal context
        $return_url = $form->getValue('return_url', false);
        if (!$return_url) {
            if ($this->view->viewer()->getIdentity())
                $return_url = $this->view->url(array('action' => 'home'), 'user_general', true);
            else
                $return_url = $this->view->url(array(), 'default', true);
        }
        return $this->redirect($return_url);
    }

    /**
     * Handles HTTP POST request to delete a comment or an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /activity/index/delete
     *
     * @return void
     */
    public function indexDeleteAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');


        // Identify if it's an action_id or comment_id being deleted
        $this->view->comment_id = $comment_id = $this->_getParam('comment_id', null);
        $this->view->action_id = $action_id = $this->_getParam('action_id', null);

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
        if (!$action) {
            // tell smoothbox to close
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
            $this->view->smoothboxClose = true;
            return $this->render('deletedItem');
        }

        // Send to view script if not POST
        if (!$this->getRequest()->isPost())
            return;


        // Both the author and the person being written about get to delete the action_id
        if (!$comment_id && (
                $activity_moderate ||
                ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
                ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id))
        ) // commenter
        {
            // Delete action item and all comments/likes
            $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
            $db->beginTransaction();
            try {
                $action->deleteItem();
                $db->commit();

                // tell smoothbox to close
                $this->view->status = true;
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');
                $this->view->smoothboxClose = true;
                return $this->render('deletedItem');
            } catch (Exception $e) {
                $db->rollback();
                $this->view->status = false;
            }

        } elseif ($comment_id) {
            $comment = $action->comments()->getComment($comment_id);
            // allow delete if profile/entry owner
            $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
            $db->beginTransaction();
            if ($activity_moderate ||
                ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)
            ) {
                try {
                    $action->comments()->removeComment($comment_id);
                    $db->commit();
                    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted');
                    return $this->render('deletedComment');
                } catch (Exception $e) {
                    $db->rollback();
                    $this->view->status = false;
                }
            } else {
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
                return $this->render('deletedComment');
            }

        } else {
            // neither the item owner, nor the item subject.  Denied!
            $this->_forward('requireauth', 'error', 'core');
        }

    }

    public function indexGetLikesAction()
    {
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');

        if (!$action_id ||
            !$comment_id ||
            !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
            !($comment = $action->comments()->getComment($comment_id))
        ) {
            $this->view->status = false;
            $this->view->body = '-';
            return;
        }

        $likes = $comment->likes()->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }

//  notifications Controller {
    public function notificationsInit()
    {
        $this->_helper->requireUser();
        $this->notificationTypes = array(
            'commented' => 'chat',
            'commented_commented' => 'chat',
            'advgroup_discussion_reply' => 'chat',
            'event_discussion_reply' => 'chat',
            'forum_topic_reply' => 'chat',
            'group_discussion_reply' => 'chat',
            'ynevent_discussion_reply' => 'chat',

            'advgroup_approve' => 'check',
            'event_approve' => 'check',
            'group_approve' => 'check',
            'pageevent_approved' => 'check',
            'ynevent_approve' => 'check',


            'liked' => 'like',
            'liked_commented' => 'like',

            'advgroup_accepted' => 'plus',
            'event_accepted' => 'plus',
            'friend_accepted' => 'plus',
            'friend_follow_accepted' => 'plus',
            'group_accepted' => 'plus',
            'pageevent_accepted' => 'plus',
            'ynevent_accepted' => 'plus',

        );
    }

    public function notificationsIndexAction()
    {
        $this->addPageInfo('contentTheme', 'd');

        $viewer = Engine_Api::_()->user()->getViewer();

        $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
        $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
        $notifications->setCurrentPageNumber($this->_getParam('page', 1));

        // Force rendering now
        $this->_helper->viewRenderer->postDispatch();
        $hasunread = false;

        // Now mark them all as read
        $ids = array();
        $updatesEl = $this->dom()->new_('ul', array('data-role' => 'listview'));
        $updatesFormat = array();
        foreach ($notifications as $notification) {

//      if(!$this->hasItemType($notification->object_type))
//        continue;

            $itemFormat = array(
                'creation_date' => $this->view->timestamp(strtotime($notification->date)),
                'descriptions' => array(),
                'href' => $notification->getObject()->getHref(),
                'title' => $notification . '',
                'photo' => $notification->getSubject()->getPhotoUrl('thumb.normal'),
                'attrsLi' => array(
                    'data-icon' => $this->getNotificationIcon($notification),
                    'actionid' => $notification->getIdentity()
                )
            );
            if (!$notification->read) {

                $itemFormat['attrsLi']['data-theme'] = 'e';
//        $itemFormat['attrsA']['class'] = @$itemFormat['attrsA']['class'] . ' ui-btn-active';
                $hasunread = true;
            }
            $updatesFormat[] = $itemFormat;
        }
        $requestsFormat = array();
        foreach ($requests as $notification) {
//      if( !Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationType($notification->type) ) {
//        continue;
//      }
            $itemFormat = array(
                'creation_date' => $this->view->timestamp(strtotime($notification->date)),
                'descriptions' => array(),
                'href' => $notification->getSubject()->getHref(),
                'title' => $notification . '',
                'photo' => $notification->getSubject()->getPhotoUrl('thumb.normal'),
                'attrsLi' => array(
                    'data-icon' => $this->getNotificationIcon($notification)
                )
            );
            $requestsFormat[] = $itemFormat;
        }

        $itemCount = $requests->getTotalItemCount();

        $switcherEl = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal', 'data-mini' => true, 'style' => 'text-align: center', 'class' => 'switcher'), '', array(
            $this->dom()->new_('a', array('class' => 'showNotifications', 'data-role' => 'button', 'data-icon' => 'flag', 'href' => 'javascript:void(0);'), $this->view->translate('APPTOUCH_Updates')),
            $this->dom()->new_('a', array('class' => 'showRequests', 'data-role' => 'button', 'data-icon' => 'question', 'href' => 'javascript:void(0);'), $this->view->translate("Requests")),
        ));
        if (count($updatesFormat) || $itemCount) {
            $this->setFormat('browse')
                ->add($this->component()->html($switcherEl));
        }
        if (count($updatesFormat)) {
          $paginatorPages = $notifications->getPages();

          $this
            ->setPageTitle($this->view->translate('Recent Updates'))
            ->addPageInfo('notifications', array(
              'markreadAction' => $this->view->url(array('module' => 'activity', 'controller' => 'notifications', 'action' => 'markread'), 'default', true),
              'notificationCount' => (int)Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer),
              'messagesCount' => (int)Engine_Api::_()->messages()->getUnreadMessageCount($viewer)
            ))
            ->add($this->component()->customComponent('itemList', array(
//              'listPaginator' => true,
              'pageCount' => $paginatorPages->pageCount,
              'next' => @$paginatorPages->next,
              'paginationParam' => 'page',
              'attrs' => array(
                'data-filter' => true,
                'data-filter-placeholder' => $this->view->translate('Search'),
                'data-filter-theme' => 'c',
//          'data-inset' => true,
                'class' => 'notifications_leftside',
                'listName' => 'notificationsList'
              ),
              'items' => $updatesFormat
            )))
//            ->add($this->component()->paginator($notifications))
          ;
        }

        else
            $this
                ->add($this->component()->tip($this->view->translate('You have no notifications.'), '', array(
                    'class' => 'notifications_leftside'
                )));

        if ($hasunread)
            $this->add(
                $this->component()->html(
                    $this->dom()->new_(
                        'a',
                        array(
                            'class' => 'notifications_leftside mark-all-read',
                            'data-role' => 'button',
                            'data-icon' => 'check',
                            'href' => $this->view->url(
                                    array(
                                        'module' => 'activity',
                                        'controller' => 'notifications',
                                        'action' => 'hide'
                                    ),
                                    'default',
                                    true
                                )
                        ),
                        $this->view->translate('Mark All Read')
                    )->toString()
                )
            );

        if ($itemCount) {
          $paginatorPages = $requests->getPages();
          $this
            ->add($this->component()->customComponent('itemList', array(
//              'listPaginator' => true,
              'pageCount' => $paginatorPages->pageCount,
              'next' => @$paginatorPages->next,
              'paginationParam' => 'page',
              'attrs' => array(
                'data-filter' => true,
                'data-filter-theme' => 'c',
//          'data-inset' => true,
                'class' => 'notifications_rightside',
                'style' => 'display: none',
                'listName' => 'requestsList'
              ),
              'items' => $requestsFormat
            )))
            ->add($this->component()->paginator($requests))
          ;
        }
        else
            $this
                ->add($this->component()->tip($this->view->translate('You have no requests.'), '', array(
                    'class' => 'notifications_rightside',
                    'style' => 'display: none'
                )));

        $this
            ->renderContent();

        //Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer, $ids);
    }

    public function notificationsHideAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer);
        return $this->redirect($this->view->url(array('module' => 'activity', 'controller' => 'notifications', 'action' => 'index'), 'default', true));
    }

    public function notificationsMarkreadAction()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $action_id = $request->getParam('actionid', 0);

        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $notificationsTable->getAdapter();
        $db->beginTransaction();

        try {
            $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
            if ($notification) {
                $notification->read = 1;
                $notification->save();
            } else {
                $this->view->message = 'No Such Notification Found';
                $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
            if ($viewerId = $viewer->getIdentity()) {
                $this->view->viewerId = $viewerId;
                $this->view->notificationCount = (int)$notificationsTable->hasNotifications($viewer);
                $this->view->requestCount = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer)->getTotalItemCount();
                $this->view->messagesCount = (int)Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
            }
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function notificationsUpdateAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationTbl = Engine_Api::_()->getDbtable('notifications', 'activity');

        $count = 0;

        if ($this->view->viewerId = $viewer->getIdentity()) {

//            $this->view->requestCount = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer)->getTotalItemCount();
            $this->view->messagesCount = $messagesCount = (int)Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
            $this->view->notificationCount = $notificationTbl->hasNotifications($viewer) - $messagesCount;

            // Store
            if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
                $cart_table = Engine_Api::_()->getItemTable('store_cart');
                $cart = $cart_table->getCart($viewer->getIdentity());
                $this->view->productCount = $cart->getItemCount();
            } else {
                $this->view->productCount = '';
            }
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->view->notificationOnly = $request->getParam('notificationOnly', false);

        // @todo locale()->tonumber
        // array('%s update', '%s updates', $this->notificationCount), $this->locale()->toNumber($this->notificationCount));
        $this->view->text = $this->view->translate(array('%s Update', '%s Updates', $count), $count);
    }

    public function getNotificationIcon($notification)
    {
        $icon = @$this->notificationTypes[$notification->type] ? @$this->notificationTypes[$notification->type] : 'flag';
        return $icon;
    }
//  } Notification Controller
}

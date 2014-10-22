<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Apptouch_PhotoviewerController extends Apptouch_Controller_Action_Bridge
{


  /**
   * The plugin support these Photo modules
   * - Album by SocialEngine
   * - Advanced Albums by Modules2Buy
   * - Page's Albums by Hire-experts
   * - Advanced Albums by SocialEngineAddons
   * - Photos in Advanced Groups by Modules2Buy
   */

  protected $_supportItems = array(
    'album_photo',
    'advalbum_photo',
    'pagealbumphoto',
    'advgroup_photo'
  );

  protected function _checkPhoto($photo)
  {
    if (!$photo){
      return false;
    }
    if (!($photo instanceof Core_Model_Item_Abstract)){
      return false;
    }
    if (!in_array($photo->getType(), $this->_supportItems)){
      return false;
    }
    return true;
  }


  public function getPhoto($photo_id)
  {
    $photo = null;
    $isPage = $this->_getParam('type') == 'pagealbumphoto';
    if (1 == (int) $isPage){

      // Page Album
      if (Engine_Api::_()->hasItemType('pagealbumphoto')){
        try {
          $photo = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
        } catch (Exception $e){

        }
      }

    } else {

      // Album
      if (2 != (int) $isPage &&  Engine_Api::_()->hasItemType('album_photo')){
        try {
          $photo = Engine_Api::_()->getItem('album_photo', $photo_id);
          if($photo)
          return $photo;
        } catch (Exception $e){

        }
      }
      // Advanced Albums by m2b
      if (2 != (int) $isPage &&  Engine_Api::_()->hasItemType('advalbum_photo') ){
        try {
          $photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id);
          if($photo)
          return $photo;
        } catch (Exception $e){

        }
      }
      // Photos in Advanced Groups by m2b
      if (2 == (int) $isPage && Engine_Api::_()->hasItemType('advgroup_photo')){
        try {
          $photo = Engine_Api::_()->getItem('advgroup_photo', $photo_id);
        } catch (Exception $e){

        }
      }

    }

    return $photo;

  }

  public function getAlbumByPhoto($subject)
  {
    $album_id = 0;
    $album = null;
    if (isset($subject['album_id'])){
      $album_id = $subject->album_id;
    }

    // Get Album
    if ($subject->getType() == 'album_photo') {
      $album = Engine_Api::_()->getItem('album', $album_id);
    } else if ($subject->getType() == 'advalbum_photo') {
      $album = Engine_Api::_()->getItem('advalbum_album', $album_id);
    } else if ($subject->getType() == 'pagealbumphoto') {
      $album = $subject->getCollection();
    } else if ($subject->getType() == 'advgroup_photo') {
      $album = $subject->getCollection();
    } else {
      throw new Exception('Album not found');
    }
    return $album;
  }

  public function indexIndexAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'));
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $table = $subject->getTable();


    // ID key of photo (must return photo_id, pagealbumphoto_id etc)
    $matches = $table->info('primary');
    $primary = array_pop($matches);


    // get album
    $album = $this->getAlbumByPhoto($subject);
    $owner = $album->getOwner();


    // ID key of album (must return album_id, pagealbump_id etc)
    $matches = $album->getTable()->info('primary');
    $album_primary = array_pop($matches);



    // get all photos by album id

    $select = $table->select();

    if (isset($subject->{$album_primary})){
      $select->where(''.$album_primary.' = ?', $subject->{$album_primary});
    } else if (isset($subject->collection_id)){
      $select->where('collection_id = ?', $subject->collection_id);
    }

    if (isset($subject['order'])){
      $select->order('order ASC');
    }
    $select->order($primary.' ASC');




    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(600);

    $photos = array();
    foreach ($paginator as $item){

      $owner = $item->getOwner();

      $photos[] = array(
        'photo_id' => $item->getIdentity(),
        'guid' => $item->getGuid(),
        'thumb' => $item->getPhotoUrl('thumb.normal'),
        'src' => $item->getPhotoUrl(),
        'active' => ($item->getIdentity() == $subject->getIdentity()),
        'title' => $item->getTitle(),
        'description' => $item->getTitle()
      );
    }


    $this->view->photos = $photos;
    $this->view->count = $paginator->getTotalItemCount();
    $this->view->album_title = $album->getTitle();
    $this->view->album_href = $album->getHref();
    $this->view->owner_title = $owner->getTitle();
    $this->view->owner_href = $owner->getHref();

  }

  public function indexCommentsAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->isPage = (int)$this->_getParam('isPage');

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $album = $this->getAlbumByPhoto($subject);

    Engine_Api::_()->core()->setSubject($subject);

    $this->view->canEdit = $canEdit = $album->authorization()->isAllowed($viewer, 'edit');
    $this->view->canDelete = $canDelete = $album->authorization()->isAllowed($viewer, 'delete');
    $this->view->canTag = $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    $this->view->canUntagGlobal = $canUntag = $album->isOwner($viewer);
    $this->view->photo = $photo = $subject;

    if( !$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer) && isset($photo->view_count)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

    // Get tags
    $tags = array();
    foreach( $photo->tags()->getTagMaps() as $tagmap ) {
      $tags[] = array_merge($tagmap->toArray(), array(
        'id' => $tagmap->getIdentity(),
        'text' => $tagmap->getTitle(),
        'href' => $tagmap->getHref(),
        'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $this->view->tags = $tags;
  }
  public function detectFacesAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if($viewer->getIdentity()){
      if($this->_hasParam('type') && $this->_hasParam('type') && $item = Engine_Api::_()->getItem($this->_getParam('type'), $this->_getParam('photo_id'))){
        $detectedFaces = Engine_Api::_()->getDbTable('faces', 'apptouch')->getFaces($item);
        $friends = $this->getFriends($item);
        $c = $friends->getTotalItemCount();
        if($c){
          $t = $this->view->translate('APPTOUCH_Type any text');
          $h = '<ul data-role="listview" data-filter="true"><li data-ajax="'.((boolean)($c > 100)).'" contenteditable="true" placeholder="'.$t.'" class="text">'.$t.'</li>';

          foreach($friends as $friend){
            $h .= '<li class="friend selected" data-title="'.strtolower($friend->getTitle()).'" data-userid="'.$friend->getIdentity().'">' . $friend->getTitle() . '</li>';
          }
          $h .= '</ul>';
          $this->view->friendList = $h;
        }
        $tags = array();
        $photo = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
        $faces = $detectedFaces['tags'];
        foreach( $photo->tags()->getTagMaps() as $tagmap ) {
          $tags[] = $tag = array_merge($tagmap->toArray(), array(
            'id' => $tagmap->getIdentity(),
            'text' => $tagmap->getTitle(),
            'href' => $tagmap->getHref(),
            'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
          ));
          if($coords = $tag['extra']){
            $tx = $tag['extra']['x'];
            $ty = $tag['extra']['y'];
            $tw = $tag['extra']['w'];
            $th = $tag['extra']['h'];
            foreach($faces as $index => $face){
              $fx = $face['x'];
              $fy = $face['y'];
              $fw = $face['width'];
              $fh = $face['height'];
              if($fx == $tx && $fy == $ty){
                $faces[$index] = null;
                continue;
              }
              if($th * $tw > $fh * $fw){
                $min = array($fh, $fw);
              } else {
                $min = array($th, $tw);
              }
              $dx = abs($fx - $tx + ($fw - $tw) / 2);
              $dy = abs($fy - $ty + ($fh - $th) / 2);
              if($min[1]/2 > $dx && $min[0]/2 > $dy){
                $faces[$index] = null;
              }
            }
            $faces = array_values(array_filter($faces));
          }
        }
        $this->view->tags = $tags;
        $this->view->detected = $faces;
      }
    }
  }
  public function indexFriendsAction(){
    if($this->_hasParam('type') && $this->_hasParam('type') && $item = Engine_Api::_()->getItem($this->_getParam('type'), $this->_getParam('photo_id'))){
      $friends =$this->getFriends($item);
      $friends->setCurrentPageNumber($this->_getParam('page', 1));
      $h = '';
      foreach($friends as $friend){
        $h .= '<li class="friend selected" data-title="'.strtolower($friend->getTitle()).'" data-userid="'.$friend->getIdentity().'">' . $friend->getTitle() . '</li>';
      }
    }
    $this->view->pageCount = round($friends->getTotalItemCount() / $friends->getItemCountPerPage() + .5);
    $this->view->keyword = $this->_getParam('keyword');
    $this->view->friendList = $h;
  }
  private function getFriends($item){
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $photo_type = $item->getType();
    $photo_id = $item->getIdentity();
    $set = <<<SET
`engine4_users`.`user_id` IN (select `members`.`user_id` from `engine4_user_membership` as `members` where `members`.`resource_id` = {$viewer_id} and `members`.`active` = 1 and `members`.`user_id` not in (select `tag_id` from `engine4_core_tagmaps` where `resource_type` = '{$photo_type}' and `resource_id` = {$photo_id} and `tag_type` = 'user'))
SET;
$ut = Engine_Api::_()->getDbTable('users', 'user');
    $s = $ut->select()->where($set)->order('displayname');
    if($keyword = $this->_getParam('keyword')){
      $s->where('displayname like ?', '%' . $keyword. '%');
    }
    $friends = Zend_Paginator::factory($s);
    $friends->setItemCountPerPage(100);
    return $friends;
  }
  public function indexDownloadAction()
  {
    $subject = $this->getPhoto($this->_getParam('photo_id'), $this->_getParam('isPage'));
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check Photo
    if (!$this->_checkPhoto($subject)){
      $this->view->message = 'Invalid Photo';
      $this->view->status = false;
      return ;
    }

    // Check privacy
    if ($subject->getType() == 'pagealbumphoto') {
      $authSubject = $subject->getPage();
    } else {
      $authSubject = $subject;
    }

    if (!$authSubject->authorization()->isAllowed($viewer, 'view')) {
      $this->view->message = 'The photo is private';
      $this->view->status = false;
      return;
    }

    $album = $this->getAlbumByPhoto($subject);

    Engine_Api::_()->core()->setSubject($subject);

    $file = Engine_Api::_()->getItem('storage_file', $subject->file_id);
    if (!$file){
      throw new Exception('File is not available');
    }
    $file_patch = APPLICATION_PATH .'/'. $file->storage_path;

    if (file_exists($file_patch) && is_readable($file_patch)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file_patch));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_patch));
        ob_clean();
        flush();
        readfile($file_patch);
        exit;
    } else {
      throw new Exception('File is not exists');
    }

  }

}
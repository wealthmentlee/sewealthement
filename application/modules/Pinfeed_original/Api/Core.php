<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Pinfeed_Api_Core extends Activity_Api_Core
{
  // overridden
  public function getPluginLoader()
  {
    if( null === $this->_pluginLoader )
    {
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
          . 'modules' . DIRECTORY_SEPARATOR
          . 'Wall';

      $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
        'Wall_Model_Helper_' => $path . '/Model/Helper/'
      ));
    }

    return $this->_pluginLoader;
  }

  public function assemble($body, array $params = array(), $action = null)
  {
    // Translate body
    $body = $this->getHelper('translate')->direct($body);

    // Do other stuff
    preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
    foreach( $matches as $match )
    {
      $tag = $match[0];
      $args = explode(':', $match[1]);
      $helper = array_shift($args);

      $helperArgs = array();
      foreach( $args as $arg )
      {
        if( substr($arg, 0, 1) === '$' )
        {
          $arg = substr($arg, 1);
          $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
        }
        else
        {
          $helperArgs[] = $arg;
        }
      }

      $helper = $this->getHelper($helper);
      if ($action){
        $helper->setAction($action);
      }
      $r = new ReflectionMethod($helper, 'direct');
      $content = $r->invokeArgs($helper, $helperArgs);
      $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
      $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
    }


    return $body;
  }


  protected $manifest_types = array();
  protected $settings = array();


  public function getManifestType($type, $get_keys = false)
  {
    if (isset($this->manifest_types[$type])){
      if ($get_keys){
        return array_keys($this->manifest_types[$type]);
      }
      return $this->manifest_types[$type];
    }

    $modules = Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames();
    $data_array = array();
    foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
      if( empty($data[$type]) ) {
        continue;
      }
      foreach ($data[$type] as $subdata){

        // is enabled module
        if (isset($subdata['module'])){
          if (is_array($subdata['module'])){
            $intersect = array_intersect($subdata['module'], $modules);
            if (empty($intersect)){
              continue ;
            }
          }
          if (is_string($subdata['module']) && !in_array($subdata['module'], $modules)){
            continue ;
          }
        }
        if (empty($subdata['type'])){
          continue ;
        }
        $key = $subdata['type'];
        $data_array[$key] = $subdata;
      }
    }

    $this->manifest_types[$type] = $data_array;

    if ($get_keys){
      return array_keys($data_array);
    }

    return $data_array;
  }


  public function setItemsType($items)
  {
    $types = array();
    foreach ($items as $item){

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($types[$type])){
        $types[$type] = array();
      }
      $types[$type][] = $id;

    }
    return $types;
  }


  public function setItemsGuid($items)
  {
    $new_items = array();
    foreach ($items as $item){

      $guid = $item['type'] . '_' . $item['id'];

      if (!isset($new_items[$guid])){
        $new_items[$guid] = array();
      }
      $new_items[$guid] = $item;

    }
    return $new_items;
  }


  public function guidsToItems($guids)
  {
    $items = array();
    if (!empty($guids)){
      foreach ($guids as $guid){
        $parts = explode('_', $guid);
        if (count($parts) == 2){
          $items[] = array(
            'type' => $parts[0],
            'id' => $parts[1]
          );
        }
      }
    }
    return $items;
  }


  public function getItems($items)
  {
    $item_array = array();

    foreach ($this->setItemsType($items) as $type => $ids){

      if (!Engine_Api::_()->hasItemType($type)){
        continue ;
      }
      $table = Engine_Api::_()->getItemTable($type);

      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary){
        continue ;
      }

      foreach ($this->getTableItems($table, $ids) as $item){
        if (!isset($item_array[$type])){
          $item_array[$type] = array();
        }
        $item_array[$type][$item->{$primary}] = $item;
      }

    }

    $ready_items = array();
    foreach ($items as $item){

      $type = $item['type'];
      $id = $item['id'];

      if (!isset($item_array[$type]) || !isset($item_array[$type][$id])){
        continue ;
      }
      $ready_items[] = $item_array[$type][$id];
    }

    return $ready_items;

  }


  public function getTableItems(Zend_Db_Table_Abstract $table, $ids)
  {
    try
    {
      $matches = $table->info('primary');
      $primary = array_shift($matches);
      if (!$primary){
        return ;
      }
      $select = $table->select()
          ->where("$primary IN (?)", $ids);

      return $table->fetchAll($select);

    } catch (Exception $e)
    {
      return ;
    }
  }


  public function getHostUrl()
  {
    return (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
  }


  public function getUrl($params, $route = null, $reset = null)
  {
    $host_url = Engine_Api::_()->wall()->getHostUrl();
    $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);

    return $host_url . $url;
  }


  public function getUrlContent($url, $postdata = null, $ssl = false)
  {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    if ($ssl){
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    }
    if ($postdata){
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    }
    $content = curl_exec($curl);
    curl_close($curl);

    if ($content === NULL && !$postdata){
      $content = file_get_contents($url);
    }

    return $content;
  }


  public function getService($provider)
  {
    if (!$provider){
      return ;
    }
    $services = Engine_Api::_()->wall()->getManifestType('wall_service');
    if (!array_key_exists($provider, $services)){
      return ;
    }
    return $services[$provider];
  }


  public function getServiceClass($provider)
  {
    $service = $this->getService($provider);
    if (!$service){
      return ;
    }
    return Engine_Api::_()->loadClass(@$service['plugin']);
  }


  public function isPhotoType($type)
  {
    $album_types = array(
      'album_photo',
      'event_photo',
      'group_photo',
      'profile_photo',
      'pagealbumphoto',
      'advalbum_photo'
    );

    if (in_array($type, $album_types)){
      return true;
    }
    return false;
  }



  public function getMutualFriendsPaginator(User_Model_User $subject, User_Model_User $user)
  {
    $table = Engine_Api::_()->getDbTable('users', 'user');
    $membership = Engine_Api::_()->getDbTable('membership', 'user')->info('name');

    $select = $table->select()
        ->from(array('u' => $table->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('m' => $membership), 'u.user_id = m.resource_id AND m.user_id = ' . $subject->getIdentity() . ' AND m.active = 1', array())
        ->join(array('vm' => $membership), 'u.user_id = vm.resource_id AND vm.user_id = '. $user->getIdentity() . ' AND vm.active = 1', array())
        ->where('u.user_id <> ?', $user->getIdentity())
        ->group('u.user_id');

    return Zend_Paginator::factory($select);
  }


  public function getItemsLike($params = array())
  {
    $subject = (isset($params['subject'])) ?
        Engine_Api::_()->getItemByGuid($params['subject'])
        : null;

    if (!$subject){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('users', 'user');
    $like_name = Engine_Api::_()->getDbTable('likes', 'core')->info('name');

    $select = $table->select()
        ->from(array('u' => $table->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('l' => $like_name), 'u.user_id = l.poster_id AND poster_type = "user"', array())
        ->where('l.resource_type = ?', $subject->getType())
        ->where('l.resource_id = ?', $subject->getIdentity())
        ->group('u.user_id');

    if (!empty($params['search'])){
      $select->where('u.username LIKE ? OR u.displayname LIKE ?', '%'. $params['search'] .'%');
    }

    return Zend_Paginator::factory($select);

  }



  public function assembleUrl($params)
  {
    $route = 'default';
    if (!empty($params['route'])){
      $route = $params['route'];
      unset($params['route']);
    }
    $reset = true;
    if (!empty($params['reset'])){
      $reset = $params['reset'];
      unset($params['reset']);
    }

    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);

  }

  var $_settings = array();

  public function getUserSetting($viewer)
  {
    if (!$viewer){
      return ;
    }
    if (!($viewer instanceof User_Model_User)){
      return ;
    }
    if (!isset($this->_settings[$viewer->getGuid()])){
      $this->_settings[$viewer->getGuid()] = Engine_Api::_()->getDbTable('userSettings', 'wall')->getUserSetting($viewer);
    }
    return $this->_settings[$viewer->getGuid()];
  }


  public function getLikeTipTypes()
  {
    return array('user','page','group','event');
  }




  public function isOwnerTeamMember($subject, $user)
  {
    $page = $this->getSubjectPage($subject);
    if (!$page){
      return ;
    }
    return $page->isTeamMember($user);
  }

  protected $_items = array();

  public function getSubjectPage($subject)
  {
    if (!$subject){
      return ;
    }
    if (!$this->isPageModule($subject->getType())){
      return ;
    }
    if (!isset($subject->page_id) && $subject->getType() != 'pagealbumphoto'){
      return ;
    }
    $page_id = 0;

    if ($subject->getType() == 'page'){

      $page = $subject;

    } else {

      if ($subject->getType() == 'pagealbumphoto'){ // :))

        $collection = $subject->getCollection();
        if (!$collection){
          return ;
        }
        $page_id = $collection->page_id;

      } else {
        $page_id = $subject->page_id;
      }
      $guid = 'page_' . $page_id;
      if (!isset($this->_items[$guid])){
        $this->_pages[$guid] = Engine_Api::_()->getItem('page', $page_id);
      }
      $page = $this->_pages[$guid];
    }
    if (!$page){
      return ;
    }
    return $page;
  }

  public function isPageModule($type)
  {
    if (substr($type, 0, 4) == 'page' || $type == 'playlist'){
      return true;
    }
    return false;
  }

  public function getSupportedItems()
  {
    return array(
      'user',
      'event',
      'group',
      'page',
      'job',
      'offer'
    );
  }

  protected $_privacy;


  public function getPrivacyList()
  {
    return array(
      'user' => array(
        'everyone',
        'networks',
        'members',
        'owner'
      ),
      'page' => array(
        'everyone',
        'registered',
        'page'
      )
    );
  }

  public function getPrivacy($type)
  {
    if (is_null($this->_privacy)){

      $privacy = $this->getPrivacyList();

      foreach ($privacy as $key => $item){
        $this->_privacy[$key] = $item;
      }

    }

    if (empty($this->_privacy[$type])){
      return array();
    }

    return $this->_privacy[$type];

  }


  public function getItemTable($type)
  {
    if ($type == 'activity_action'){
      return Engine_Api::_()->getDbTable('actions', 'wall');
    }
  }



  public function replaceStream(Activity_Model_Action $action, $old_stream)
  {
    $new_stream = array();

    $table = Engine_Api::_()->getDbTable('privacy', 'wall');
    $select = $table->select()
        ->where('action_id = ?', $action->getIdentity());

    $privacy = $table->fetchRow($select);

    if (!$privacy){
      return $old_stream;
    }

    foreach ($old_stream as $item){
      if (in_array($item['type'], array('owner', 'parent', 'network', 'members', 'registered', 'everyone'))){
        continue ;
      }
      $new_stream[] = $item;
    }

    $subject = $action->getSubject();
    $object = $action->getObject();

    $subjectOwner = null;
    if( $subject instanceof User_Model_User ) {
      $subjectOwner = $subject;
    } else {
      try {
        $subjectOwner = $subject->getOwner('user');
      } catch( Exception $e ) {}
    }

    $objectParent = null;
    if( $object instanceof User_Model_User ) {
      $objectParent = $object;
    } else {
      try {
        $objectParent = $object->getParent('user');
      } catch( Exception $e ) {}
    }

    if( $subjectOwner instanceof User_Model_User ) {
      $new_stream[] = array(
        'type' => 'owner',
        'identity' => $subjectOwner->getIdentity()
      );
    }

    if( $objectParent instanceof User_Model_User ) {
      $new_stream[] = array(
        'type' => 'parent',
        'identity' => $objectParent->getIdentity()
      );
    }

    if( in_array($privacy->privacy, array('everyone', 'networks')) ) {
      if ($object instanceof User_Model_User ) { // Engine_Api::_()->authorization()->context->isAllowed($object, 'network', 'view')
        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
        $ids = $networkTable->getMembershipsOfIds($object);
        $ids = array_unique($ids);
        foreach( $ids as $id ) {
          $new_stream[] = array(
            'type' => 'network',
            'identity' => $id,
          );
        }
      } elseif ($objectParent instanceof User_Model_User ) { // Engine_Api::_()->authorization()->context->isAllowed($object, 'owner_network', 'view')
        $networkTable = Engine_Api::_()->getDbtable('membership', 'network');
        $ids = $networkTable->getMembershipsOfIds($objectParent);
        $ids = array_unique($ids);
        foreach( $ids as $id ) {
          $new_stream[] = array(
            'type' => 'network',
            'identity' => $id,
          );
        }
      }
    }

    if( in_array($privacy->privacy, array('everyone', 'networks', 'members')) ) {
      if( $object instanceof User_Model_User ) {
        /*if( Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view') ) {*/
        $new_stream[] = array(
          'type' => 'members',
          'identity' => $object->getIdentity()
        );
        //}
      } else if( $objectParent instanceof User_Model_User ) {
        /*        if( Engine_Api::_()->authorization()->context->isAllowed($object, 'owner_member', 'view') ||
    Engine_Api::_()->authorization()->context->isAllowed($object, 'parent_member', 'view') ) {*/
        $new_stream[] = array(
          'type' => 'members',
          'identity' => $objectParent->getIdentity()
        );
        //}
      }
    }

    if( $privacy->privacy == 'everyone'
    ) { // Engine_Api::_()->authorization()->context->isAllowed($object, 'registered', 'view')
      $new_stream[] = array(
        'type' => 'registered',
        'identity' => 0
      );
    }

    if( $privacy->privacy == 'everyone'
    ) { // Engine_Api::_()->authorization()->context->isAllowed($object, 'everyone', 'view')
      $new_stream[] = array(
        'type' => 'everyone',
        'identity' => 0
      );
    }


    if ( $object instanceof Page_Model_Page) {

      foreach ($new_stream as $key => $item){
        if (in_array($item['type'], array('page_feed', 'page_registered', 'page', 'registered', 'everyone'))){ // add registered, everyone
          unset($new_stream);
        }
      }

      if ($privacy->privacy == 'everyone'){
        $new_stream[] = array(
          'type' => 'page_feed',
          'identity' => 0,
        );
      }

      if (in_array($privacy->privacy, array('everyone', 'registered'))){
        $new_stream[] = array(
          'type' => 'page_registered',
          'identity' => 0,
        );
      }

      if (Engine_Api::_()->authorization()->context->isAllowed($object, 'registered', 'view')){
        $new_stream[] = array(
          'type' => 'page',
          'identity' => $object->getIdentity()
        );
      }


    }


    return $new_stream;


  }





  public function getActionFields(Activity_Model_Action $action)
  {
    $fields = array();

    $fields['site'] = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title . "";
    $fields['host'] = $this->getHostUrl();

    $fields['title'] = '';
    $fields['description'] = '';
    $fields['message'] = html_entity_decode(strip_tags($action->body));


    $fields['link'] = $this->getHostUrl() . $action->getHref();
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.bitly', 1)){
      $fields['link'] = $this->getBitlyShortUrl($this->getHostUrl() . $action->getHref());
    }




    $attachments = $action->getAttachments();
    $attachment = (!empty($attachments[0])) ? $attachments[0]->item: null;

    if ($attachment){
      $fields['title'] = strip_tags($attachment->getTitle());
      $fields['description'] = strip_tags($attachment->getDescription());
      $photo = $attachment->getPhotoUrl('thumb.icon');
      if ($photo && !preg_match('/fbcdn.net$/i', parse_url($photo, PHP_URL_HOST))){
        if (!preg_match('/http:\/\//i', $photo)){
          $photo = $this->getHostUrl() . $photo;
        }
        $fields['picture'] = $photo;
      }
      if ($attachment->getHref()){
        $fields['link'] = $this->getHostUrl() . $attachment->getHref();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.bitly', 1)){
          $fields['link'] = $this->getBitlyShortUrl($this->getHostUrl() . $attachment->getHref());
        }
      }
    }

    return $fields;
  }


  function getBitlyShortUrl($url,$format='txt')
  {
    $connectURL = 'http://api.bit.ly/v3/shorten?login=michaeluzaren&apiKey=R_fd08f3b29ebab1513ec6b20032b83ba1&uri='.urlencode($url).'&format='.$format;
    return $this->getUrlContent($connectURL);
  }



  public function getTagSuggest(Core_Model_Item_Abstract $user, $params = array())
  {


    $db = Engine_Db_Table::getDefaultAdapter();
    $like = Engine_Api::_()->getDbTable('likes', 'core');
    $user_id = $user->getIdentity();
    $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');
    $select_data = array();

    if ($moduleTable->isModuleEnabled('group')){

      $table = Engine_Api::_()->getDbTable('groups', 'group');
      $membership = Engine_Api::_()->getDbTable('membership', 'group');

      $select = $db->select()
          ->from(array('g' => $table->info('name')), new Zend_Db_Expr("'group' AS `type`, g.group_id AS id"))
          ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'group' AND l.resource_id = g.group_id AND l.poster_type = 'user' AND l.poster_id = $user_id", array())
          ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = g.group_id AND m.user_id = $user_id AND m.active = 1", array())
          ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

      if (!empty($params['search'])){
        $select->where('g.title LIKE ? OR g.description LIKE ?', '%'. $params['search'] .'%');
      }

      $select_data[] = $select;

    }

    if ($moduleTable->isModuleEnabled('user')){

      $table = Engine_Api::_()->getDbTable('users', 'user');
      $membership = Engine_Api::_()->getDbTable('membership', 'user');

      $select = $db->select()
          ->from(array('u' => $table->info('name')), new Zend_Db_Expr("'user' AS `type`, u.user_id AS id"))
          ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'user' AND l.resource_id = u.user_id AND l.poster_type = 'user' AND l.poster_id = $user_id", array())
          ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = u.user_id AND m.user_id = $user_id AND m.active = 1", array())
          ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id) OR (u.user_id = '. $user_id .')'));

      if (!empty($params['search'])){
        $select->where('u.username LIKE ? OR u.displayname LIKE ?', '%'. $params['search'] .'%');
      }

      $select_data[] = $select;

    }

    if ($moduleTable->isModuleEnabled('page')){

      $table = Engine_Api::_()->getDbTable('pages', 'page');
      $membership = Engine_Api::_()->getDbTable('membership', 'page');

      $select = $db->select()
          ->from(array('p' => $table->info('name')), new Zend_Db_Expr("'page' AS `type`, p.page_id AS id"))
          ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'page' AND l.resource_id = p.page_id AND l.poster_type = 'user' AND l.poster_id = $user_id", array())
          ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = p.page_id AND m.user_id = $user_id AND m.active = 1", array())
          ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

      if (!empty($params['search'])){
        $select->where('p.title LIKE ? OR p.description LIKE ?', '%'. $params['search'] .'%');
      }

      $select_data[] = $select;

    }


    if ($moduleTable->isModuleEnabled('event')){

      $table = Engine_Api::_()->getDbTable('events', 'event');
      $membership = Engine_Api::_()->getDbTable('membership', 'event');

      $select = $db->select()
          ->from(array('e' => $table->info('name')), new Zend_Db_Expr("'event' AS `type`, e.event_id AS id"))
          ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'event' AND l.resource_id = e.event_id AND l.poster_type = 'user' AND l.poster_id = $user_id", array())
          ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = e.event_id AND m.user_id = $user_id AND m.active = 1", array())
          ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

      if (!empty($params['search'])){
        $select->where('e.title LIKE ? OR e.description LIKE ?', '%'. $params['search'] .'%');
      }

      $select_data[] = $select;

    }


    $union = $db->select();

    foreach ($select_data as $select){
      $union->union(array('(' .$select->__toString() . ')'));
    }

    $union->order('id DESC');


    return $union;


  }


  public function getSuggestPeople($user, $params = array())
  {

    $db = Engine_Db_Table::getDefaultAdapter();
    $like = Engine_Api::_()->getDbTable('likes', 'core');
    $user_id = $user->getIdentity();

    $table = Engine_Api::_()->getDbTable('users', 'user');
    $membership = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $db->select()
        ->from(array('u' => $table->info('name')), new Zend_Db_Expr("'user' AS `type`, u.user_id AS id"))
        ->joinLeft(array('l' => $like->info('name')), "l.resource_type = 'user' AND l.resource_id = u.user_id AND l.poster_type = 'user' AND l.poster_id = $user_id", array())
        ->joinLeft(array('m' => $membership->info('name')), "m.resource_id = u.user_id AND m.user_id = $user_id AND m.active = 1", array())
        ->where(new Zend_Db_Expr('NOT ISNULL(l.resource_id) OR NOT ISNULL(m.resource_id)'));

    if (!empty($params['search'])){
      $select->where('u.username LIKE ? OR u.displayname LIKE ?', '%'. $params['search'] .'%');
    }

    return $select;

  }



  public function checkWidgetIsEnabled($widget)
  {
    $widget = preg_replace('/[^\d\w.-]/', '', $widget);

    $segments = explode('.', strtolower($widget));
    if( count($segments) == 2 ) {
      $name = array_pop($segments);
      $module = array_pop($segments);
    } else if( count($segments) == 1 ) {
      $name = array_pop($segments);
      $module = null;
    } else {
      return false;
    }

    if( null !== $module && !Engine_Api::_()->hasModuleBootstrap($module) ) {
      return false;
    }

    return true;

  }


  public function getPageLink($viewer, $page_id)
  {
    if (!$viewer->getIdentity()){
      return ;
    }
    $page = Engine_Api::_()->getItem('page', $page_id);
    if (empty($page)){
      return ;
    }
    if (!$page->isTeamMember($viewer)){
      return ;
    }
    $link_data = Engine_Api::_()->getDbTable('settings', 'user')->getSetting($viewer, 'wall-linked-pages');

    if (empty($link_data)){
      return ;
    }
    $link_data = unserialize($link_data);


    if (empty($link_data)){
      return ;
    }
    foreach ($link_data as $item_page_id => $fbpage_id){
      if ($page_id == $item_page_id){
        return $fbpage_id;
      }
    }
    return ;
  }

  public function setPageLink($viewer, $page_id, $fbpage_id)
  {
    if (!$viewer->getIdentity()){
      return ;
    }
    $page = Engine_Api::_()->getItem('page', $page_id);
    if (empty($page)){
      return ;
    }
    if (!$page->isTeamMember($viewer)){
      return ;
    }
    $link_data = Engine_Api::_()->getDbTable('settings', 'user')->getSetting($viewer, 'wall-linked-pages');
    if (empty($link_data)){
      $link_data = array();
    } else {
      $link_data = unserialize($link_data);
    }
    $link_data[$page_id] = $fbpage_id;

    $link_data = serialize($link_data);

    Engine_Api::_()->getDbTable('settings', 'user')->setSetting($viewer, 'wall-linked-pages', $link_data);

  }


}
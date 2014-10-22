<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: StoretController.php 3.9.14 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_UltimatenewsController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexIndexAction()
  {
    $this->_setParam('start_date', '');
    $this->_setParam('end_date', '');

    $array = $this->getRequest()->getPost();
    $subject = Engine_Api::_()->getItem('ultimatenews_param', 1);
    if ($array)
    {
      if (isset($array['search']))
        $subject->search = $array['search'];
      else
        $subject->search = "";

      if (isset($array['category']))
        $subject->category = $array['category'];
      else
        $subject->category = "0";

      if (isset($array['page']))
        $subject->page = $array['page'];
      elseif (isset($array['nextpage']))
        $subject->page = $array['nextpage'];
      else
        $subject->page = "1";
    }
    Engine_Api::_()->core()->setSubject($subject);
    $this->indexListAction();
  }

  public function indexListAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $ultimatenews_search_query = "";
    if (isset($_POST['categoryparent']) && $_POST['categoryparent'] > -1)
    {
      $category_parent = $_POST['categoryparent'];
    }
    else
    {
      $category_parent = -1;
    }
    if (isset($_REQUEST['category']) && !empty($_REQUEST['category']))
    {
      $category_id = $_REQUEST['category'];
    }
    else
    {
      $category_id = 0;
    }

    if (!isset($_REQUEST['search']) && empty($_REQUEST['search']))
    {
      $searchText = "";
    }
    else
    {
      $searchText = $_REQUEST['search'];
      $ultimatenews_search_arr = explode(" ", $searchText);
      $ultimatenews_searchs = array();
      foreach ($ultimatenews_search_arr as $item)
      {
        if ($item != "")
        {
          $ultimatenews_searchs[] = $item;
        }
      }
      $ultimatenews_search_query = implode("%", $ultimatenews_searchs);
      $ultimatenews_search_query = "%" . $ultimatenews_search_query . "%";
    }
    if (isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
    {
      $page = $_POST['nextpage'];
    }
    else
    {
      $page = 1;
    }
    $keysearch = array('nextpage' => $page, 'category' => $category_id, 'category_parent' => $category_parent,'searchText' => $ultimatenews_search_query, 'keyword' => $searchText);

    if ( !empty($keysearch) ) {
      $category = $keysearch['category'];
      $ultimatenews_search_query = $keysearch['searchText'];
      $category_parent = $keysearch['category_parent'];
      $page = $keysearch['nextpage'];
      $start_date = $this->_getParam('start_date', '');
      $end_date = $this->_getParam('end_date', '');
    }
    else
    {
      $category = $this->_getParam('category', 0);
      $category_parent = $this->_getParam('categoryparent', -1);
      $page = "1";
      if (isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
      {
        $page = $_POST['nextpage'];
      }
      $searchText = $_POST['search'];
      $ultimatenews_search_arr = explode(" ", $searchText);
      $ultimatenews_searchs = array();
      foreach ($ultimatenews_search_arr as $item)
      {
        if ($item != "")
        {
          $ultimatenews_searchs[] = $item;
        }
      }
      $ultimatenews_search_query = implode("%", $ultimatenews_searchs);
      $ultimatenews_search_query = "%" . $ultimatenews_search_query . "%";
      $_SESSION['category'] = $category;
      $_SESSION['category_parent'] = $category_parent;
      $_SESSION['searchText'] = $ultimatenews_search_query;
    }

    $wide = $this->_getParam('wide', 3);//number of article in wide area
    $narrow = $this->_getParam('narrow', 7);//number of article in narrow area
    $number_article = $wide + $narrow;//number of articles per widget
    $categories_per_page = $this->_getParam('categories_per_page');//number of feeds per page

    $paginator = Engine_Api::_()->ultimatenews()->getCategoriesByNewsFilter(array(
      'category_id' => $category,
      'number_article' => $number_article,
      'search' => $ultimatenews_search_query,
      //'order' => 'pubDate DESC',
      'is_active' => 1,
      'getcommment' => true,
      'start_date' => $start_date,
      'end_date' => $end_date,
      'group' => 'feed',
      'limit' => $categories_per_page,
      'category_parent' => $category_parent,
    ));

    $paginator->setItemCountPerPage($categories_per_page);
    $paginator->setCurrentPageNumber($page);

    $this->setPageTitle($this->view->translate('Ultimate News'));

//    $this->addNavigation();

    $form = $this->getSearchForm();
    $this->add($this->component()->itemSearch($form));

    $subscribe = false;
    if(Engine_Api::_() -> authorization() -> isAllowed('ultimatenews', $viewer, 'subscribe')) {
      $subscribe = true;
    }

    $select = Engine_Api::_()->ultimatenews()->getCategoryparentsSelect(array('category_active'=> 1));
    $table = Engine_Api::_()->getItemTable('categoryparents');
    $categories = $categories = $table->fetchAll($select);
    $cats = '';
    foreach( $categories as $cat ) {
      $cats .= '<a href="' .
        $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent') .
        '" class="btn">'.
        (strlen($this->view->translate($cat->category_name))>22?substr($this->view->translate($cat->category_name),0,19).'...':$this->view->translate($cat->category_name)).
        '</a> ';
    }
    $cats .= '<a href="' .
      $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>0 ),'ultimatenews_categoryparent') .
      '" class="btn">'.
      $this->view->translate("Other").
      '</a> ';
    $catElements = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-collapsed' => true, 'data-content-theme' => 'b', 'data-theme' => 'b'), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('Categories')),
      $this->dom()->new_('p', array(), $cats)
    ));

    $this->add($this->component()->html($catElements));

    $isTablet = Engine_Api::_()->apptouch()->isTablet();
    foreach( $paginator as $cateItem ) {
      $component = array();
      $items = array();

      $param = (!empty($keysearch)) ? (array(
        'category_id' => $keysearch['category'],
        'search' => $keysearch['searchText'],
        'category_parent' => $keysearch['category_parent'],
        'page' => $keysearch['nextpage'],
        'start_date' => $this->_getParam('start_date'),
        'end_date' => $this->_getParam('end_date'),
        'order' => 'pubDate DESC',
        'getcommment' => true,
        'limit' => $wide + $narrow
      )) : (array(
        'limit' => $wide + $narrow
      )) ;
      $contents = $cateItem->getTopContents($param);

      for( $i = 0; $i < $wide; $i++ ) {
        if( !isset($contents[$i]) )
          continue;
        $item_news = $contents[$i];
        if( !is_object($item_news) )
          continue;
        $photoUrl = $item_news->image != "" ? $item_news->image : './application/modules/Ultimatenews/externals/images/news.png';
        $photoUrl = $item_news->photo_id != "" ?  $item_news->getPhotoUrl() : $photoUrl;

        $item = array(
          'title' => $item_news->title,
          'descriptions' => array(),
          'href' => $item_news->getHref(),
          'photo' => $photoUrl,
          'creation_date' => $item_news->pubDate_parse
        );
        if($isTablet)
          $item['descriptions'][] = $item_news->description ? $this->view->feedDescription($item_news->description, 100) : $this->view->feedDescription($item_news->content, 100);
        $items[] = $item;
      }
      $component['items'] = $items;
      $title = '<br><img src="' . $cateItem->logo .'"> <a href="' . $this->view->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $cateItem->category_id),'ultimatenews_feed') . '"><b>' . $cateItem->category_name . '</b></a>';
      if( $subscribe ) {
        $title .= ' <a style="float:right;" class="btn btn-primary" href="' . $this->view->url(array('action'=>$cateItem->isSubscribe(),'feed' => $cateItem->getIdentity()), 'ultimatenews_extended') . '">' . $this->view->translate(ucfirst($cateItem->isSubscribe())) . '</a><br><br>';
      }
      $this->add($this->component()->html($title));
      $this->add($this->component()->customComponent('itemList', $component));
    }

    $this->add($this->component()->paginator($paginator));

      $this->renderContent();
  }

  public function addNavigation()
  {
//    $currentUser = Engine_Api::_()->user()->getViewer();
//    $username = $currentUser -> username;
//    $userid = $currentUser -> user_id;
//    $users = Engine_Api::_()->ultimatenews()->getAllUsers();
//    $flag = false;
//    foreach ($users as $user)
//    {
//      if ( $user['username'] && (!$user['userid']) ) {
//        if ($user['username'] == $username)
//          $flag = true;
//      }
//      else {
//        if ($user['userid'] == $userid)
//          $flag = true;
//      }
//    }
//    if (Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2)
//    {
//      $flag = true;
//    }

    if(true) {
      $navigation = $this->getNewsNavigation();
      $this->add($this->component()->navigation($navigation));
    }
  }

  public function indexDetailAction()
  {
    $content_id = $this->_getParam('id');
    //inactive jobs If I click to a inactive jobs link on activity feed
    $table = Engine_Api::_() -> getDbtable('contents', 'ultimatenews');
    $select = $table->select('engine4_ultimatenews_contents')
      ->setIntegrityCheck(false)
      ->joinLeft("engine4_ultimatenews_categories", "engine4_ultimatenews_categories.category_id= engine4_ultimatenews_contents.category_id")
      ->where('engine4_ultimatenews_contents.content_id= ? ', $content_id)
      ->where('engine4_ultimatenews_categories.is_active= ? ', 1)
      ->limit(1);

    $item = $table->fetchRow($select);

    if( is_object($item) ) {
      $this->setPageTitle($this->view->translate('Ultimate News'));

      if (!$item->image) {
        preg_match('/img\s+src="([^"]*)"/i', $item->content, $matches );
        if($matches[1]){
          list($width, $height, $type, $attr) = getimagesize($matches[1]);
          if ( ($width > 48) && ($height > 48) )
          {
            try{
              $storage_file = $this->saveImg($matches[1], md5($matches[1]));
            }
            catch (Exception $e) {
              echo $e;exit;
            }
            $item->image = $storage_file->storage_path;
            $item->photo_id = $storage_file->file_id;
            $item->save();
          }
        }
      }

      if(!Engine_Api::_() -> core() -> hasSubject()){
        Engine_Api::_() ->core() ->setSubject($item);
      }

      $item -> count_view = $item -> count_view + 1;
      $item -> save();

      $is_commment = $this -> _getParam('commentdetail');
      $category = Engine_Api::_()->ultimatenews()->getAllCategories(array(
        'category_id' => $item->category_id,
      ));
      $category = $category[0];

      $this->addNavigation();

      $this->add($this->component()->date(array('title' => $this->view->translate('Posted date') . ' ' . $item->pubDate_parse, 'count' => null)));

      if( $category->link_detail )
        $title =  '<a href="'. $item->link_detail . '"><h3 style="display:inline-block;">' . $item->title .'</h3></a>';
      else
        $title = '<h3 style="display:inline-block;">'.$item->title.'</h3>';
      if($category['logo'] != "" && $category['mini_logo'])
        $title = '<img src="' . $category['logo'] . '"> '.$title;
      $this->add($this->component()->html($title));

      if($category['category_parent_id'] > 0) {
        $cat = Engine_Api::_()->getItem('categoryparents',$category['category_parent_id']);
        $catLink = '<a href="'. $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent') . '"> ' . $this->view->translate($cat->category_name) . ' &#187</a> ';
      } else {
        $catLink = '<a href="' . $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=> 0),'ultimatenews_categoryparent') .'"> ' . $this->view->translate('Other') .' &#187</a> ';
      }
      $catLink .= '<a href="' . $this->view->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $category['category_id']),'ultimatenews_feed') . '">' . $category['category_name'] . ' &#187</a> ';
      if( $item->link_detail ) {
        $catLink .= '<a href="' . $item->link_detail . '">' . $item->title . '</a>';
      } else {
        $catLink .= $item->title;
      }
      $this->add($this->component()->html('<br>'.$catLink));

      if( $item->content != '' ) {
        $body = '<div class="news_body">' . $item->content . '</div>';
      } else {
        $body = '<div class="news_body">' . $item->description . '</div>';
      }

      if ($item->link_detail) {
        $body .= '<br><a href="' . $item->link_detail . '" target="_blank">' . $this->view->translate('Original Link') . '...</a>';
      }
      $this->add($this->component()->html($body));

      $viewer = Engine_Api::_()->user()->getViewer();
      $controlGroup = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: center'));
      if( $viewer && $viewer->getIdentity() ) {
        $controlGroup->append($this->dom()->new_('a',
          array(
            'data-role' => 'button',
            'data-icon' => 'chat',
            'data-rel' => 'dialog',
            'href' => $this->view->url(array(
                'module'=>'activity',
                'controller'=>'index',
                'action'=>'share',
                'type'=>$item->getType(),
                'id' => $item->getIdentity()), 'default', true)), $this->view->translate('Share')))

          ->append($this->dom()->new_('a',
            array(
              'data-role' => 'button',
              'data-icon' => 'flag',
              'data-rel' => 'dialog',
              'href' => $this->view->url(array(
                  'module'=>'core',
                  'controller'=>'report',
                  'action'=>'create',
                  'subject'=>$item->getGuid(),
                  'id' => $item->getIdentity()), 'default', true)), $this->view->translate('Report')));

        $this->add($this->component()->html($controlGroup . '<br />'));
      }

      $this->add($this->component()->comments());

      $this->renderContent();
    }
  }

  public function indexFeedAction()
  {
    $this->setPageTitle($this->view->translate('Ultimate News'));

    $category_id = $this->_getParam('category');
    if($category_id > 0)
      $category = Engine_Api::_()->getItem('categories',$category_id);

    $page =$this->_getParam('page', 1);
    if (isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
    {
      $page = $_POST['nextpage'];
    }
    $paginator = Engine_Api::_()->ultimatenews()->getContentsPaginator(array(
      'category_id' => $category_id, 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true,
    ));
    $paginator->setCurrentPageNumber($page);

    $flag = false;
    if (Engine_Api::_()->user()->getViewer()->getIdentity() > 0) {
      $username  = Engine_Api::_()->user()->getViewer()->username;
      $users = Engine_Api::_()->ultimatenews()->getAllUsers();
      $flag = false;
      foreach ($users as $user) {
        if ($user['username'] == $username) {
          $flag = true;
        }
      }
      if ( Engine_Api::_()->user()->getViewer()->level_id == 1 || Engine_Api::_()->user()->getViewer()->level_id == 2) {
        $flag = true;
      }
    }

    //Rendering
    if( $flag ) {
      // todo Render Navigation
    }

    $this->addNavigation();

    if( $category ) {
      $title = '<span>'.$category->category_name .' &#187; </span>';
      if( $category->logo != "" && $category->mini_logo ) {
        $title = '<img style="height: 23px; float: left; margin-right: 5px;" src="' . $category->logo . '"> '.$title;
      }
      $title = '<h3>' . $title . '</h3>';
      $this->add($this->component()->html($title));
    }

    $items = array();
    $isTablet = Engine_Api::_()->apptouch()->isTablet();

    foreach( $paginator as $item ) {
      $photoUrl = $item->image != "" ? $item->image : './application/modules/Ultimatenews/externals/images/news.png';
      $photoUrl = $item->photo_id != "" ?  $item->getPhotoUrl() : $photoUrl;
      $temp_item = array(
        'title' => $item->title,
        'descriptions' => array(),
        'href' => $item->getHref(),
        'photo' => $photoUrl,
        'creation_date' => $item->pubDate_parse
      );

      if($isTablet)
        $temp_item['descriptions'][] = $item->description ? $this->view->feedDescription($item->description, 100) : $this->view->feedDescription($item->content, 100);
      $items[] = $temp_item;
    }
    $paginatorPages = $paginator->getPages();
    $this->add($this->component()->customComponent('itemList', array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    )));

//    $this->add($this->component()->paginator($paginator));

    $this->renderContent();
  }

  public function indexContentsAction()
  {
    $categoryparent_id = $this->_getParam('categoryparent');
    $this->view->category_id = $categoryparent_id;
    if($categoryparent_id > 0)
      $categoryparent = Engine_Api::_()->getItem('categoryparents',$categoryparent_id);
    $page = $this->_getParam('page', 1);

    $paginator = Engine_Api::_()->ultimatenews()->getContentsPaginator(array(
      'categoryparent' => $categoryparent_id, 'order' => 'pubDate DESC', 'is_active' => 1, 'getcommment' => true,
    ));
    $paginator->setCurrentPageNumber($page);
    $select = Engine_Api::_()->ultimatenews()->getCategoryparentsSelect(array('category_active'=> 1));
    $table = Engine_Api::_()->getItemTable('categoryparents');
    $categories = $categories = $table->fetchAll($select);

    $this->addNavigation();

    $cats = '';
    foreach( $categories as $cat ) {
      $cats .= '<a href="' .
        $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent') .
        '" class="btn">'.
        (strlen($this->view->translate($cat->category_name))>22?substr($this->view->translate($cat->category_name),0,19).'...':$this->view->translate($cat->category_name)).
        '</a> ';
    }
    $cats .= '<a href="' .
      $this->view->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>0 ),'ultimatenews_categoryparent') .
      '" class="btn">'.
      $this->view->translate("Other").
      '</a> ';
    $catElements = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-collapsed' => false, 'data-content-theme' => 'b', 'data-theme' => 'b'), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('Categories')),
      $this->dom()->new_('p', array(), $cats)
    ));

    $this->add($this->component()->html($catElements));

    if( $categoryparent ) {
      $title = '<h3>'.$categoryparent->category_name . ' &#187 </h3>';
    } else {
      $title = '<h3>' . $this->view->translate("Other") . ' &#187 </h3>';
    }
    $this->add($this->component()->html($title));

    $items = array();
    $isTablet = Engine_Api::_()->apptouch()->isTablet();

    foreach( $paginator as $item ) {
      $photoUrl = $item->image != "" ? $item->image : './application/modules/Ultimatenews/externals/images/news.png';
      $photoUrl = $item->photo_id != "" ?  $item->getPhotoUrl() : $photoUrl;
      $temp_item = array(
        'title' => $item->title,
        'descriptions' => array(),
        'href' => $item->getHref(),
        'photo' => $photoUrl,
        'creation_date' => $item->pubDate_parse
      );

      if($isTablet)
        $temp_item['descriptions'][] = $item->description ? $this->view->feedDescription($item->description, 100) : $this->view->feedDescription($item->content, 100);
      $items[] = $temp_item;
    }
    $paginatorPages = $paginator->getPages();
    $this->add($this->component()->customComponent('itemList', array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',

      'items' => $items
    )));

//    $this->add($this->component()->paginator($paginator));

    $this->renderContent();
  }

  public function indexSubscribeAction() {
    $feed = $this -> _getParam('feed', 0);
    $viewer = Engine_Api::_() -> user() -> getViewer();

    if (empty($feed)) {
      return $this->redirect('refresh');
    }
    $category = Engine_Api::_() -> getItem('categories', $feed);
    $users = Zend_Json::decode($category -> subscribe);
    $users[$viewer -> getIdentity()] = $viewer -> getIdentity();
    $category -> subscribe = Zend_Json::encode($users);
    $category -> save();

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate') -> _('You have just subscribed this RSS feed successfully.'));
  }

  public function indexUnsubscribeAction() {
    $feed = $this -> _getParam('feed', 0);
    $viewer = Engine_Api::_() -> user() -> getViewer();

    if (empty($feed)) {
      return $this->redirect('refresh');
    }
    $category = Engine_Api::_() -> getItem('categories', $feed);
    $users = Zend_Json::decode($category -> subscribe);
    unset($users[$viewer -> getIdentity()]);
    $category -> subscribe = Zend_Json::encode($users);
    $category -> save();

    return $this->redirect('refresh', Zend_Registry::get('Zend_Translate') -> _('You have just unsubscribed this RSS feed successfully.'));
  }

  protected $_navigation;
  public function getNewsNavigation()
  {
    $tabs = array();
    $tabs[] = array('label' => 'Browse News',
      'route' => 'ultimatenews_general',
      'action' => 'index',
      'controller' => 'index',
      'module' => 'ultimatenews'
    );
//    $tabs[] = array(
//      'label' => 'News Management',
//      'route' => 'ultimatenews_general',
//      'action' => 'manage',
//      'controller' => 'index',
//      'module' => 'ultimatenews'
//    );
//    $tabs[] = array(
//      'label' => 'Add News',
//      'route' => 'ultimatenews_general',
//      'action' => 'create-news',
//      'controller' => 'index',
//      'module' => 'ultimatenews'
//    );
    if (is_null($this->_navigation))
    {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages($tabs);
    }
    return $this->_navigation;
  }

  public function saveImg($url, $name)
  {
    $adminArr = Engine_Api::_()->user()->getSuperAdmins()->toArray();
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'category_logo',
      'parent_id' => $adminArr[0]['user_id'],
      'user_id' => $adminArr[0]['user_id']
    );
    $gis = getimagesize($url);
    $type = $gis[2];
    switch($type) {
      case "1": $imorig = imagecreatefromgif($url); break;
      case "2": $imorig = imagecreatefromjpeg($url);break;
      case "3": $imorig = imagecreatefrompng($url); break;
      default: $imorig = imagecreatefromjpeg($url);
    }

    // Save
    $storage = Engine_Api::_()->storage();
    $filename = $path . DIRECTORY_SEPARATOR . $name . '.png';

    $im = imagecreatetruecolor(150,112);
    $x = imagesx($imorig);
    $y = imagesy($imorig);
    if (imagecopyresampled($im,$imorig , 0,0,0,0,150,112,$x,$y))
    {
      imagejpeg($im, $filename);
    }
    $iMain = $storage->create($path . '/' . $name . '.png', $params);
    return $iMain;
    // die();
  }
}
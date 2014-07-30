<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
    
class Mobile_Api_Core extends Core_Api_Abstract
{
  public function isMobile()
  {
		if (array_key_exists('HTTP_USER_AGENT', $_SERVER)){
    	$useragent=$_SERVER['HTTP_USER_AGENT'];
    	return (bool)preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
		}
		return false;
  }

  public function siteMode()
  {
		$session = new Zend_Session_Namespace('standard-mobile-mode');

    if ($session->__isset('mode'))
    {
			$mode	= $session->__get('mode');
			if ($mode === 'mobile')
				return 'mobile';

			elseif($mode === 'standard')
				return 'standard';
    }

		if ($this->isMobile())
		{
			return 'mobile';
		}

    return 'standard';
  }

	public function isMobileMode()
  {
		return (bool)($this->siteMode() === 'mobile');
	}

  public function setLayout()
  {
    // Create layout
    $layout = Zend_Layout::startMvc();

    // Set options
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobile/layouts", 'Core_Layout_View')
      ->setViewSuffix('tpl')
      ->setLayout(null);
		
    // Add themes
    $themeTable = Engine_Api::_()->getDbtable('themes', 'mobile');
    $themeSelect = $themeTable->select()->where('active = ?', 1);
    $themes = array();
    $themesInfo = array();
    foreach( $themeTable->fetchAll($themeSelect) as $row ) {
      $themes[] = $row->name;
      $themesInfo[$row->name] = include APPLICATION_PATH_COR . DS . 'modules' . DS . 'Mobile' . DS . 'themes' . DS . $row->name . DS . 'manifest.php';
    }
    $layout->themes = $themes;
    $layout->themesInfo = $themesInfo;
    Zend_Registry::set('Themes', $themesInfo);

    // Add global site title etc
    $siteinfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site', array());
    $siteinfo = array_filter($siteinfo);
    $siteinfo = array_merge(array(
      'title' => 'Social Network',
      'description' => '',
      'keywords' => '',
    ), $siteinfo);
    $layout->siteinfo = $siteinfo;

    return $layout;
  }

	public function redirectController($module)
	{
		$frontController = Zend_Controller_Front::getInstance();
		$moduleDir = $this->getPath($module);

		if ( is_dir($moduleDir) ) {
			$moduleDir .= DIRECTORY_SEPARATOR . $frontController->getModuleControllerDirectoryName();
			$frontController->addControllerDirectory($moduleDir, $module);
      return true;
		} else {
			return false;
		}
	}

	public function getPath($module, $params = array())
	{
		$moduleInflected = Engine_Api::inflect($module);
		
		$path = APPLICATION_PATH
			. DIRECTORY_SEPARATOR
			. "application"
			. DIRECTORY_SEPARATOR
			. "modules"
			. DIRECTORY_SEPARATOR
			. 'Mobile'
			. DIRECTORY_SEPARATOR
			. 'modules'
			. DIRECTORY_SEPARATOR
			. $moduleInflected;

		foreach ($params as $dir)
		{
			$path .= DIRECTORY_SEPARATOR . $dir;
		}

		return $path;
	}

	public function getScriptPath($module)
	{
		$path = $this->getPath($module);

		return $path . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts';
	}

	public function getActivity(User_Model_User $user, array $params = array())
  {
		$table = Engine_Api::_()->getDbtable('actions', 'activity');
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id, order;

    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $mainActionTypes = array();

    // Filter out types set as not displayable
    foreach( $masterActionTypes as $type ) {
      if( $type->displayable & 4 ) {
        $mainActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if( isset($showTypes) && is_array($showTypes) && !empty($showTypes) ) {
      $mainActionTypes = array_intersect($mainActionTypes, $showTypes);
    } else if( isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes) ) {
      $mainActionTypes = array_diff($mainActionTypes, $hideTypes);
    }

    // Nothing to show
    if( empty($mainActionTypes) ) {
      return null;
    }
    // Show everything
    else if( count($mainActionTypes) == count($masterActionTypes) ) {
      $mainActionTypes = true;
    }
    // Build where clause
    else {
      $mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', $user);
    $responses = (array) $event->getResponses();

    if( empty($responses) ) {
      return null;
    }

    foreach( $responses as $response )
    {
      if( empty($response) ) continue;

      $select = $streamTable->select()
        ->from($streamTable->info('name'), 'action_id')
        ->where('target_type = ?', $response['type'])
        ;

      if( empty($response['data']) ) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if( is_scalar($response['data']) || count($response['data']) === 1 ) {
        // Single
        if( is_array($response['data']) ) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if( is_array($response['data']) ) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if( null !== $action_id ) {
        $select->where('action_id = ?', $action_id);
      } else {
        if( null !== $min_id ) {
          $select->where('action_id >= ?', $min_id);
        } else if( null !== $max_id ) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      if( $mainActionTypes !== true ) {
        $select->where('type IN(' . $mainActionTypes . ')');
      }

      // Add order/limit
			if (null !== $order)
			{
				$select
					->order('action_id ' . $order);
			} else {
				$select
					->order('action_id DESC');
			}
			$select
					->limit($limit);

      // Add to main query
      $union->union(array('('.$select->__toString().')')); // (string) not work before PHP 5.2.0
    }

    // Finish main query
		if (null !== $order)
		{
			$union
				->order('action_id ' . $order);
		} else {
			$union
				->order('action_id DESC');
		}

		$union
				->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // No visible actions
    if( empty($actions) )
    {
      return null;
    }

    // Process ids
    $ids = array();
    foreach( $actions as $data )
    {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $table->fetchAll(
      $table->select()
        ->where('action_id IN('.join(',', $ids).')')
        ->order('action_id DESC')
        ->limit($limit)
    );
  }

  public function getActivityAbout(Core_Model_Item_Abstract $about, User_Model_User $user,
          array $params = array())
  {
		$table = Engine_Api::_()->getDbtable('actions', 'activity');
    // Proc args
    extract($this->_getInfo($params)); // action_id, limit, min_id, max_id, order

    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $masterActionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getActionTypes();
    $subjectActionTypes = array();
    $objectActionTypes = array();

    // Filter types based on displayable
    foreach( $masterActionTypes as $type ) {
      if( $type->displayable & 1 ) {
        $subjectActionTypes[] = $type->type;
      }
      if( $type->displayable & 2 ) {
        $objectActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if( isset($showTypes) && is_array($showTypes) && !empty($showTypes) ) {
      $subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
      $objectActionTypes = array_intersect($objectActionTypes, $showTypes);
    } else if( isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes) ) {
      $subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
      $objectActionTypes = array_diff($objectActionTypes, $hideTypes);
    }

    // Nothing to show
    if( empty($subjectActionTypes) && empty($objectActionTypes) ) {
      return null;
    }

    if( empty($subjectActionTypes) ) {
      $subjectActionTypes = null;
    } else if( count($subjectActionTypes) == count($masterActionTypes) ) {
      $subjectActionTypes = true;
    } else {
      $subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
    }

    if( empty($objectActionTypes) ) {
      $objectActionTypes = null;
    } else if( count($objectActionTypes) == count($masterActionTypes) ) {
      $objectActionTypes = true;
    } else {
      $objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', $user);
    $responses = (array) $event->getResponses();

    if( empty($responses) ) {
      return null;
    }

    foreach( $responses as $response )
    {
      if( empty($response) ) continue;

      // Target info
      $select = $streamTable->select()
        ->from($streamTable->info('name'), 'action_id')
        ->where('target_type = ?', $response['type'])
        ;

      if( empty($response['data']) ) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if( is_scalar($response['data']) || count($response['data']) === 1 ) {
        // Single
        if( is_array($response['data']) ) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if( is_array($response['data']) ) {
        // Array
        $select->where('target_id IN(?)', (array) $response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if( null !== $action_id ) {
        $select->where('action_id = ?', $action_id);
      } else {
        if( null !== $min_id ) {
          $select->where('action_id >= ?', $min_id);
        } else if( null !== $max_id ) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      // Add order/limit
			if (null !== $order)
			{
				$select
					->order('action_id ' . $order);
			} else {
				$select
					->order('action_id DESC');
			}
			$select
					->limit($limit);

      // Add subject to main query
      $selectSubject = clone $select;
      if( $subjectActionTypes !== null ) {
        if( $subjectActionTypes !== true ) {
          $selectSubject->where('type IN('.$subjectActionTypes.')');
        }
        $selectSubject
          ->where('subject_type = ?', $about->getType())
          ->where('subject_id = ?', $about->getIdentity());
        $union->union(array('('.$selectSubject->__toString().')')); // (string) not work before PHP 5.2.0
      }

      // Add object to main query
      $selectObject = clone $select;
      if( $objectActionTypes !== null ) {
        if( $objectActionTypes !== true ) {
          $selectObject->where('type IN('.$objectActionTypes.')');
        }
        $selectObject
          ->where('object_type = ?', $about->getType())
          ->where('object_id = ?', $about->getIdentity());
        $union->union(array('('.$selectObject->__toString().')')); // (string) not work before PHP 5.2.0
      }
    }

    // Finish main query
    $union
      ->order('action_id DESC')
      ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // No visible actions
    if( empty($actions) )
    {
      return null;
    }

    // Process ids
    $ids = array();
    foreach( $actions as $data )
    {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $table->fetchAll(
      $table->select()
        ->where('action_id IN('.join(',', $ids).')')
        ->order('action_id DESC')
        ->limit($limit)
    );
  }
  // Utility

  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
			'order' => null,
    );

    $newParams = array();
    foreach( $args as $arg => $default ) {
      if( !empty($params[$arg]) ) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }




  /*
   * APIs Page event
   *  */


  public function getEventMembers($params = array())
  {

    $event_id = (int)$params['event_id'];
    $rsvp = (int)$params['rsvp'];

    $event = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->findRow($event_id);
    if ($event){
      $only_friends = ($params['list_type'] == 'mutual');
      return $event->membership()->getMemberPaginator($rsvp, $only_friends);
    }

    return null;

  }

  public function checkPageWidget($page_id, $name)
  {
    $api = Engine_Api::_()->getDbTable('modules', 'core');

    if (!$api->isModuleEnabled('page')){
      return false;
    }

    $widget_list = array(
      'mobile.page-feed' => 'page.feed',
      'mobile.page-profile-photo' => 'page.profile-photo',
      'mobile.page-profile-note' => 'page.profile-note',
      'mobile.page-profile-admins' => 'page.profile-admins',
      'mobile.page-profile-options' => 'page.profile-options',
      'mobile.page-profile-fields' => 'page.profile-fields',
      'mobile.page-profile-album' => 'pagealbum.profile-album',
      'mobile.page-profile-blog' => 'pageblog.profile-blog',
      'mobile.page-profile-discussion' => 'pagediscussion.profile-discussion',
      'mobile.page-profile-event' => 'pageevent.profile-event',
      'mobile.page-review' => 'rate.page-review',
      'mobile.rate-widget' => 'rate.widget-rate',
      'mobile.page-profile-status' => 'page.profile-status',
    );

    if (!array_key_exists($name, $widget_list)){
      return false;
    }

    $external_name = $widget_list[$name];

    $parts = explode('.', $external_name);

    if (!$api->isModuleEnabled($parts[0])){
      return false;
    }

    $tbl = Engine_Api::_()->getDbTable('content', 'page');

    if ($external_name == 'page.profile-status'){

      $select = $tbl->select()
          ->where('page_id = ?', $page_id)
          ->where('name IN (?)', array('page.profile-status', 'like.status'));

    }  else {

      $select = $tbl->select()
          ->where('page_id = ?', $page_id)
          ->where('name = ?', $external_name);

    }

    $widget = $tbl->fetchRow($select);

    if (!$widget){
      return false;
    }

    return true;

  }












}
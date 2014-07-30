<?
require_once('staff.inc.php');

/* Ajax for STAFF needs */
$command = $_POST['command'] ? $_POST['command'] : $_GET['command'];
$return = null;
$is_json = true;



if( $command=='start_dev_log' )
{
  $lod_id = dev_hours::start_log($thisuser->getId());
	$return['status'] = $lod_id ? true : false;
	
	dev_hours::track_points($thisuser->getId());
}


elseif( $command=='stop_dev_log' )
{
	$return['status'] = dev_hours::stop_log($thisuser->getId()) ? true : false;
}


elseif( $command=='is_all_logs_stoped' )
{
	$return['status'] = dev_hours::is_all_logs_stoped($thisuser->getId()) ? true : false;
}


elseif( $command=='change_dev_log' )
{
	if( !$thisuser->isadmin() || !($log = dev_hours::get_log($_REQUEST['log_id'])) )
	{
		$return['status'] = false;
	}
	else
	{
		if( $_REQUEST['start_time'] )
		{
			$hours = Format::userdate("H", Misc::db2gmtime((int)$log['start_time']));
			$minutes = Format::userdate("i", Misc::db2gmtime((int)$log['start_time']));
			
			$time = explode(':', $_REQUEST['start_time']);
			$start_time = $log['start_time'] - (($hours - $time[0])*3600 + ($minutes-$time[1])*60);
			$end_time = $log['end_time'];
		}
		else 
		{
			$hours = Format::userdate("H", Misc::db2gmtime((int)$log['end_time']));
			$minutes = Format::userdate("i", Misc::db2gmtime((int)$log['end_time']));
			
			$time = explode(':', $_REQUEST['end_time']);
			$end_time = $log['end_time'] - (($hours - $time[0])*3600 + ($minutes-$time[1])*60);
			$start_time = $log['start_time'];
		}
		$period = $end_time - $start_time;
		$return['period'] = Format::sec2hms($period);
		$return['status'] = dev_hours::change_dev_log($_REQUEST['log_id'], $start_time, $end_time, $period) ? true : false;
	}
}


elseif ( $command=='get_client' )
{
	$name = mysql_real_escape_string($_REQUEST['input']);
  if( $_REQUEST['only_active'] )
    $sql = "SELECT full_name, email, id FROM ost_account WHERE !`disabled` AND `email_confirmed` AND `full_name` LIKE '%$name%' LIMIT 12";
  else
    $sql = "SELECT full_name, email, id FROM ost_account WHERE full_name LIKE '%$name%' LIMIT 12";

	$clients = db_get_rows($sql);
	$return['results'] = array();
	foreach ($clients as $client)
	{
		$return['results'][] = array(
			'id' => $client['id'],
			'info' => $client['email'],
			'value' => $client['full_name'],
		);
	}
}


elseif ($command == 'set_part_time')
{
    $full_day = isset($_REQUEST['full_day']) ? (int)$_REQUEST['full_day'] : 0;
    $day = isset($_REQUEST['day']) ? (int)$_REQUEST['day'] : 0;
    $month = isset($_REQUEST['month']) ? (int)$_REQUEST['month'] : 0;
    $year = isset($_REQUEST['year']) ? (int)$_REQUEST['year'] : 0;
    $user_id = isset($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : 0;
    
    if (!$day) {
        $day = date('j', mktime(0, 0) + 24*3600);
    }
    if (!$month) {
        $month = date('n', mktime(0, 0) + 24*3600);
    }
    if (!$year) {
        $year = date('Y', mktime(0, 0) + 24*3600);
    }
    
    $user_id = ($thisuser->isadmin()) ? $user_id : $thisuser->getId();
    
    if ($full_day) {
        dev_hours::set_full_day($user_id, $day, $month, $year);
    } else {
        dev_hours::set_part_day($user_id, $day, $month, $year);
    }
    
    $return = $day;
}


elseif ($command == 'get_client_tickets')
{
	$name = mysql_real_escape_string($_REQUEST['input']);
	$clients = db_get_rows("SELECT full_name, email, id FROM ost_account WHERE full_name LIKE '%$name%' LIMIT 10");
	$return['results'] = array();
	foreach ($clients as $client)
	{
		$return['results'][] = array(
			'id' => $client['email'],
			'info' => $client['email'],
			'value' => $client['full_name'],
		);
	}
}

elseif ($command == 'verify_email')
{
	$return['status'] = ( account::confirm_email_manually($_REQUEST['account_id']) );
}

elseif ($command == 'unverify_email')
{
	$return['status'] = ( account::unconfirm_email_manually($_REQUEST['account_id']) );
}

elseif ($command == 'change_dev_point')
{
    $staff_id = (int)$_REQUEST['user_id'];
    $day = (int)$_REQUEST['day'];
    $month = (int)$_REQUEST['month'];
    $year = (int)$_REQUEST['year'];
    $point_id = (int)$_REQUEST['point_id'];
    $point = (int)$_REQUEST['point'];
    
	$return['point_id'] = $thisuser->isadmin()
	  ? dev_hours::change_dev_point($staff_id, $point_id, $day, $month, $year, $point) 
	  : false;
}

elseif ($command == 'dp_get_updates')
{
  if (!$thisuser || !$thisuser->getId()) {
    exit();
  }

  $hash = trim($_REQUEST['hash']);
  $page = (int)$_REQUEST['page'];
  $list_type = trim($_REQUEST['list_type']);

  $ticket_list = $thisuser->getUserTicketList($page, $list_type);
  setcookie('dp_' . $list_type . $page, $ticket_list['hash'], time()+120);

  $return = ($ticket_list['hash'] != $hash) ? $ticket_list : false;
}

elseif ($command == 'crd_save_changes')
{
  if (!$thisuser || !$thisuser->getId()) {
    exit();
  }

  $account_id = (int)$_REQUEST['account_id'];
  $type = trim($_REQUEST['type']);
  $value = trim($_REQUEST['value']);

  $return = account::update_crd_info($account_id, $type, $value);
}

elseif ($command == 'close_correspondence')
{
	$return['status'] = ( correspondence::close(intval($_REQUEST['correspondence_id'])) );
}

elseif ($command == 'open_correspondence')
{
	$return['status'] = ( correspondence::open(intval($_REQUEST['correspondence_id'])) );
}

elseif ($command == 'delete_correspondence')
{
  correspondence::delete(intval($_REQUEST['correspondence_id']));
	$return['status'] = true;
}



elseif ($command == 'cp_enable')
{
  client_product::enable($_REQUEST['cp_id']);
}

elseif ($command == 'cp_disable')
{
  client_product::disable($_REQUEST['cp_id']);
}



elseif( $command == 'disable_account' && $thisuser->isadmin() )
{
  account::update_account($_REQUEST['email'], array('disabled' => 1));
}

elseif( $command == 'enable_account' && $thisuser->isadmin() )
{
  account::update_account($_REQUEST['email'], array('disabled' => 0));
}



elseif( $command == 'enable_chat' )
{
  account::update_account($_REQUEST['email'], array('chat_enabled' => 1));
}

elseif( $command == 'disable_chat' )
{
  account::update_account($_REQUEST['email'], array('chat_enabled' => 0));
}



elseif( $command == 'enable_risky' )
{
  account::update_account($_REQUEST['email'], array('risky' => 1));
}

elseif( $command == 'disable_risky' )
{
  account::update_account($_REQUEST['email'], array('risky' => 0));
}



elseif( $command == 'subscribe' )
{
  account::update_account($_REQUEST['email'], array('unsubscribe' => 0));
}

elseif( $command == 'unsubscribe' )
{
  account::update_account($_REQUEST['email'], array('unsubscribe' => 1));
}



elseif( $command == 'subscribe_correspondent' && $thisuser->isadmin() )
{
  $return['status'] = ( correspondence::subscribe_correspondent(null, $_REQUEST['correspondent_id']) );
}

elseif( $command == 'unsubscribe_correspondent' && $thisuser->isadmin() )
{
  $return['status'] = ( correspondence::unsubscribe_correspondent(null, $_REQUEST['correspondent_id']) );
}

elseif( $command == 'delete_correspondent' && $thisuser->isadmin() )
{
  $return['status'] = ( correspondence::delete_correspondent($_REQUEST['correspondent_id']) );
}



elseif( $command == 'client_reviewed' )
{
  if( isset($_REQUEST['is_up']) && $_REQUEST['is_up'] )
    reviews::save_client_review($_REQUEST['account_id'], $_REQUEST['rpage_key'], true);
  elseif( isset($_REQUEST['is_down']) && $_REQUEST['is_down'] )
    reviews::save_client_review($_REQUEST['account_id'], $_REQUEST['rpage_key'], false);
  else
    reviews::save_client_review($_REQUEST['account_id'], $_REQUEST['rpage_key'], null);
  $return['status'] = true;
}







header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0
header("Content-Type: application/json");

echo json_encode($return);

?>
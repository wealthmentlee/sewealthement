<?php
class Socialdna_RController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {

    $service = Engine_Api::_()->getApi('core', 'socialdna');
    $req = $this->getRequest();

    $campaign = (int)$req->get('c');
    $user_id = (int)$req->get('u');
    $service_id = (int)$req->get('s');
    $url = $req->get('r');
    
    
    $url = base64_decode(strtr($url, '-~,', '+/='));

    $visitor_ipaddress = ip2long(Semods_Utils::g($_SERVER,'REMOTE_ADDR'));
    
    // Update stats      
    $insert =  array( 'openidlinkstat_campaign_id'  => $campaign,
                      'openidlinkstat_user_id'      => $user_id,
                      'openidlinkstat_service_id'   => $service_id,
                      'openidlinkstat_link'         => htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                      'openidlinkstat_time'         => time(),
                      'openidlinkstat_type'         => 0,   // click
                      'openidlinkstat_ref'          => htmlspecialchars(Semods_Utils::g($_SERVER,'HTTP_REFERER',''), ENT_QUOTES, 'UTF-8'),
                      'openidlinkstat_ip'           => $visitor_ipaddress
                     );
      
    Engine_Api::_()->getDbTable('linkstats','socialdna')->insert($insert);
    
    if($service->getServiceId($service_id) != 0) {
      $service->update_stats('click', $service_id);
    }

    return $this->_redirect($url, array('prependBase' => false));

  }
}
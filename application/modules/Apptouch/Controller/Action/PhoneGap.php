<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 03.11.12
 * Time: 13:12
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_PhoneGap extends Apptouch_Controller_Action_Bridge
{
  public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()){
    parent::__construct($request, $response, $invokeArgs);
  }
}

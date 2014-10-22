<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright 2009-2012 Hire-Experts
 * @license    http://hire-experts.com/
 * @version    $Id: OfferMenus.php 2012-08-16 01:09:21Z Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright 2009-2012 Hire-Experts
 * @license    http://hire-experts.com/
 */

class Apptouch_Plugin_OfferMenus
{
  public function initMenu($row)
  {

    $subject = Engine_Api::_()->core()->getSubject();

    $params = $row->params;

    if ($subject instanceof Offers_Model_Offer) {

      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }

      $params['params']['offer_id'] = $subject->getIdentity();

      return $params;
    }
    return true;

  }

  public function __call($method, array $arguments = array())
  {
    return $this->initMenu($arguments[0]);
  }

}

<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Apptouch_Form_Heevent_Subscription_Choose extends Engine_Form
{
    private $event = null;
    private $ticketPrice = 0;
    private $event_id = 0;
    private $count_quantity = 0;
    private $gateways = 0;
    private $translate = NULL;


    public function __construct(Heevent_Model_Event $event = null, $count_quantity, $gateways) {
        if($event) {
            $this->event = $event;
            $this->event_id = $event->getIdentity();
            $this->ticketPrice = Engine_Api::_()->getDbTable('tickets', 'heevent')->getEventTickets($event)->ticket_price;
            $this->count_quantity = $count_quantity;
            $this->gateways = $gateways;
            $this->translate = Zend_Registry::get('Zend_Translate');

        }
        parent::__construct();
    }
  public function init()
  {
//      print_arr($this->translate);
    $this->setTitle($this->translate->_('EVENTS_Purchase Event').' - '.$this->event->getTitle());
      $this->addElement('Dummy', 'content1', array('content' => $this->translate->_('HEEVENT_count').' - '.$this->count_quantity));
      $this->addElement('Dummy', 'content2', array('content' => $this->translate->_('HEEVENT_total_price').' - '.$this->count_quantity));
      $this->addElement('Dummy', 'content3', array('content' => $this->translate->_('HEEVENTS_Purchase Event Gateways Description')));
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'heevent',
              'controller' => 'subscription',
              'action' => 'process'),
          'default'));
            foreach( $this->gateways as $gatewayInfo ):
          $gateway = $gatewayInfo['gateway'];
          $first = ( !isset($first) ? true : false );


          $this->addElement('Button', 'submit', array(
              'label' => $this->translate->_('Pay with '. $this->translate->_($gateway->getTitle())),
              'type' => 'submit',
              'ignore' => true,
              'onclick' => "$('#gateway_id').val(". $gateway->gateway_id.");",
              'class' => 'hecontest_widget_button',
              'decorators' => array(
                  'ViewHelper',
              ),
          ));
       endforeach;
      $this->addElement('hidden', 'gateway_id', array(
            'id' => 'gateway_id'
        ));
  }
}

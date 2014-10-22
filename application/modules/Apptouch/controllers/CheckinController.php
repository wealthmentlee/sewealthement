<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:38
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_CheckinController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexSuggestAction()
  {
    $keyword = $this->_getParam('keyword', $this->_getParam('value'));
    $latitude = $this->_getParam('latitude', 0);
    $longitude = $this->_getParam('longitude', 0);

    $pageResults = Engine_Api::_()->checkin()->getPageResults($keyword);
    $googleResults = Engine_Api::_()->checkin()->getGoogleResults($keyword, $latitude, $longitude);

    $suggest_list = array();
    $key = 1;
    foreach ($pageResults as $pageResult) {
      $pageResult['id'] = 'checkin_' . $key++;
      $pageResult['checkins'] = 0;
      $suggest_list[] = $pageResult;

      if ($key > 10) {
        break;
      }
    }

    foreach ($googleResults as $googleResult) {
      $googleResult['id'] = 'checkin_' . $key++;
      $googleResult['checkins'] = 0;
      $suggest_list[] = $googleResult;
    }

/*  //todo implement in the future
     if (count($suggest_list) == 0) {
      $suggest_list[] = array(
        'id' => 'checkin_1',
        'no_content' => true,
      );
    }*/


    header('Content-type: application/json');
    echo Zend_Json::encode($suggest_list);
    exit();
  }


  public function indexViewMapAction()
  {
    $place_id = $this->_getParam('place_id', 0);
    $noPhoto = 'application/modules/Checkin/externals/images/nophoto.png';
    if (!$place_id) {

      $this
        ->add($this->component()->html($this->view->translate('Undefined')))
        ->renderContent();
      return ;

    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');

    $this->view->checkin = $checkin = $placesTbl->findRow($place_id);

    if (!$checkin) {

      $this
        ->add($this->component()->html($this->view->translate('Undefined')))
        ->renderContent();
      return ;
    }

    if ($checkin->object_type == 'page' || $checkin->object_type == 'event') {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id);

      if (!$subject || !$viewer ||  !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) {

        $this
          ->add($this->component()->html($this->view->translate('Undefined')))
          ->renderContent();
        return ;
      }
    }

    $paginator = Zend_Paginator::factory($checksTbl->getPlaceVisitors($place_id, 9));


    $this->lang()->add('CHECKIN_There are no locations');

    $this
      ->add($this->component()->checkinmap(array('place_id' => $place_id)))
      ->setFormat('manage')
      ->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();


  }

  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'creation_date' => null
    );
    return $customize_fields;
  }




  public function indexGetEventLocationAction()
  {
    $event_id = $this->_getParam('event_id', 0);
    $keyword = $this->_getParam('keyword', '');

    if (!$event_id || !$keyword) {
      return;
    }

    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event || !$viewer || $viewer->getIdentity() != $event->user_id) {
      return;
    }

    $this->view->places = Engine_Api::_()->checkin()->getGoogleResults($keyword, 0, 0);
  }

  public function indexSetEventLocationAction()
  {
    $event_id = $this->_getParam('event_id', 0);
    $reference = $this->_getParam('reference', '');

    if (!$event_id || !$reference) {
      return;
    }

    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event || !$viewer || $viewer->getIdentity() != $event->user_id) {
      return;
    }

    $this->view->place = $place_info = Engine_Api::_()->checkin()->getGooglePlaceDetails($reference);

    if ($place_info && isset($place_info['name']) && $place_info['name']) {
      $event->location = $place_info['name'];
      $event->save();

      $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
      $event_place = $placesTbl->findByObject('event', $event_id);

      if (!$event_place) {
        $event_place = $placesTbl->createRow();
      }

      $event_place->setFromArray(array(
        'object_id' => $event_id,
        'object_type' => 'event',
        'google_id' => isset($place_info['google_id']) ? $place_info['google_id'] : '',
        'name' => isset($place_info['name']) ? $place_info['name'] : '',
        'types' => isset($place_info['types']) ? $place_info['types'] : '',
        'vicinity' => isset($place_info['vicinity']) ? $place_info['vicinity'] : '',
        'latitude' => $place_info['latitude'],
        'longitude' => $place_info['longitude'],
        'creation_date' => new Zend_Db_Expr('NOW()')
      ));

      $event_place->save();
    }
  }


}

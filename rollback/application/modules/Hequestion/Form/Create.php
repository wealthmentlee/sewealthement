<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Form_Create extends Engine_Form
{

  public function init()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = Engine_Api::_()->user()->getViewer();


    $this->addElement('Text', 'title', array(
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 255)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));


    $availableLabels = array(
      'everyone'            => 'HEQUESTION_Everyone',
      'owner_network'       => 'HEQUESTION_Friends and Networks',
      'owner_member'        => 'HEQUESTION_Friends Only',
      'owner'               => 'HEQUESTION_Just Me'
    );

    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      } else {
        $this->addElement('Select', 'auth_view', array(
          'multiOptions' => $viewOptions,
          'value' => key($viewOptions),
        ));
      }
    }


    $this->addElement('Checkbox', 'can_add', array(
      'checked' => true
    ));

  }


}

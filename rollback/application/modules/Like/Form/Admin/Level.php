<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
  	$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'admin_like_level');
  	
    $this
    	->setAction($url)
      ->setTitle('like_Like Settings')
      ->setDescription('like_FAN_FORM_ADMIN_LEVEL_DESCRIPTION');
      
		$levels = array();
    $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
    foreach ($table->fetchAll($table->select()) as $row){
      $levels[$row['level_id']] = $row['title'];
    }

    $this->addElement('Select', 'level_id', array(
      'label' => 'like_Member Level',
      'multiOptions' => $levels,
    	'onChange' => 'form_redirect_level("'.$url.'", this)'
    ));

		$api = Engine_Api::_()->getDbTable('modules', 'core');
		$hecoreApi = Engine_Api::_()->getDbTable('modules', 'hecore');

    $this->addElement('Radio', 'like_user', array(
      'label' => 'like_Like Profiles',
      'description' => 'LIKE_ALLOW_PROFILES',
      'multiOptions' => array(
        0 => 'like_No, do not allow profiles to be liked.',
        1 => 'like_Yes, allow profiles to be liked.'
      )
    ));

		if ($hecoreApi->isModuleEnabled('page')){
			$this->addElement('Radio', 'like_page', array(
				'label' => 'like_Like Pages',
				'description' => 'LIKE_ALLOW_PAGES',
				'multiOptions' => array(
					0 => 'like_No, do not allow pages to be liked.',
					1 => 'like_Yes, allow pages to be liked.'
				)
			));
		}

		if ($api->isModuleEnabled('event')){
			$this->addElement('Radio', 'like_event', array(
				'label' => 'like_Like Events',
				'description' => 'LIKE_ALLOW_EVENTS',
				'multiOptions' => array(
					0 => 'like_No, do not allow events to be liked.',
					1 => 'like_Yes, allow events to be liked.'
				)
			));
		}

		if ($api->isModuleEnabled('group')){
			$this->addElement('Radio', 'like_group', array(
				'label' => 'like_Like Groups',
				'description' => 'LIKE_ALLOW_GROUPS',
				'multiOptions' => array(
					0 => 'like_No, do not allow groups to be liked.',
					1 => 'like_Yes, allow groups to be liked.'
				)
			));
		}

    //Эта фича скрыто, возможно будет в следующих билдах
    /*if($api->isModuleEnabled('store')){
      $this->addElement('Radio', 'like_product', array(
        'label' => 'like_Promote Products',
        'description' => 'LIKE_ALLOW_PRODUCTS',
        'multiOptions' => array(
          0 => 'like_No, do not allow products to be promoted.',
          1 => 'like_Yes, allow products to be promoted.'
        )
      ));
    }*/

    if ($api->isModuleEnabled('donation')){
      $this->addElement('Radio', 'like_donation', array(
        'label' => 'like_Like Donations',
        'description' => 'LIKE_ALLOW_DONATIONS',
        'multiOptions' => array(
          0 => 'like_No, do not allow donations to be liked.',
          1 => 'like_Yes, allow donations to be liked.'
        )
      ));
    }

    if ($api->isModuleEnabled('offers')){
      $this->addElement('Radio', 'like_offer', array(
        'label' => 'like_Like Offers',
        'description' => 'LIKE_ALLOW_OFFERS',
        'multiOptions' => array(
          0 => 'like_No, do not allow offers to be liked.',
          1 => 'like_Yes, allow offers to be liked.'
        )
      ));
    }

		$this->addElement('Radio', 'interest', array(
			'label' => 'Allow Viewing Interests',
			'description' => 'LIKE_ALLOW_INTERESTS',
			'multiOptions' => array(
				0 => 'like_No, do not allow viewing interests.',
				1 => 'like_Yes, allow viewing interests.'
			)
		));

		$this->addElement('MultiCheckbox', 'auth_interest', array(
			'label' => 'Interests Privacy',
			'description' => 'LIKE_INTERESTS_PRIVACY_DESC',
			'multiOptions' => array(
				'everyone'            => 'Everyone',
				'registered'          => 'All Registered Members',
				'owner_network'       => 'Friends and Networks',
				'owner_member_member' => 'Friends of Friends',
				'owner_member'        => 'Friends Only',
				'owner'               => 'Just Me'
			),
			'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
		));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
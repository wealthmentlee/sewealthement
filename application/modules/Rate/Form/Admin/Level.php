<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2010-07-02 19:27 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Form_Admin_Level extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("Ratings level settings");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    // prepare user levels
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $select = $table->select();
    $user_levels = $table->fetchAll($select);

    foreach ($user_levels as $user_level) {
      if ($user_level->type == 'public') {
        continue;
      }
      
      $levels_prepared[$user_level->level_id]= $user_level->getTitle();
    }

    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels_prepared,
            'onchange' => 'javascript:fetchLevelSettings(this.value);',
      'ignore' => true
    ));
    
    $this->addElement('Radio', 'enabled', array(
      'label' => 'Allow Rate of content?',
      'description' => 'RATE_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow content to be rated.',
        1 => 'Yes, allow rate content.',
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'reviewcreate', array(
      'label' => 'RATE_REVIEW_LEVEL_CREATE_TITLE',
      'description' => 'RATE_REVIEW_LEVEL_CREATE_DESCRIPTION',
      'multiOptions' => array(
        0 => 'RATE_REVIEW_LEVEL_CREATE_NO',
        1 => 'RATE_REVIEW_LEVEL_CREATE_YES',
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'rateenabled', array(
      'label' => 'RATE_LEVEL_RATE_PROFILE_ENABLED_TITLE',
      'description' => 'RATE_LEVEL_RATE_PROFILE_ENABLED_DESCRIPTION',
      'multiOptions' => array(
        0 => 'RATE_LEVEL_RATE_PROFILE_ENABLED_NO',
        1 => 'RATE_LEVEL_RATE_PROFILE_ENABLED_YES',
      ),
      'value' => 1,
    ));
        
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
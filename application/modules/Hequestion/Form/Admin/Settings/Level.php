<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hequestion_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('HEQUESTION_FORM_ADMIN_LEVEL_DESCRIPTION');

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'HEQUESTION_Allow Viewing of Questions?',
      'description' => 'HEQUESTION_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        2 => 'HEQUESTION_Yes, allow viewing of all questions, even private ones.',
        1 => 'HEQUESTION_Yes, allow viewing of questions.',
        0 => 'HEQUESTION_No, do not allow questions to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'HEQUESTION_Allow Questions?',
        'description' => 'HEQUESTION_Do you want to allow members to create question?',
        'multiOptions' => array(
          1 => 'HEQUESTION_Yes, allow this member level to create question',
          0 => 'HEQUESTION_No, do not allow this member level to create question',
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'HEQUESTION_Allow Editing of Questions?',
        'description' => 'HEQUESTION_Do you want to let members edit questions? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'HEQUESTION_Yes, allow members to edit all questions.',
          1 => 'HEQUESTION_Yes, allow members to edit their own questions.',
          0 => 'HEQUESTION_No, do not allow members to edit their questions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'HEQUESTION_Allow Deletion of Questions?',
        'description' => 'HEQUESTION_FORM_ADMIN_LEVEL_DELETE_DESCRIPTION',
        'multiOptions' => array(
          2 => 'HEQUESTION_Yes, allow members to delete all questions.',
          1 => 'HEQUESTION_Yes, allow members to delete their own questions.',
          0 => 'HEQUESTION_No, do not allow members to delete their questions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'HEQUESTION_Allow Commenting on Questions?',
        'description' => 'HEQUESTION_Do you want to let members of this level comment on questions?',
        'multiOptions' => array(
          2 => 'HEQUESTION_Yes, allow members to comment on all questions, including private ones.',
          1 => 'HEQUESTION_Yes, allow members to comment on questions.',
          0 => 'HEQUESTION_No, do not allow members to comment on questions.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'HEQUESTION_Question Privacy',
        'description' => 'HEQUESTION_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'HEQUESTION_Everyone',
          'owner_network'       => 'HEQUESTION_Friends and Networks',
          'owner_member'        => 'HEQUESTION_Friends Only',
          'owner'               => 'HEQUESTION_Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));


/*      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Question Comment Options',
        'description' => 'HEQUESTION_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'owner_network'       => 'Friends and Networks',
          'owner_member_member' => 'Friends of Friends',
          'owner_member'        => 'Friends Only',
          'owner'               => 'Just Me'
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));*/

    }

  }
  
}
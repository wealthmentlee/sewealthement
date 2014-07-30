<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Photo.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Photo extends Engine_Form
{

  public function init()
  {

    $this->setTitle('WALL_UPLOAD_PHOTO_TITLE');
    $this->setDescription('WALL_UPLOAD_PHOTO_DESCRIPTION');

    $this
        ->setAttrib('enctype', 'multipart/form-data')
        ->setAttrib('name', 'EditPhoto');

    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
      'onchange'=>'$(this).getParent("form").addClass("wall_active").submit();',
    ));
  }


}

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 03.05.13
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */

class Pinfeed_Form_Admin_Settings_Global extends Engine_Form {
  public function init()
  {
    // My stuff
    $this
      ->setTitle("Wall Pin Feed setting")->setDescription('BLOCKS_PEN_SETTINGS_DESCRIPTION');

    $this->addElement('Select', 'usage', array(
      'label' => 'Replace standard Home Page to the Pin Feed',
      'description' => 'Replaces standard Home Page on a beautiful new Pin Feed',
      'multiOptions' => array(
        '1' => 'Yes, use Pin Feed Home page',
        '0' => 'No, use default Home page',
      ),
    ));
    $this->addElement('Select', 'width', array(
      'label' => 'Enable responsive width for pinfeed?',
      'description' => 'Place the large size of the pin feed to make it more beautiful',
      'multiOptions' => array(
        '1' => 'Yes, enable to fit the user`s browser size',
        '0' => 'No, keep pin feed`s width fixed',
      ),
    ));

    // Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
    ));

  }


}
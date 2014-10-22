<?php


class Apptouch_Form_Share extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Share')
      ->setDescription('Share this by re-posting it with your own message.')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    
    $this->addElement('Textarea', 'body', array(
      //'required' => true,
      //'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    // Buttons
    $buttons = array();





    /**
     * Sharing via Wall
     */

    $isWall = 0;
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')) {
      $isWall = 1;
    }
    $router = Zend_Controller_Front::getInstance()->getRouter();


    $serviceRequestUrl = '';
    $services = '';

    if ($isWall){


      $serviceRequestUrl = $router->assemble(array('module' => 'wall', 'controller' => 'index', 'action' => 'services-request', 'format' => 'json'), 'default', true);
      $services = array();

      $wall_services = Engine_Api::_()->wall()->getManifestType('wall_service', true);
      $viewer = Engine_Api::_()->user()->getViewer();
      $setting = Engine_Api::_()->wall()->getUserSetting($viewer);
      foreach ($wall_services as $service){
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class || !$class->isActiveStream()){
          continue ;
        }
        $services[$service] = array(
          'url' => $router->assemble(array('module' => 'wall', 'controller' => $service, 'action' => 'index'), 'default', true),
          'serviceShareUrl' => $router->assemble(array('module' => 'wall', 'controller' => 'index', 'action' => 'service-share', 'format' => 'json'), 'default', true),
          'enabled' => false,
        );

        $setting_key = 'share_' . $service . '_enabled';

        if (isset($setting->{$setting_key}) && $setting->{$setting_key} ){
          $services[$service]['enabled'] = true;
        }
        $services[$service]['enabled'] = (int)$services[$service]['enabled'];
      }

    }
    $content = '';
    if( $services ) {
      //$services = Zend_Json::encode($services);
      $fbactive = ($services['facebook']['enabled']) ? 'active' : 'disabled';
      $twactive = $services['twitter']['enabled'] ? 'active' : 'disabled';
      $ldnactive = $services['linkedin']['enabled'] ? 'active' : 'disabled';
      $content = <<<CONTENT
<ul class="wallShareMenu" data-services='$services' data-request-url=$serviceRequestUrl>
  <li class="service" style="display: none;">
    <a data-type="facebook" class="wall-share-facebook wall_tips {$fbactive}" href="javascript:void(0);" target="_blank"></a>
    <input type="hidden" value="{$services['facebook']['enabled']}" class="share_input" name="share[facebook]">
  </li>
  <li class="service" style="display: none;">
    <a data-type="twitter" class="wall-share-twitter wall_tips {$twactive}" href="javascript:void(0);" target="_blank"></a>
    <input type="hidden" value="{$services['twitter']['enabled']}" class="share_input" name="share[twitter]">
  </li>
  <li class="service" style="display: none;">
    <a data-type="linkedin" class="wall-share-linkedin wall_tips {$ldnactive}" href="javascript:void(0);" target="_blank"></a>
    <input type="hidden" value="{$services['linkedin']['enabled']}" class="share_input" name="share[linkedin]">
  </li>
</ul>

CONTENT;
    }

    $this->addElement('Dummy', 'share_social', array(
      'content' => $content,
    ));
    $this->getElement('share_social')->clearDecorators();
    $buttons[] = 'share_social';

    $this->addElement('Button', 'submit', array(
      'label' => 'Share',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $buttons[] = 'cancel';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}
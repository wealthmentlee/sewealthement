<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Content.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Admin_Content extends Wall_Form_Subform
{
    public function init()
    {
        $this->setTitle('WALL_ADMIN_CONTENT_TITLE');
        $this->setDescription('WALL_ADMIN_CONTENT_DESCRIPTION');

        $this->addElement('Checkbox', 'liketips', array('description' => 'WALL_ADMIN_CONTENT_LIKETIPS'));
        $this->getElement('liketips')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_LIKETIPS_DESC'));

        $this->addElement('Checkbox', 'rolldownload', array('description' => 'WALL_ADMIN_CONTENT_ROLLDOWNLOAD'));
        $this->getElement('rolldownload')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_ROLLDOWNLOAD_DESC'));

        $this->addElement('Checkbox', 'dialogconfirm', array('description' => 'WALL_ADMIN_CONTENT_DIALOGCONFIRM'));
        $this->getElement('dialogconfirm')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_DIALOGCONFIRM_DESC'));

        $this->addElement('Checkbox', 'listenable', array('description' => 'WALL_ADMIN_CONTENT_LISTENABLED'));
        $this->getElement('listenable')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_LISTENABLED_DESC'));

        $this->addElement('Checkbox', 'frendlistenable', array('description' => 'WALL_ADMIN_CONTENT_FRIENDLISTENABLED'));
        $this->getElement('frendlistenable')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_FRIENDLISTENABLED_DESC'));

        $this->addElement('Checkbox', 'autoload', array('description' => 'WALL_ADMIN_CONTENT_AUTOLOAD'));
        $this->getElement('autoload')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_AUTOLOAD_DESC'));

        $this->addElement('Checkbox', 'profilehome', array('description' => 'WALL_ADMIN_CONTENT_PROFILEHOME'));
        $this->getElement('profilehome')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_PROFILEHOME_DESC'));

        $this->addElement('Checkbox', 'bitly', array('description' => 'WALL_ADMIN_CONTENT_BITLY'));
        $this->getElement('bitly')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_BITLY_DESC'));

        $this->addElement('Checkbox', 'smile', array('description' => 'WALL_ADMIN_CONTENT_SMILE'));
        $this->getElement('smile')->addDecorator($this->elemDescDecorator('WALL_ADMIN_CONTENT_SMILE_DESC'));

        $view = $this->getView();
        $url = $view->url(array('module' => 'photoviewer', 'controller' => 'index'), 'admin_default', true);
        $this->addElement('dummy', 'photoviewer', array('content' => '<a href="' . $url . '">' . $view->translate('Photo Viewer Settings') . '</a>'));

    }

    public function applyDefaults()
    {
        $setting = Engine_Api::_()->getDbTable('settings', 'core');

        $this->getElement('liketips')->setValue($setting->getSetting('wall.content.liketips', true));
        $this->getElement('rolldownload')->setValue($setting->getSetting('wall.content.rolldownload', true));
        $this->getElement('dialogconfirm')->setValue($setting->getSetting('wall.content.dialogconfirm', true));
        $this->getElement('listenable')->setValue($setting->getSetting('wall.content.listenable', true));
        $this->getElement('frendlistenable')->setValue($setting->getSetting('wall.content.frendlistenable', true));
        $this->getElement('autoload')->setValue($setting->getSetting('wall.content.autoload', true));
        $this->getElement('profilehome')->setValue($setting->getSetting('wall.content.profilehome', false));
        $this->getElement('bitly')->setValue($setting->getSetting('wall.content.bitly', 1));
        $this->getElement('smile')->setValue($setting->getSetting('wall.content.smile', 1));


    }

    public function saveValues()
    {
        $setting = Engine_Api::_()->getDbTable('settings', 'core');

        $setting->setSetting('wall.content.liketips', (string)$this->getElement('liketips')->getValue());
        $setting->setSetting('wall.content.rolldownload', (string)$this->getElement('rolldownload')->getValue());
        $setting->setSetting('wall.content.dialogconfirm', (string)$this->getElement('dialogconfirm')->getValue());
        $setting->setSetting('wall.content.frendlistenable', (string)$this->getElement('frendlistenable')->getValue());
        $setting->setSetting('wall.content.listenable', (string)$this->getElement('listenable')->getValue());
        $setting->setSetting('wall.content.autoload', (string)$this->getElement('autoload')->getValue());
        $setting->setSetting('wall.content.profilehome', (string)$this->getElement('profilehome')->getValue());
        $setting->setSetting('wall.content.bitly', (string)$this->getElement('bitly')->getValue());
        $setting->setSetting('wall.content.smile', (string)$this->getElement('smile')->getValue());

    }

    protected function elemDescDecorator($description)
    {
        $decorator = new Wall_Form_Decorator_WallElemDesc();
        $decorator->setOption('description', $this->getView()->translate($description));
        return $decorator;
    }

}

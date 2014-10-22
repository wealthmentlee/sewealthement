<?php

class Apptouch_GrandopeningController extends Apptouch_Controller_Action_Bridge
{
  public function emailInit()
  {
//    Zend_Layout::startMvc()->setLayout(null);
  }

  public function emailAddAction()
  {
    $form = new Grandopening_Form_Collection();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $row = $this->_helper->api()->getDbtable('collections', 'grandopening')->createRow();
            $row->setFromArray($form->getValues());
            $row->save();

            $db->commit();
        }

        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
        $form->reset();
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your email was added.'));
    }
    $time = Engine_Api::_()->grandopening()->getEndTime();
    if ($time > ($serverTime = time())) {
      $time -=  $serverTime;
      $this->setPageTitle($this->view->translate('APPTOUCH_Site will be launched in'));
      $this->addPageInfo('launchTime', $time);
      $this->addPageInfo('serverTime', $serverTime);
      $timeEl = $this->dom()->new_('div', array('class' => 'go-time'))
        ->append($this->dom()->new_('div')->append($this->dom()->new_('div', array('class' => 'go-days'), sprintf('%03d', floor($time/(86400)))))->append($this->dom()->new_('span', array(), $this->view->translate('DAYS'))))
        ->append($this->dom()->new_('div')->append($this->dom()->new_('div', array('class' => 'go-hours'), sprintf('%02d', floor($time/3600)%24 )))->append($this->dom()->new_('span', array(), $this->view->translate('HOURS'))))
        ->append($this->dom()->new_('div')->append($this->dom()->new_('div', array('class' => 'go-minutes'), sprintf('%02d', floor($time/60) % 60)))->append($this->dom()->new_('span', array(), $this->view->translate('MINUTES'))))
        ->append($this->dom()->new_('div')->append($this->dom()->new_('div', array('class' => 'go-seconds'), sprintf('%02d', floor($time) % 60)))->append($this->dom()->new_('span', array(), $this->view->translate('SECONDS'))));
      $this->add($this->component()->html($timeEl));
    }
    $this
      ->add($this->component()->html('<h3>'.$this->view->translate('APPTOUCH_We are currently building a new site which will be ready soon.').'</h3>'))
      ->add($this->component()->form($form))
      ->renderContent();
  }
}

<?php

class Apptouch_IndexController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
}

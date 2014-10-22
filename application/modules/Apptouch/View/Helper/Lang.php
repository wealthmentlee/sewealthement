<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 10.09.12
 * Time: 15:24
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_Lang extends Zend_View_Helper_Abstract
{
  protected $_javascriptContainer = 'core.lang.add(%s);';

  protected $_includeScriptTags = true;


  public function lang()
  {
    return $this;
  }

  public function add($string = null)
  {
    if (null !== $string) {
      if (is_array($string)) {
        foreach ($string as $subString) {
          $this->_getContainer()->append($subString);
        }
      } else {
        $this->_getContainer()->append($string);
      }
    }

    return $this;
  }

  public function render($asArray = false)
  {
    if ($this->_getContainer()->count() <= 0) {
      return '';
    }

    $content = '';

    // Header
    if ($this->_includeScriptTags) {
      $content .= '<script data-cfasync="false" type="text/javascript">' . "\n"
        . '//<![CDATA[' . "\n";
    }

    // Data
    $vars = array_flip(array_unique($this->_getContainer()->getArrayCopy()));
    foreach ($vars as $key => &$value) {
      $value = $this->view->translate($key);
    }
    if ($asArray) {
      return $vars;
    }

    $content .= sprintf($this->_javascriptContainer, Zend_Json::encode($vars));

    // Footer
    if ($this->_includeScriptTags) {
      $content .= "\n" . '//]]>' . "\n" .
        '</script>';
    }

    return $content;
  }

  public function __toString()
  {
    return $this->render();
  }

  public function toString()
  {
    return $this->render();
  }

  public function toArray()
  {
    return $this->render(true);
  }

  /**
   * Get the container
   *
   * @return ArrayObject
   */
  protected function _getContainer()
  {
    if (!Zend_Registry::isRegistered(get_class($this))) {
      $container = new ArrayObject();
      Zend_Registry::set(get_class($this), $container);
    } else {
      $container = Zend_Registry::get(get_class($this));
    }
    return $container;
  }

}


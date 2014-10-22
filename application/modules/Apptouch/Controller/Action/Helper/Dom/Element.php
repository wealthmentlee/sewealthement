<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 05.09.12
 * Time: 18:53
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_Dom_Element
{
  public $name = null;
  public $attrs;
  public $html;
  public $text;
  public $parent;
  protected static $singleTags = array(
    'img',
    'br',
    'input',
    'link',
    'meta',
    'doctype',
  );

  public function __construct($name, array $attrs = array(), $text = '', $html = array())
  {
    $this->name = $name;
    $this->attrs = $attrs;
    $this->html = $html;
    $this->text = $text;
  }

  public function tag($name = false)
  {
    if (is_string($name))
      $this->name = $name;
    else
      return $this->name;
  }

  public function attr($arg1 = false, $arg2 = null)
  {
    if (is_array($arg1))
      $this->attrs = array_merge($this->attrs, $arg1);
    elseif (is_string($arg1) && (is_string($arg2) || is_bool($arg2) || is_numeric($arg2))){
      $this->attrs[$arg1] = $arg2;
      return $this;
    }
    elseif (is_string($arg1) && $arg2 === null)
      return isset($this->attrs[$arg1]) ? $this->attrs[$arg1] : null;
    else
      return $this->attrs;
  }

  public function append($html)
  {
    if ($html instanceof Apptouch_Controller_Action_Helper_Dom_Element) {
      $this->html[] = $html;
      $html->parent = $this;
    }
    elseif (is_array($html)) {
        foreach ($html as $el) {

        }
        $this->html = array_merge($this->html, $html);
    }
    return $this;
  }

  public function remove()
  {
    unset($this);
  }
  public function toArray(){

    $asArray = array(
      'name' => $this->name,
      'attrs' => $this->attrs,
      'text' => $this->text,
      'html' => array()
    );
    if(is_array($this->html))
      foreach($this->html as $child){
        $asArray['html'][] = $child->toArray();
      }
    return $asArray;
  }

  public function toString(){
    return $this->__toString();
  }

  public function __toString(){
    $html = '<' . $this->name;
    $isSingle = in_array($this->name, self::$singleTags);
    $singleSlash = $isSingle ? '/': '';

    if($this->attrs)
      foreach($this->attrs as $name => $value){
        $html .=  ' ' . $name . '="' . $value . '" ';
      }
    $html .= $singleSlash . '>' . $this->text;

    if(is_array($this->html))
      foreach($this->html as $child){
        $html .= $child;
      }
    $html .= $isSingle ? '' : '</' . $this->name . '>';
    return $html;
  }
  public function __destruct()
  {

  }
}
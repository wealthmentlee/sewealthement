<?php
class Apptouch_Api_Navigator
  extends Core_Api_Abstract
{
  protected $appCodeName;
  protected $appName;
  protected $appVersion;
  protected $grade;
  protected $language;
  protected $oscpu;
  protected $platform;
  protected $uaString;
  protected $array;

  public function __construct()
  {

    $navigatorInfo = Zend_Json::decode($_COOKIE['navigator']);
    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
      $navigatorInfo['uaString'] = $_SERVER['HTTP_USER_AGENT'];
    }
    $this->array = $navigatorInfo;
    $this->setFromArray($navigatorInfo);
  }

  public function getUserAgent()
  {
    return $this->uaString;
  }

  public function getAppCodeName()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->appCodeName;
  }

  public function getAppName()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->appName;

  }

  public function getAppVersion()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->appVersion;

  }

  public function getLanguage()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->language;

  }

  public function getGrade()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->grade;

  }

  public function getOsCpu()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->oscpu;

  }

  public function getPlatform()
  {
    if($this->isPicup()){
      return false;
    }
    return $this->platform;

  }

  private function setFromArray(array $info)
  {
    foreach($info as $propName => $val){
      if(property_exists($this, $propName)){
        $this->$propName = $val;
      }
    }
  }

  public function isPicup()
  {

    $ua = $this->getUserAgent();

    $is = (preg_match('/imageuploader/', $ua) ||
      (
        preg_match('/picup/', $ua) &&
          preg_match('/cfnetwork/', $ua)
      )
    );

    return $is;
  }

}

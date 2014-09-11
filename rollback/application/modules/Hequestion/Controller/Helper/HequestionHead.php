<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HequestionHead.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */




class Hequestion_Controller_Helper_HequestionHead extends Zend_Controller_Plugin_Abstract
{
  public function postDispatch(Zend_Controller_Request_Abstract $request)
  {

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Hequestion/externals/scripts/core.js');

    $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

    $script = <<<CONTENT
        //en4.core.runonce.add(function (){
        window.addEvent('domready', function (){

          (function(){
            $$('#activity-feed a[href*='+en4.core.baseUrl+'question-view'+']:not(.heEventClickActive)').each(function (i){
              i.addClass('heEventClickActive');
              i.addEvent('click', function (e){
                e.stop();
                var question_id = $(this).get('href').replace('https://', '').replace('http://', '').replace(document.domain,'').replace(en4.core.baseUrl+"question-view/", '').split('/')[0];
                if (question_id){
                  Smoothbox.open(new Element('a', {href: en4.core.baseUrl+"question-box/"+question_id}));
                }
              });
            });
          }).periodical(2000);

        });
CONTENT;

    $headScript->appendScript($script);





  }





}
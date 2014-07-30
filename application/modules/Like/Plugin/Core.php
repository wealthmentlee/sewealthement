<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Plugin_Core extends  Zend_Controller_Plugin_Abstract
{
  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();

		if ($view instanceof Zend_View) {
			$view->headScript()
        ->appendFile('application/modules/Like/externals/scripts/core.js');
			
			$view->headTranslate(array(
				'like_You', 'like_and', 'like_people', 'like_like it', 'like_Suggest to Friends', 'like_Unlike', 'like_Show Like', 'like_Hide', 'like_Like',
        'You and %s people like it.', 'You and %s person like it.', 'You like it.', 'No one like it.', 'like_What do you like to read?',
        'like_What pages do you want to visit?', 'like_What kind of events do you like?', 'like_What groups do you like?', 'like_What classifieds do you like?',
        'like_What albums do you like?', 'like_What videos do you like?', 'like_What music do you like?', 'like_What quizzes do you like to experience?',
        'like_What polls do you like?', 'like_Are you sure you want to unlike this?', "like_You like it.", "like_You and %s other person like it.",
        "like_You and %s other people like it.", "like_%s other people like it.", "like_%s other person like it.", "like_No one like it.", "like_I like %s.",
        "like_%s likes %s."
			));
		}

    if ($view instanceof Zend_View && $viewer->getIdentity() && Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
			
			$itemTypes = Engine_Api::_()->like()->getSupportedModules();
			if (in_array($subject->getType(), $itemTypes)) {
				$view->like_data = $html = $view->action("list", "comment", "like", array("type" => $subject->getType(), "id" => $subject->getIdentity()));

				$likeUrl = $view->url(array('action' => 'like'), 'like_comment');
				$unlikeUrl = $view->url(array('action' => 'unlike'), 'like_comment');
				$hintUrl = $view->url(array('action' => 'hint'), 'like_comment');
				$showLikesUrl = $view->url(array('action' => 'list'), 'like_comment');
				$postCommentUrl = $view->url(array('action' => 'create'), 'like_comment');

				$script = "
					var likeTips = null;
					en4.core.runonce.add(function(){
					var options = {
						'container' : 'comments',
						'html' : ". Zend_Json_Encoder::encode($html) .",
						'url' : {
							'like' : '".$likeUrl."',
							'unlike' : '".$unlikeUrl."',
							'hint' : '".$hintUrl."',
							'showLikes' : '".$showLikesUrl."',
							'postComment' : '".$postCommentUrl."'
						}
					};
					likeTips = new LikeTips('".$subject->getType()."', ".$subject->getIdentity().", options);
				})";

				$view->headScript()
					->appendScript($script);
			}
    }
  }

  public  function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $fb_tw_url = @$_SESSION['fb_tw_url'];
    if(
      $request->getModuleName()=='user' &&
      $request->getControllerName()=='index' &&
      $request->getActionName()=='home' &&
      $fb_tw_url
    )
    {
      header('Location: '.$fb_tw_url);
      exit;
    }
  }
}
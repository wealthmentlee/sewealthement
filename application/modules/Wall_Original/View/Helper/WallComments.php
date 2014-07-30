<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallComments.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallComments extends Zend_View_Helper_Abstract
{

  public function wallComments($subject, $viewer, $params = array())
  {
    $view_vars = array(
      'subject' => $subject,
      'viewer' => $viewer,
      'params' => $params
    );

    // Perms
    $view_vars['canComment'] = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
    $view_vars['canDelete'] = $subject->authorization()->isAllowed($viewer, 'edit');

    // Likes
    $view_vars['viewAllLikes'] = (!empty($params['viewAllLikes'])) ?  $params['viewAllLikes'] : false;
    $view_vars['likes'] = $likes = $subject->likes()->getLikePaginator();

    // Comments

    // If has a page, display oldest to newest
    if( null !== ( $page = (!empty($params['page'])) ?  $params['page'] : false) )
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
      $view_vars['comments'] = $comments;
      $view_vars['page'] = $page;
    }

    // If not has a page, show the
    else
    {
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
      $view_vars['comments'] = $comments;
      $view_vars['page'] = $page;
    }

    if( $viewer->getIdentity() && $canComment ) {
      $view_vars['form'] = $form = new Core_Form_Comment_Create();
      $form->populate(array(
        'identity' => $subject->getIdentity(),
        'type' => $subject->getType(),
      ));
    }

    return $this->view->partial('comment/list.tpl', 'wall', $view_vars);
  }


}

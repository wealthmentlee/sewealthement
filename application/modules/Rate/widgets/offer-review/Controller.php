<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_OfferReviewController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction(){

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers')) {
      return $this->setNoRender();
    }

  	/**
		 * @var $subject Offers_Model_Offer
		 */
		$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('offer');
  	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if ( !($subject instanceof Offers_Model_Offer) ) {
      return $this->setNoRender();
		}

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/offer-review-ajax';
  	$this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/';
  	$this->view->addScriptPath($path);

    $this->view->headTranslate(array('RATE_REVIEW_DELETE', 'RATE_REVIEW_DELETEDESC'));

    $this->view->offerId = $offer_id = $subject->getIdentity();

    $this->view->types = $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer_id);
    $this->view->countOptions = count($types);

    $form = new Rate_Form_OfferReview_Create();
    $this->view->js = $form->addVotes($types);
    $this->view->form = $form;

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('offerreview');

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    $p = $this->_getParam('page', 1);

    $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
    $this->view->paginator = $paginator = $tbl->getPaginator($offer_id, $viewer->getIdentity(), $p);
    $this->view->isAllowedPost = $tbl->isAllowedPost($offer_id, $viewer);

    // is allowed remove
    $this->view->isAllowedRemove = true; //Engine_Api::_()->getApi('core', 'rate')->isAllowRemoveReview($offer_id, $viewer);

    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $this->view->paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
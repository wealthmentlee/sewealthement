<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: StoretController.php 3.9.14 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_DonationController extends Apptouch_Controller_Action_Bridge
{
  protected $_settings;

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
    $this->_settings = Engine_Api::_()->getApi('settings', 'core');

    /**
     * @var $subject Donation_Model_Donation
     */
    $subject = null;

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('donation_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('donation', $id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }
    if($subject){
      if($subject->type == 'charity'){
        if(!$this->_settings->getSetting('donation.enable.charities',1)){
          if($this->_settings->getSetting('donation.enable.projects',1)){
            return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true); // todo
          }
          else{
            return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true); // todo
          }
        }
      }
      else{
        if(!$this->_settings->getSetting('donation.enable.projects',1)){
          if($this->_settings->getSetting('donation.enable.fundraising',1)){
            return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true); // todo
          }
          else{
            return $this->_helper->redirector->gotoRoute(array(),'donation_charity_browse',true); //todo
          }
        }
      }
    }
  }

  public function indexBrowseAction()
  {
    //Check enabled the charity type
    if(!$this->_settings->getSetting('donation.enable.charities',1)){
      if($this->_settings->getSetting('donation.enable.projects',1)){
        return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true); // todo
      }
      else{
        return $this->_helper->redirector->gotoRoute(array(),'donation_fundraise_browse',true); // todo
      }
    }

    $this->view->currency = $this->_settings->getSetting('payment.currency', 'USD');

    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }

    // Prepare data
    $table = Engine_Api::_()->getItemTable('donation');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $browse_params = array(
      'type' => 'charity',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'orderBy' => $order,
    );

    if ($this->_getParam('category_id')) {
      $browse_params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $browse_params['search'] = $this->_getParam('search');
    }

    $paginator = $table->getDonationsPaginator($browse_params);

    $searchForm = $this->getSearchForm();

    $like_count = array();
    $supporters = array();
    $params = array(
      'limit' => 7,
      'resource_type' => 'donation',
    );

    //Get Supporters
    foreach($paginator as $donation)
    {
      $like_count[$donation->getIdentity()] = Engine_Api::_()->like()->getLikeCount($donation);
      $params['resource_id'] = $donation->getIdentity();
      $select_supporters = Engine_Api::_()->like()->getLikesSelect($params);
      $supporters[$donation->getIdentity()] = $select_supporters->query()->fetchAll();
    }

    $canCreate = Engine_Api::_()->authorization()->isAllowed('donation', null, 'create_charity');

    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_main');
    $this->add($this->component()->navigation($navigation));

    $this->add($this->component()->itemSearch($searchForm));

    if( $paginator->getTotalItemCount() > 0 ) {
      $this->add($this->component()->itemList($paginator, 'BrowseCharityList', array('listPaginator' => true,)));

//      $this->add($this->component()->paginator($paginator));
    } else {
      $this->add($this->component()->html($this->view->translate('DONATION_Nobody has created a donation yet.')));
    }

    $this->renderContent();
  }

  public function indexViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = Engine_Api::_()->core()->getSubject();


    if(!$subject || $subject->status == 'cancelled'){
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }

    if(!$subject->approved){
      $page = $subject->getPage();
      if($page){
        if(!$page->getDonationPrivacy($subject->type)){
          return $this->_forward('requiresubject', 'error', 'core'); // todo
        }
      }
      elseif(!$subject->isOwner($viewer)){
        return $this->_forward('requiresubject', 'error', 'core'); // todo
      }
    }

    if (!$subject->getOwner()->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }

    $isOwner = false;
    $page = $subject->getPage();

    if($page){
      if($page->getDonationPrivacy($subject->type)){
        $isOwner = true;
      }
    }
    elseif($subject->isOwner($viewer)){
      $isOwner = true;
    }

    $this->add($this->component()->subjectPhoto($subject));

    // Add Donation status
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $status = '<b>'.$this->view->translate('DONATION_Raised:') . '</b> ' . $this->view->locale()->toCurrency((double)$subject->getRaised(), $currency) ;
    if ( in_array($subject->type, array('project', 'fundraise')) ) {
      if ($subject->target_sum > 0) {
        $status .= '<br><b>' . $this->view->translate('DONATION_Target:') . '</b> ' . $this->view->locale()->toCurrency((double)$subject->getTargetSum(), $currency);
      }
      if (strtotime($subject->expiry_date) != '1546300800') {
        $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($subject->expiry_date), new DateTime(date("Y-m-d H:i:s")));
        $month = (int)$left->format('%m');
        $day = (int)$left->format('%d');
        $status .= '<br>';
        if($month > 0) {
          $status .= '<b>' . $this->view->translate(array("%s month", "%s months", $month), $month) . '</b> ';
        }
        $status .= '<b>' . $this->view->translate(array("%s day left", "%s days left", $day), $day) . '</b>';
      }

      if ($subject->target_sum > 0) {
        $st = (int) (100 * $subject->getRaised() / $subject->getTargetSum());

        $status .= '<br><div class="progress_cont">
    <div class="progress">
            <div style="width: ' . ($st > 100 ? 100 : $st) . '%" class="bar"></div>
    </div>
    <span style="font-size: 12px"> ' . ($st > 100 ? 100 : $st) . '%</span>
    <br>
  </div>';
      }
    }
    $this->add($this->component()->html($status));

    // adding donation options
    $options = '';
    if ($subject->status == 'expired') {
      $options .= '<img src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/tick32.png" align="left"> ';
      $options .= '<span>' . $this->view->translate("Completed") . '</span><br>';
      $options .= $this->view->translate("Thank you to everyone who donated and supported.");
    } elseif($subject->status == 'initial') {
      $options .= '<img src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/warning.png" align="left"> ';
      if ($isOwner) {
        $options .= '<span>' . $this->view->translate("Initialization") . '</span><br>';
        $options .= $this->view->translate('That others have donated, first you need to enter %1$sfinancial information%2$s!',
          '<a href="'.$this->view->url(array(
            'controller' => $subject->type,
            'action' => 'fininfo',
            'donation_id' => $subject->getIdentity()), 'donation_extended', true).'">', '</a>');
      } else {
        $options .= $this->view->translate("None Active");
      }
    } elseif($subject->approved) {
      $options .= '<button onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $subject->getType(),'object_id' => $subject->getIdentity()),'donation_donate',true) .'\');">' . $this->view->translate('DONATION_Donate') . '</button>';
    }
    if (!$subject->approved && $isOwner) {
      $options .= '<br><img align="left" src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/not_approved.png"> ';
      $options .= '<span>' . $this->view->translate("DONATION_Not Approved") . '</span>';
      $options .= '<br>' . $this->view->translate("DONATION_Your donation is not approved. Please wait while administrator approve this donation.");
    }
    $this->add($this->component()->html($options));

    // Add navigation (quick links)
    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_profile');
    $this->add($this->component()->quickLinks($navigation));

    //Add creation date
    $text = $this->view->translate('Created by %1$s on %2$s', $this->view->htmlLink($subject->getOwner()->getHref(), $subject->getOwner()->getTitle(),
      array('class' => 'member_donation_members_icon')), $this->view->locale()->toDate($subject->creation_date));
    $this->add($this->component()->html($text));
    // Add Donation Description
    $this->add($this->component()->html($subject->getDescription(false,false)));

    // Add photos
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 200));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->add($this->component()->html('<h3>'.$this->view->translate('Photos').'</h3>'));
    $this->add($this->component()->gallery($paginator, null, array('canComment' => false)));

    // Add Comments and Like
    $this->add($this->component()->comments());

    // Add Fundraises
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $table = Engine_Api::_()->getDbTable('donations', 'donation');
    $params = array(
      'parent_id' => $subject->getIdentity()
    );
    $paginator = Zend_Paginator::factory($table->getFundraises($params));
    $count =  $paginator->getTotalItemCount();
    if( $count ) {
      $this->add($this->component()->html('<h3>' . $this->view->translate('Fundraisers (%s)', $count) . '</h3>'));
      $items = array();
      $isTablet = Engine_Api::_()->apptouch()->isTablet();

      foreach( $paginator as $item ) {
        $owner = $item->getOwner();
        $photoUrl = $owner->getPhotoUrl();
        $title = '<a style="z-index: 9999;" onclick="$.mobile.changePage(\'' . $owner->getHref() . '\')">' . $owner->getTitle() . '</a> ';
        $title .= $this->view->translate('%1$s raised so far', $this->view->locale()->toCurrency((double)$item->raised_sum, $currency));
        $temp_item = array(
          'title' => $title,
          'descriptions' => array(),
          'href' => $item->getHref(),
          'photo' => $photoUrl,
          'creation_date' => ''
        );

//        if($isTablet)
//          $temp_item['descriptions'][] = $item->description ? $this->view->feedDescription($item->description, 100) : $this->view->feedDescription($item->content, 100);
        $items[] = $temp_item;
      }
      $this->add($this->component()->customComponent('itemList', array('items' => $items)));
    }

    $this->renderContent();
  }

  public function indexManageAction()
  {

    $canCreateCharity = $this->getDonationApi()->canCreateCharity();

    $canCreateProject = $this->getDonationApi()->canCreateProject();

    if(!$canCreateCharity && !$canCreateProject){
      return $this->_forward('requireauth', 'error', 'core'); // todo
    }

    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_main');

    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    if(!$viewer->getIdentity()){
      return $this->_forward('requireauth', 'error', 'core'); // todo
    }

    $currency = $this->_settings->getSetting('payment.currency', 'USD');

    $params = array(
      'user_id' => $viewer->getIdentity(),
      'order' => 'DESC',
      'page' => $this->_getParam('page',1),
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
    );

    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }

    $params['orderBy'] = $order;

    if ($this->_getParam('category_id')){
      $params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $params['search'] = $this->_getParam('search');
    }

    $donations = Engine_Api::_()->getItemTable('donation')->getDonationsPaginator($params);

    $searchForm = $this->getSearchForm();

    $this->setPageTitle($this->view->translate('Donations'));

    $this->add($this->component()->navigation($navigation));

    $this->add($this->component()->itemSearch($searchForm));

    if($donations->getTotalItemCount() > 0) {
      $items = array();

      foreach( $donations as $donation ) {
        $options = array();
        $options[] = array(
          'label' => $this->view->translate('DONATION_Delete Donation'),
          'attrs' => array(
            'href' => $this->view->url(array('controller' => $donation->type,'action' => 'delete','donation_id' => $donation->getIdentity()),
                'donation_extended', true),
            'class' => 'buttonlink smoothbox'
          ),
        );

        $item = array(
          'title' => $donation->getTitle(),
          'photo' => $donation->getPhotoUrl(),
          'href' => $donation->getHref(),
          'descriptions' => array(
            Engine_String::substr($donation->getDescription(),0,200)
          ),
          'manage' => $options
        );

        $items[] = $item;
      }
      $this->add($this->component()->customComponent('itemList', array('items' => $items)));
    } else {
      $this->add($this->component()->html($this->view->translate('DONATION_You do not have any donations yet.')));
    }


    $this->renderContent();
  }


  public function projectInit()
  {
    $this->addPageInfo('contentTheme', 'd');
    $this->view->page_id = $this->_getParam('page_id');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (!$settings->getSetting('donation.enable.projects', 1)) {
      if ($this->view->page_id) {
        return $this->_helper->redirector->gotoRoute(array(), 'default'); // todo
      } elseif ($settings->getSetting('donation.enable.charities', 1)) {
        return $this->_helper->redirector->gotoRoute(array(), 'donation_charity_browse', true); // todo
      } else {
        return $this->_helper->redirector->gotoRoute(array(), 'donation_fundraise_browse', true); //todo
      }
    }


    if ($this->view->page_id) {
      $this->view->subject = $this->subject = Engine_Api::_()->getItem('page', $this->view->page_id);
      if ($this->subject) {
        $api = Engine_Api::_()->getApi('page', 'donation');
        $this->view->navigation = $api->getNavigation($this->subject);
      }
    }
  }

  public function projectBrowseAction()
  {
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $settings = Engine_Api::_()->getApi('settings', 'core');

// Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


// Prepare data
    $table = Engine_Api::_()->getItemTable('donation');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $browse_params = array(
      'type' => 'project',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $settings->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'orderBy' => $order,
    );

    if ($this->_getParam('category_id')) {
      $browse_params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $browse_params['search'] = $this->_getParam('search');
    }
    $paginator = $table->getDonationsPaginator($browse_params);

    $searchForm = $this->getSearchForm();

    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_main');
    $this->add($this->component()->navigation($navigation));

    $this->add($this->component()->itemSearch($searchForm));

    if($paginator->getTotalItemCount() > 0) {
      $this->add($this->component()->itemList($paginator, 'BrowseProjectList', array('listPaginator' => true,)));

//      $this->add($this->component()->paginator($paginator));
    } else {
      $this->add($this->component()->html($this->view->translate('DONATION_Nobody has created a donation yet.')));
    }

    $this->renderContent();
  }

  public function projectDeleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getRequest()->getParam('donation_id'));

    if (!$donation || $donation->type != 'project') {
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $page = $donation->getPage();


    if ($page) {
      if (!$page->getDonationPrivacy('project')) {
        return $this->_forward('requireauth', 'error', 'core'); // todo
      }
    } elseif (!$donation->isOwner($viewer)) {
      return $this->_forward('requireauth', 'error', 'core'); // todo
    }

// Make form
   $form = new Donation_Form_Delete();

    if (!$donation) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Donation doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $donation->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $donation->deleteDonation();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(
      $this->view->url(array(), 'donation_project_browse'),
      Zend_Registry::get('Zend_Translate')->_('The selected donation has been deleted.'),
      true
    );
  }

  public function projectFininfoAction()
  {
    $form = new Donation_Form_FinancialInfo();
    $donation_id = $this->_getParam('donation_id');

    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      if (null !== $this->_getParam('donation_id')) {
        $subject = Engine_Api::_()->getItem('donation', $donation_id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }
    $table = Engine_Api::_()->getItemTable('donation_fin_info');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = array_merge($form->getValues(), array(
        'donation_id' => $donation_id
      ));
      $fininfo = $table->createRow();
      $fininfo->setFromArray($values);
      $fininfo->save();
      $subject->status = 'active';
      if ($subject->save()) {
        $subject->addAction();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->redirect($this->view->url(array('donation_id' => $donation_id, 'title' => $subject->getUrlTitle()), 'donation_profile'));
  }


  public function fundraiseInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    $this->_settings = Engine_Api::_()->getApi('settings', 'core');

    if(!$this->_settings->getSetting('donation.enable.fundraising',1)){
      if($this->_settings->getSetting('donation.enable.charities',1)){
        return $this->_helper->redirector->gotoRoute(array(),'donation_charity_browse',true); // todo
      }
      else{
        return $this->_helper->redirector->gotoRoute(array(),'donation_project_browse',true); // todo
      }
    }
  }

  public function fundraiseBrowseAction()
  {
    $this->view->currency = $currency = $this->_settings->getSetting('payment.currency', 'USD');

    // Get params
    switch ($this->_getParam('sort', 'recent')) {
      case 'popular':
        $order = 'view_count';
        break;
      case 'recent':
      default:
        $order = 'modified_date';
        break;
    }


    // Prepare data
    $table = Engine_Api::_()->getItemTable('donation');
    if (!in_array($order, $table->info('cols'))) {
      $order = 'modified_date';
    }

    $browse_params = array(
      'type' => 'fundraise',
      'status' => 'active',
      'approved' => 1,
      'ipp' => $this->_settings->getSetting('donation_browse_count', 10),
      'page' => $this->_getParam('page', 1),
      'orderBy' => $order,
    );

    if ($this->_getParam('category_id')){
      $browse_params['category_id'] = $this->_getParam('category_id');
    }

    if ($this->_getParam('search', false)) {
      $browse_params['search'] = $this->_getParam('search');
    }

    $paginator = $table->getDonationsPaginator($browse_params);

    // Add Navigation
    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_main');
    $this->add($this->component()->navigation($navigation));

    // Add Search Form
    $searchForm = $this->getSearchForm();
    $this->add($this->component()->itemSearch($searchForm));

    $this->add($this->component()->itemList($paginator, 'BrowseFundraiseList', array('listPaginator' => true,)));
//    $this->add($this->component()->paginator($paginator));

    $this->renderContent();
  }

  public function fundraiseViewAction()
  {
    $subject = null;
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('fundraise_id');
      if (null !== $id) {
        $subject = Engine_Api::_()->getItem('donation', $id);
        if(!$subject){
          return $this->_forward('requiresubject', 'error', 'core'); // todo
        }
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if($subject->status == 'cancelled' || (!$subject->approved && !$subject->isOwner($viewer))){
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }

    $isOwner = false;
    $page = $subject->getPage();

    if($page){
      if($page->getDonationPrivacy($subject->type)){
        $isOwner = true;
      }
    }
    elseif($subject->isOwner($viewer)){
      $isOwner = true;
    }

    // Add donation photo
    $this->add($this->component()->subjectPhoto($subject));

    // Add Donation status
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $status = '<b>'.$this->view->translate('DONATION_Raised:') . '</b> ' . $this->view->locale()->toCurrency((double)$subject->getRaised(), $currency) ;
    if ( in_array($subject->type, array('project', 'fundraise')) ) {
      if ($subject->target_sum > 0) {
        $status .= '<br><b>' . $this->view->translate('DONATION_Target:') . '</b> ' . $this->view->locale()->toCurrency((double)$subject->getTargetSum(), $currency);
      }
      if (strtotime($subject->expiry_date) != '1546300800') {
        $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($subject->expiry_date), new DateTime(date("Y-m-d H:i:s")));
        $month = (int)$left->format('%m');
        $day = (int)$left->format('%d');
        $status .= '<br>';
        if($month > 0) {
          $status .= '<b>' . $this->view->translate(array("%s month", "%s months", $month), $month) . '</b> ';
        }
        $status .= '<b>' . $this->view->translate(array("%s day left", "%s days left", $day), $day) . '</b>';
      }

      if ($subject->target_sum > 0) {
        $st = (int) (100 * $subject->getRaised() / $subject->getTargetSum());

        $status .= '<br><div class="progress_cont">
    <div class="progress">
            <div style="width: ' . ($st > 100 ? 100 : $st) . '%" class="bar"></div>
    </div>
    <span style="font-size: 12px"> ' . ($st > 100 ? 100 : $st) . '%</span>
    <br>
  </div>';
      }
    }
    $this->add($this->component()->html($status));

    // Adding parent donation
    $parent_donation = Engine_Api::_()->getItem('donation', $subject->parent_id);
    $dom_el = '<a href="' . $parent_donation->getOwner()->getHref() . '" title="' . $parent_donation->getOwner()->getTitle() . '"> ' . $parent_donation->getOwner()->getTitle() . '</a> ';
    $dom_el .= $this->view->translate('is raising funds for');
    $dom_el .= '<a style="float: left;" href="' . $parent_donation->getHref() . '"><img src="' . $parent_donation->getPhotoUrl() . '" style="float: left; width: 100px; margin-right: 5px;"></a>';
    $dom_el .= $this->view->htmlLink($parent_donation->getHref(), $parent_donation->getTitle());
    $dom_el .= '<br>' . $parent_donation->getDescription(false,false);
    $parent_el = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-collapsed' => false, 'data-content-theme' => 'b', 'data-theme' => 'b'), '', array(
      $this->dom()->new_('h3', array(), $parent_donation->getTitle()),
      $this->dom()->new_('p', array(), $dom_el)
    ));
    $this->add($this->component()->html($parent_el));

    // adding donation options
    $options = '';
    if ($subject->status == 'expired') {
      $options .= '<img src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/tick32.png" align="left"> ';
      $options .= '<span>' . $this->view->translate("Completed") . '</span><br>';
      $options .= $this->view->translate("Thank you to everyone who donated and supported.");
    } elseif($subject->status == 'initial') {
      $options .= '<img src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/warning.png" align="left"> ';
      if ($isOwner) {
        $options .= '<span>' . $this->view->translate("Initialization") . '</span><br>';
        $options .= $this->view->translate('That others have donated, first you need to enter %1$sfinancial information%2$s!',
          '<a href="'.$this->view->url(array(
            'controller' => $subject->type,
            'action' => 'fininfo',
            'donation_id' => $subject->getIdentity()), 'donation_extended', true).'">', '</a>');
      } else {
        $options .= $this->view->translate("None Active");
      }
    } elseif($subject->approved) {
      $options .= '<button onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $subject->getType(),'object_id' => $subject->getIdentity()),'donation_donate',true) .'\');">' . $this->view->translate('DONATION_Donate') . '</button>';
    }
    if (!$subject->approved && $isOwner) {
      $options .= '<br><img align="left" src="' . $this->view->baseUrl() . '/application/modules/Donation/externals/images/not_approved.png"> ';
      $options .= '<span>' . $this->view->translate("DONATION_Not Approved") . '</span>';
      $options .= '<br>' . $this->view->translate("DONATION_Your donation is not approved. Please wait while administrator approve this donation.");
    }
    $this->add($this->component()->html($options));

    // Add navigation (quick links)
    $navigation = Engine_Api::_()->getApi('menus', 'apptouch')
      ->getNavigation('donation_profile');
    $this->add($this->component()->quickLinks($navigation));

    // Add Donation Description
    $this->add($this->component()->html($subject->getDescription(false,false)));

    // Add photos
    $album = $subject->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 200));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->add($this->component()->html('<h3>'.$this->view->translate('Photos').'</h3>'));
    $this->add($this->component()->gallery($paginator, null, array('canComment' => false)));

    // Add Comments and Like
    $this->add($this->component()->comments());

    $this->renderContent();
  }

  public function fundraiseDeleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getRequest()->getParam('donation_id'));


    $viewer = Engine_Api::_()->user()->getViewer();

    if(!$donation || $donation->type != 'fundraise'){
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }

    if(!$donation->isOwner($viewer)){
      return $this->_forward('requireauth', 'error', 'core'); // todo
    }
    // Make form
    $form = new Donation_Form_Delete();

    if( !$this->getRequest()->isPost() )
    {

      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $donation->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $donation->deleteDonation();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(
      $this->view->url(array(), 'donation_fundraise_browse'),
      Zend_Registry::get('Zend_Translate')->_('The selected donation has been deleted.'),
      true
    );
  }


  protected $_subject;

  protected $_data;

  protected $_status;

  protected $_session;

  public function donationInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    $this->_session = new Zend_Session_Namespace('Donation_Transaction');
    $this->_subject = null;
    if (!Engine_Api::_()->core()->hasSubject('donation')) {
      $id = $this->_getParam('object_id');
      if (null !== $id) {
        $this->_subject = Engine_Api::_()->getItem('donation', $id);
        Engine_Api::_()->core()->setSubject($this->_subject);
      }
    }

    $this->view->donation = $this->_subject;
  }

  public function donationDonateAction()
  {
    if(!$this->_subject || $this->_subject->status != 'active' || !$this->_subject->approved){
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }
    //If members can not to make donation anonymously and viewer is quest
    if(!$this->_subject->allow_anonymous && !$this->_helper->requireUser()->isValid()){
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $predefine_list = array();

    if($this->_subject->predefine_list)
    {
      $predefine_list = explode(',',$this->_subject->predefine_list);
    }

    $this->add($this->component()->subjectPhoto($this->_subject));

    if ($this->_subject->type == 'charity') {
      $desc = $this->view->translate("DONATION_This charity raised %s.", $this->view->locale()->toCurrency((double)$this->_subject->raised_sum, $currency));
    } else {
      $desc = $this->view->translate('DONATION_This project needs to raise a further %1$s to reach its funding target of %2$s',
        $this->view->locale()->toCurrency((double)$this->_subject->target_sum - $this->_subject->raised_sum, $currency),
        $this->view->locale()->toCurrency((double)$this->_subject->target_sum, $currency));
    }
    $this->add($this->component()->html($desc));

    if (count($predefine_list)) {
      $text = '<h3>'.$this->view->translate('DONATION_Choose the amount you would like to donate') . '</h3><br>';
      $btns = '';
      foreach ($predefine_list as $list) {
        $btns .= '<a class="btn" id="' . $list . '">' . $this->view->locale()->toCurrency((double)$list, $currency) . '</a> ';
      }
      $text .= '<div class="buttons donation_select"> ' . $btns . '</div>';

      $this->add($this->component()->html($text));
    }

    if (!$this->_subject->can_choose_amount) {
      if (count($predefine_list)) {
        $text = '<br>'.$this->view->translate('DONATION_Or choose your own amount');
      } else {
        $text = '<br>'.$this->view->translate('DONATION_Amount');
      }
      $text .= '<br><input id="inputOwn" type="text" size="5"/>';

      $this->add($this->component()->html($text));
    }

    $message = '<br><label for="donation_text">'.$this->view->translate('DONATION_Your message') . '</label><textarea id="donation_text"></textarea>';
    $this->add($this->component()->html($message));

    if(!$viewer->getIdentity()) {
      $details = '<label for="name">'.$this->view->translate('DONATION_Full Name').'</label>
     <input id="name" type="text" value="'.$this->view->translate('DONATION_Anonym').'"/><br/>
     <label for="email">' . $this->view->translate('DONATION_Email').'</label>
     <input id="email" type="text"/>';
      $this->add($this->component()->html($details));
    }

    $checkbox = '<br><input id="anon" type="checkbox">
    <label for="anon" class="anon">'.$this->view->translate('DONATION_I would like to donate anonymously') . '</label>';
    $this->add($this->component()->html($checkbox));

    $submit = '<input data-id="2" id = "submit" class="" type="image" src="'.$this->view->baseUrl() . '/application/modules/Donation/externals/images/buttons/paypal.png' .'">';
    $this->add($this->component()->html($submit));

    $this->addPageInfo('donation', array(
      'min_amount' => $this->_subject->min_amount,
      'object_id' => $this->_subject->getIdentity(),
      'name' => $this->view->translate('DONATION_Anonym'),
      'donate_url' => $this->view->url(array('action' => 'checkout'), 'donation_donate', true)));

    $this->renderContent();
  }

  public function donationCheckoutAction()
  {
    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();

    $values = $this->_getAllParams();
    unset($values['action']);
    unset($values['module']);
    unset($values['controller']);
    unset($values['rewrite']);
    unset($values['format']);

    if(!isset($values['amount'])){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view->translate('DONATION_Please choose amount!');
      return;
    }
    elseif(!filter_var($values['amount'], FILTER_VALIDATE_FLOAT)){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view
        ->translate('DONATION_Please enter amount!');
      return;
    }
    elseif($values['amount']<$values['min_amount']){
      $this->view->status       = 0;
      $this->view->errorMessage = $this->view
        ->translate('DONATION_Please choose amount more than %s',$values['min_amount']);
      return;
    }
    elseif($values['anon'] == 'true' && !$user->getIdentity()){
      if(preg_match("/^[A-Z][a-zA-Z -]+$/", $values['name']) === 0){
        $this->view->status       = 0;
        $this->view->errorMessage = $this->view
          ->translate('DONATION_Please enter a valid name');
        return;
      }
      elseif(!filter_var($values['email'], FILTER_VALIDATE_EMAIL)){
        $this->view->status       = 0;
        $this->view->errorMessage = $this->view
          ->translate('DONATION_Please enter a valid email');
        return;
      }
    }
    if($values['anon'] == 'true' && $user->getIdentity()){
      $values['name'] = $this->view->translate('DONATION_Anonym');
      $values['email'] = $user->email;
    }
    $this->_session->__set('donation_info', $values);
    $this->view->status = 1;
    $this->view->link   = $this->view->url(array('action' => 'process', 'object' => 'donation', 'object_id' => $values['object_id']), 'donation_donate', true);
  }

  public function donationProcessAction()
  {
    $values = $this->_session->__get('donation_info');
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    //Get Gateway
    $gatewayId = $values['gateway_id'];

    /**
     * @var $gateway Payment_Model_Gateway
     */
    $this->view->gateway = $gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId);


    /**
     * @var $donation Donation_Model_Donation
     */

    $donation = Engine_Api::_()->getItem('donation',$values['object_id']);
    // Process


    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();
    if ($values['anon'] == 'true') {
      $user_id = 0;
    }
    else {
      $user_id = (int)$user->getIdentity();
    }
    $ordersTable->insert(array(
      'user_id' => $user_id,
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => $donation->getType(),
      'source_id' => $donation->getIdentity(),
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    if ($gateway->getTitle() == 'PayPal') {
      $params = array(
        'cmd' => '_donations',
        'item_name' => $donation->getTitle(),
        'business' => $donation->getPayPalEmail(),   //PayPal email address
        'notify_url' => $schema . $host
          . $this->view->url(array(),'donation_ipn')
          . '?order_id=' . $order_id
          . '&state=' . 'ipn',
        'return' => $schema . $host
          . $this->view->url(array('action' => 'return'))
          . '?order_id = '.$order_id
          .'&state=' . 'return',
        'cancel_return' => $schema . $host
          . $this->view->url(array('action' => 'return'))
          . '?order_id=' . $order_id
          . '&state=' . 'cancel',
        'rm' => 1,
        'currency_code' => $currency,
        'no_note' => 1,
        'cbt' => $this->view->translate('DONATION_Go Back to The Site'),
        'no_shipping' => 1,
        'bn' => 'PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest',
        'amount' => $values['amount'],
      );
    }


    else{
      $params = array(
        'sid' => 'burya_seller',
        'total' => $values['amount'],
        'tco_currency' => 'USD',
        'id_type' => 1,
        'cart_order_id' => 1,
        'demo' => 'Y',
      );
    }
    // Pull transaction params
    $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $transactionData = $params;

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false)); // todo
    }

    $form = new Engine_Form();
    $form->setAction($transactionUrl);
    $order = 0;
    foreach( $transactionData as $key => $val ) {
      $form->addElement('Hidden', $key, array(
        'value' => $val,
        'order' => $order
      ));
      $order++;
    }

    $this->add($this->component()->html($this->view->translate('DONATION_Please Wait')));

    $this->add($this->component()->form($form));

    $this->renderContent();
  }

  public function donationReturnAction()
  {
    // Get order
    if( //!$this->_page ||
      !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
      !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
      !$gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) {

      return $this->_helper->redirector->gotoRoute(array(), 'default', true); // todo
    }

    // Get gateway plugin
    $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Donation gateway plugin
    $str = str_replace('Payment', 'Donation', get_class($plugin));
    $plugin = new $str( $gateway );

    try{
      $status = $plugin->onCreateTransaction($order,$this->_getAllParams(),$this->_session->__get('donation_info'));
    }
    catch(Exception $e){
      $status = 'failed';
      $this->_session->errorMessage = $e->getMessage();
    }
    return $this->_finishPayment($status);
  }

  public function _finishPayment($state = 'completed')
  {
    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $this->_session->unsetAll();
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state)); // todo
  }

  public function donationFinishAction()
  {
    $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
    $this->_session->unsetAll();

    $form = new Engine_Form();
    $url = 'http://'.$_SERVER['HTTP_HOST'].$this->view->url(array('controller' => 'donors', 'action' => 'index'),'donation_extended',true);
    $form->setAction($url);

    if ($status == 'pending') {
      $form->setTitle($this->view->translate('DONATION_Payment Pending'));
      $form->setDescription($this->view->translate('DONATION_PAYMENT_PENDING_DESCRIPTION'));
    } elseif(in_array($status, array('completed','complete'))) {
      $form->setTitle($this->view->translate('DONATION_Payment Complete'));
      $form->setDescription($this->view->translate('DONATION_PAYMENT_COMPLETED_DESCRIPTION'));
    } else {
      $form->setTitle($this->view->translate('DONATION_Payment Failed'));
      $form->setDescription($this->view->translate('DONATION_PAYMENT_FAILED_DESCRIPTION'));
    }

    $form->addElement('Button', 'submit', array(
      'label' => $this->view->translate('DONATION_Go to Top Donors List'),
      'type' => 'submit'
    ));

    $this->add($this->component()->form($form));

    $this->renderContent();
  }


  public function donorsInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function donorsIndexAction()
  {
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $table = Engine_Api::_()->getItemTable('transaction');
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    $select = $table->select()
      ->from(array('t' => $table->info('name')), array('t.*', new Zend_Db_Expr('SUM(t.amount) AS amounted')))
      ->where('t.user_id IN (?)', $table->select()->from($table->info('name'), array($table->info('name') . '.user_id')))
      ->where('t.user_id > ?', 0)
      ->where('t.state = ?', 'completed')
      ->group('t.user_id')
      ->order(new Zend_Db_Expr('SUM(t.amount) DESC'))
    ;

    $paginator = Zend_Paginator::factory($select);

    $paginator->setItemCountPerPage($settings->getSetting('donation.donors_page_count', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $title = '<h3>' . $this->view->translate("Top Donors List") . '</h3>';
    $this->add($this->component()->html($title));

    if($paginator->getTotalItemCount() > 0 ) {
      $items = array();
      foreach( $paginator as $donation ) {
        $owner = $donation->getOwner();
        $temp = array(
          'title' => $owner->getTitle(),
          'photo' => $owner->getPhotoUrl(),
          'href' => $owner->getHref(),
          'descriptions' => array(
            $this->view->locale()->toCurrency((double)$donation->amounted, $currency) . ' ' . $this->view->translate('donated')
          ),
        );

        $items[] = $temp;
      }

      $paginatorPages = $paginator->getPages();
      $this->add($this->component()->customComponent('itemList', array(
        'listPaginator' => true,
        'pageCount' => $paginatorPages->pageCount,
        'next' => @$paginatorPages->next,
        'paginationParam' => 'page',

        'items' => $items
      )));

//      $this->add($this->component()->paginator($paginator));
    } else {
      $this->add($this->component()->html('<h3>' . $this->view->translate("DONATION_Nobody has donated yet") . '</h3>'));
    }

    $this->renderContent();
  }


  public function charityInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function charityDeleteAction()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getRequest()->getParam('donation_id'));

    $viewer = Engine_Api::_()->user()->getViewer();

    // Make form
    $form = new Donation_Form_Delete();

    if( !$donation || $donation->type != 'charity')
    {
      return $this->_forward('requiresubject', 'error', 'core'); // todo
    }

    $page = $donation->getPage();

    if($page){
      if(!$page->getDonationPrivacy('charity')){
        return $this->_forward('requireauth', 'error', 'core'); // todo
      }
    }
    elseif(!$donation->isOwner($viewer)){
      return $this->_forward('requireauth', 'error', 'core'); // todo
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $donation->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $donation->deleteDonation();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect(
      $this->view->url(array(), 'donation_charity_browse'),
      Zend_Registry::get('Zend_Translate')->_('The selected donation has been deleted.'),
      true
    );
  }


  public function BrowseCharityList(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'title' => $this->view->string()->chunk($this->view->string()->truncate($item->getTitle(), 25), 10),
      'descriptions' => array(
        $this->view->translate('DONATION_Raised:') . ' ' . $this->view->locale()->toCurrency((double)$item->raised_sum, $this->view->currency),
//        '<br><button class="btn btn-small" onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $item->getType(),'object_id' => $item->getIdentity()),'donation_donate',true) . '\')">' . $this->view->translate('DONATION_Donate') . '</button>'
      ),
    );
    return $customize_fields;
  }
  public function BrowseProjectList(Core_Model_Item_Abstract $item)
  {
    $customize_fields = array(
      'title' => $this->view->string()->chunk($this->view->string()->truncate($item->getTitle(), 25), 10),
      'descriptions' => array(),
    );
    $descriptions = $this->view->translate('DONATION_Raised:') . ' ' . $this->view->locale()->toCurrency((double)$item->raised_sum, $this->view->currency) .
      '<br>' . $this->view->translate('DONATION_Target:') . ' ' . $this->view->locale()->toCurrency((double)$item->target_sum, $this->view->currency);
    if (strtotime($item->expiry_date)) {
      $descriptions .= '<br>'.$this->view->translate('DONATION_Limited:');

      $left = Engine_Api::_()->getApi('core', 'donation')->datediff(new DateTime($item->expiry_date), new DateTime(date("Y-m-d H:i:s")));
      $month = (int)$left->format('%m');
      $day = (int)$left->format('%d');
      if($month > 0) {
        $descriptions .= ' ' . $this->view->translate(array("%s month", "%s months", $month), $month);
      }
      $descriptions .= ' ' .  $this->view->translate(array("%s day left", "%s days left", $day), $day);
    }
    $customize_fields['descriptions'][] = $descriptions;
//
//    $customize_fields['descriptions'][] = '<br><button class="btn btn-small" onclick="$.mobile.changePage(\'' . $this->view->url(array('object' => $item->getType(),'object_id' => $item->getIdentity()),'donation_donate',true) . '\')">' . $this->view->translate('DONATION_Donate') . '</button>';

    return $customize_fields;
  }
  public function BrowseFundraiseList(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $parent = $item->getParent();

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        $this->view->translate('DONATION_by ') . ' <a style="z-index: 9999;" onclick="$.mobile.changePage(\'' . $owner->getHref() . '\');"> ' . $owner->getTitle() . '</a> ' .
        $this->view->translate('DONATION_for ') . ' <a style="z-index: 9999;" onclick="$.mobile.changePage(\'' . $parent->getHref() . '\')">' . $parent->getTitle() . '</a>'
      ),
    );

    return $customize_fields;
  }
  public function getDonationApi()
  {
    return Engine_Api::_()->getApi('core', 'donation');
  }
}
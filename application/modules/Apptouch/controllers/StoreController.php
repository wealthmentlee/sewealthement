<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: StoretController.php 15.11.12 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Apptouch_StoreController
    extends Apptouch_Controller_Action_Bridge
{
    /**
     *  Index Controller
     */
    public function indexInit()
    {
        $this->addPageInfo('contentTheme', 'd');
    }

    public function indexIndexAction()
    {
        /**
         * @var $table Store_Model_DbTable_Products
         */
        $table = Engine_Api::_()->getDbtable('products', 'store');
        $prefix = $table->getTablePrefix();

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($table->info('name'))
            ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
            ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
            ->group($prefix . 'store_products.product_id');

        $select = $table->setStoreIntegrity($select);

        if ($this->_getParam('search', false)) {
            $select->where($prefix . 'store_products.title LIKE ? OR ' . $prefix . 'store_products.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $select
            ->where($prefix . 'store_products.quantity <> 0 OR ' . $prefix . 'store_products.type = ?', 'digital')
            ->order($prefix . 'store_products.sponsored DESC')
            ->order($prefix . 'store_products.featured DESC')
            ->order($prefix . 'store_products.creation_date DESC');

        /**
         * @var $settings Core_Model_DbTable_Settings
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $allowFree = $settings->getSetting('store.free.products', 0);
        if (!$allowFree) {
            $select->where($prefix . 'store_products.price > 0');
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));
        $this->setFormat('browse');
        $this->_showCart();
        $this->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($paginator, 'browseProductList'))
            ->renderContent();
    }

    public function indexProductsAction()
    {
        /**
         * @var $table Store_Model_DbTable_Products
         */
        $table = Engine_Api::_()->getDbtable('products', 'store');
        $prefix = $table->getTablePrefix();

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($table->info('name'))
            ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
            ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
            ->group($prefix . 'store_products.product_id');

        $select = $table->setStoreIntegrity($select);

        if ($this->_getParam('search', false)) {
            $select->where($prefix . 'store_products.title LIKE ? OR ' . $prefix . 'store_products.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $select
            ->where($prefix . 'store_products.quantity <> 0 OR ' . $prefix . 'store_products.type = ?', 'digital')
            ->order($prefix . 'store_products.sponsored DESC')
            ->order($prefix . 'store_products.featured DESC');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->setFormat('browse');
        $this->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($paginator, 'browseProductList', array('attrs' => array('class' => 'tile-view'))))
            ->renderContent();
    }

    public function indexStoresAction()
    {
        if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) {
            return;
        }

        $params = array();
        $params['page'] = $this->_getParam('page', 1);
        $params['ipp'] = $this->_getParam('itemCountPerPage', 12);
        $params['sort'] = 'recent';
        if ($this->_getParam('search', false)) {
            $params['keyword'] = $this->_getParam('search');
        }
        $paginator = Engine_Api::_()->getApi('page', 'store')->getPaginator($params);

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->addPageInfo('title', $this->view->translate('STORE_Browse Stores'));

        $this->setFormat('browse');
        $this->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($paginator))
            ->add($this->component()->navigation('store', true))
            ->renderContent();
    }

    public function indexFaqAction()
    {
        $faqs = Engine_Api::_()->getDbTable('faq', 'store')->fetchAll();

        $faq = array();
        foreach ($faqs as $item) {
            $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
            $question = $this->dom()->new_('h3', array(), $item->question);
            $answer = $this->dom()->new_('p', array(), $item->answer);
            $element->append($question);
            $element->append($answer);
            $faq[] = $element;
        }

        $this->addPageInfo('contentTheme', 'd');
        $this->setFormat('browse');
        $this
            ->add($this->component()->html($faq), 20)
            ->renderContent();
    }

    public function indexDownloadAction()
    {
        if (!($id = $this->_getParam('id'))) {
            return $this->fileNotFound();
        }

        $free = $this->_getParam('free', 0);

        /**
         * Declare Variables
         *
         * @var $viewer  User_Model_User
         * @var $item    Store_Model_Orderitem
         * @var $product Store_Model_Product
         * @var $order   Store_Model_Order
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $allowFree = $settings->getSetting('store.free.products', 0);
        $allowPublic = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');

        if (!$viewer->getIdentity() && !$allowPublic) {
            return $this->fileNotFound();
        }

        $isProduct = false;
        if ($free) {
            if ($allowFree) {
                $item = Engine_Api::_()->getItem('store_product', $id);
                $isProduct = true;
            } else {
                return $this->fileNotFound();
            }
        } else {
            $item = Engine_Api::_()->getItem('store_orderitem', $id);
        }

        if (!$item) {
            return $this->fileNotFound();
        }

        if ($isProduct) {
            $storage = $item->getFile();
        } else {
            $product = $item->getItem();
            $order = $item->getParent();
            $storage = $product->getFile();

            if (!$item->isDownloadable() || !$product || !$order || !$order->isOwner($viewer)) {
                return $this->fileNotFound();
            }
        }

        if (!$storage) {
            return $this->fileNotFound();
        }

        if (!($storage instanceof Storage_Model_File)) {
            if ($isProduct) {
                return $this->fileNotFound();
            } else {
                return $this->fileNotFound($order->getIdentity());
            }
        }
        // Process the file
        $file = APPLICATION_PATH . DS . $storage->storage_path;

        if (!is_file($file)) {
            return $this->fileNotFound($order->getIdentity());
        } else {
            try {
                // Disable view and layout rendering
                $this->_helper->layout()->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                // Execute Downloading

                // Set Headers
                header("Pragma: public");
                header("Expires: 0");
                header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header('Content-type: ' . $storage->mime_major . '/' . $storage->mime_minor);
                header('Content-Disposition: attachment; filename="' . $storage->name . '"');
                header('Content-Description: File Transfer');
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: ' . $storage->size);

                // Set Body
                if (readfile($file) /*Engine_Api::_()->store()->readfile_chunked($file)*/) {
                    // Increase download count
                    $item->download_count++;
                    if (!$item->save()) {
                        return $this->fileNotFound($order->order_id);
                    }
                }
            } catch (Exception $e) {
                print_log($e->__toString());
            }
        }
    }

    /**
     *  Index Controller
     */


    /**
     *  Product Controller
     */
    public function productInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        // he@todo this may not work with some of the content stuff in here, double-check
        /**
         * @var $subject Store_Model_Product
         */
        $subject = null;

        if (!Engine_Api::_()->core()->hasSubject('store_product')) {
            $id = $this->_getParam('product_id');

            if (null !== $id) {
                $subject = Engine_Api::_()->getItem('store_product', $id);

                if ($subject && null != ($page = $subject->getStore())) {
                    $approved = $page->approved;
                } else {
                    $approved = 1;
                }

                if ($subject && $approved) {
                    Engine_Api::_()->core()->setSubject($subject);
                } else {
                    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
                    return $this->redirect($this->view->url(array(), 'store_general'));
                }
            }
        }

        $this->_helper->requireSubject('store_product');
    }

    public function productIndexAction()
    {
        /**
         * @var $subject Store_Model_Product
         */
        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        // Increment view count

        if (!$subject->isProductStoreEnabled() || !$subject->getQuantity()) {
            return $this->redirect($this->view->url(array(), 'store_general'));
        }

        if (!$subject->getOwner()->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        $html = '<span>' . $this->view->translate('Posted') . '</span> ';
        $html .= $this->view->timestamp($subject->creation_date) . '<br>';

        if ($subject->hasStore()) {
            $html .= $this->view->translate('in %s store', $this->view->htmlLink($subject->getStore()->getHref(), $subject->getStore()->getTitle(), array())) . '<br>';
        }

        if (null != ($cat = $subject->getCategory())) {
            $html .= '<span>' . $this->view->translate($cat->label) . ': </span> ' . ((null !== ($category = $cat->category)) ? $this->view->htmlLink($subject->getCategoryHref(array('action' => 'products')), $this->view->translate($category), array()) : ("<i>" . $this->view->translate("Uncategorized") . "</i>")) . '<br>';
        }

        $html .= $subject->getInfo() . '<br>';
        $html .= $subject->getTaxInfo() . '<br>';
        $html .= $this->view->translate('Price') . ' : ' . $this->view->getPrice($subject);

        $allowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');

        $btn = '';
        if ($allowOrder) {
            if (!$subject->isAddedToCart()) {
                $href = $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'add', 'product_id' => $subject->getIdentity()), 'default', true);
                $btn .= '<a href="' . $href . '" data-role="button" data-rel="dialog">' . $this->view->translate('STORE_Add to Cart') . '</a>';
            }

            if (!$subject->isWished()) {
                $btn .= '<a href="' . $this->view->url(array('module' => 'store', 'controller' => 'product', 'action' => 'wish', 'product_id' => $subject->getIdentity(), 'do' => 'add'), 'default', true) . '" data-role="button">' . $this->view->translate('STORE_Add to Wishlist') . '</a>';
            } else {
                $btn .= '<a href="' . $this->view->url(array('module' => 'store', 'controller' => 'product', 'action' => 'wish', 'product_id' => $subject->getIdentity(), 'do' => 'remove'), 'default', true) . '" data-role="button">' . $this->view->translate('STORE_In Wishlist') . '</a>';
            }
        } else {
            if (!$viewer->getIdentity()) {
                $btn .= $this->view->translate("You need to login to add the product to your cart.");
            } else {
                $btn .= $this->view->translate("You do not have a permission to order products");
            }
        }

        $html .= '<br>' . $btn . '<br>' . $subject->description;
        $quick = $this->getNavigation(array('prod_profile'), array());
        $this->addPageInfo('contentTheme', 'd')
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($quick))
            ->add($this->component()->subjectPhoto(), 0)
            ->add($this->component()->rate(array('subject' => $this->view->subject())), 1)
//      ->add($this->component()->like(array('subject' => $this->view->subject())), 2) // Like plugin is not integrated with product profile
            ->add($this->component()->html($html), 3)
            ->add($this->component()->tabs(array('ignorePermissions' => 1)), 4);
        $this->renderContent();
    }

    public function productDeleteAction()
    {
        $product_id = $this->_getParam('product_id');

        if (null == ($product = Engine_Api::_()->getItem('store_product', $product_id))) {
            return false;
        };
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity() != $product->owner_id)
            return;

        $form = new Store_Form_Product_Delete();
        $form->getElement('product_id')->setValue($product_id);

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

        $productTable = Engine_Api::_()->getDbTable('products', 'store');
        $db = $productTable->getAdapter();
        $db->beginTransaction();

        try {

            $product->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected product has been deleted.');

        return $this->redirect($this->view->url(array(), '', true));
    }

    public function productWishAction()
    {
        $do = $this->_getParam('do', 'add');
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!in_array($do, array('add', 'remove')) || !$viewer->getIdentity()) {
            return $this->redirect('parentRefresh');
        }

        /**
         * @var $table   Store_Model_DbTable_Wishes
         * @var $product Store_Model_Product
         */

        $product = Engine_Api::_()->core()->getSubject();
        $table = Engine_Api::_()->getDbTable('wishes', 'store');
        if ($product->isWished() && $do == 'remove') {
            $select = $table->select()
                ->where('user_id = ?', $viewer->getIdentity())
                ->where('product_id = ?', $product->getIdentity())
                ->limit(1);
            $row = $table->fetchRow($select);
            $row->delete();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product successfully removed from Wishlist.');
        } elseif (!$product->isWished() && $do == 'add') {
            $row = $table->createRow();
            $row->product_id = $product->getIdentity();
            $row->user_id = $viewer->getIdentity();
            $row->save();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product successfully added to Wishlist.');
        }

        return $this->refresh();
    }

    /**
     *  Product Controller
     */

    public function tabPhotos($active = false)
    {
        $product = Engine_Api::_()->core()->getSubject('store_product');
        $paginator = $product->getCollectiblesPaginator();
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));

        return $paginator;
    }

    public function tabAudios($active = null)
    {
        /**
         * @var $audiosTbl Store_Model_DbTable_Audios
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');
        $audiosTbl = Engine_Api::_()->getDbTable('audios', 'store');
        $select = $audiosTbl->select()
            ->where('product_id = ?', $product->getIdentity());

        $paginator = Zend_Paginator::factory($select);

        if ($active) {
            $this->add($this->component()->html('<br>'), 9)
                ->add($this->component()->playlist($paginator), 10)
                ->add($this->component()->mediaControls(), 15);
        }
        return array(
            'showContent' => false,
            'response' => $paginator
        );
    }

    public function tabVideo($active = null)
    {
        if (!Engine_Api::_()->core()->hasSubject('store_product')) return false;

        /**
         * @var $product Store_Model_Product
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');
        $video = $product->getVideo();

        if ($video == null || !$video->status) {
            return false;
        }

        if ($active) {
            $this->add($this->component()->video($video), 10);
        }

        return true;
    }

    /**
     *  Products Controller
     */

    public function productsInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->page_id = $page->getIdentity();
        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !$page->getStorePrivacy()
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            $this->redirect($page->getHref());
        }

        $this->params = array('page_id' => $page->getIdentity(),
            'ipp' => 5,
            'p' => $this->_getParam('p', 1),
            'order' => 'DESC');

        $this->products_navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
        $this->products_quickLinks = $this->getNavigation(array('create'), array('page_id' => $page->getIdentity()));

        $this->addPageInfo('contentTheme', 'd');
    }

    public function productsIndexAction()
    {
        $this->params['owner'] = true;
        $this->params['order'] = 'DESC';
        $table = Engine_Api::_()->getDbTable('products', 'store');
        $prefix = $table->getTablePrefix();
        $select = $table->getSelect($this->params);
        if ($this->_getParam('search', false)) {
            $select->where($prefix . 'store_products.title LIKE ? OR ' . $prefix . 'store_products.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $products = Zend_Paginator::factory($select);
        $products->setItemCountPerPage(5);
        $products->setCurrentPageNumber($this->_getParam('p', 1));

        $navigation = $this->getNavigation(array('transactions'), array('page_id' => $this->pageObject->getIdentity()));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->add($this->component()->navigation($navigation))
            ->add($this->component()->subjectPhoto($this->pageObject))
            ->add($this->component()->html($this->view->translate('STORE_PAGE_PRODUCTS_DESCRIPTION')))
            ->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($products, 'productsManageList'))
            ->add($this->component()->quickLinks($this->products_quickLinks))
            ->renderContent();
    }

    public function productsEditAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

        if (!$product->isOwner($viewer) && !$this->pageObject->isOwner($viewer)) {
            return $this->redirect($this->view->url(array('page_id' => $this->pageObject->getIdentity()), 'store_products', true));
        }

        $form = new Store_Form_Page_Products_Edit(array('item' => $product));
        $form->removeElement('file');
        $form->getElement('additional_params')
            ->clearDecorators()
            ->addDecorator('FormAdditionalParams')
            ->addDecorator('viewScript', array(
                'viewScript' => '_StoreAdditionalParams.tpl',
                'placement' => '',
            ));
        //$form->getElement('discount_expiry_date')->setAllowEmpty(false);
        $form->removeAttrib('style');

        $product_array = $product->toArray();
        $product_array['description'] = strip_tags($product_array['description']);

        // Populate form
        $form->populate(array_merge($product_array, array('additional_params' => $product->params)));
        $tagStr = '';
        foreach ($product->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap->getTag();
            if (!isset($tag->text)) {
                continue;
            }
            if ('' !== $tagStr) {
                $tagStr .= ', ';
            }
            $tagStr .= $tag->text;
        }
        $form->populate(array(
            'tags' => $tagStr,
        ));

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        foreach ($roles as $role) {
            if ($form->auth_comment) {
                if ($auth->isAllowed($product, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
            }
        }

        $manage_menus = $this->getNavigation(array('manage'), array('page_id' => $product->page_id, 'product_id' => $product->getIdentity()));

        // Check post/form
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->quickLinks($manage_menus))
                ->add($this->component()->navigation($this->products_navigation))
                ->renderContent();
            return;
        }

        $values = $this->getRequest()->getPost();
        $values['type'] = $product->type;

        if (!$form->isValid($values)) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->quickLinks($manage_menus))
                ->add($this->component()->navigation($this->products_navigation))
                ->renderContent();
            return;
        }

        // Process
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            $values['params'] = $values['additional_params'];
            $values['description'] = strip_tags($values['description']);

            // Convert times
            if ($values['discount_expiry_date'] != '0000-00-00') {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($oldTz);
                $discount_expiry_date = strtotime($values['discount_expiry_date']);
                $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
            } else {
                unset($values['discount_expiry_date']);
            }

            if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
                if (isset($values['via_credits'])) {
                    unset($values['via_credits']);
                }
            }

            $product->setFromArray($values);
            $product->modified_date = date('Y-m-d H:i:s');

            $product->save();

            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($product);
            $customfieldform->saveValues();
            $customfieldform->removeElement('submit');

            if (empty($values['auth_comment'])) {
                $values['auth_comment'] = 'everyone';
            }

            $commentMax = array_search($values['auth_comment'], $roles);

            foreach ($roles as $i => $role) {
                $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
            }

            // handle tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $product->tags()->setTagMaps($viewer, $tags);

            // insert new activity if blog is just getting published
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($product);
            if (count($action->toArray()) <= 0) {
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new', null, array('is_mobile' => true));
                // make sure action exists before attaching the blog to the activity
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
                }
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($product) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->saveData($product);

            $db->commit();

            $mess = $this->view->translate('STORE_All changes have been successfully saved');
            $form->addNotice($mess);

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->add($this->component()->subjectPhoto($product))
            ->add($this->component()->form($form))
            ->add($this->component()->quickLinks($manage_menus))
            ->add($this->component()->navigation($this->products_navigation))
            ->renderContent();
    }

    public function productsDeleteAction()
    {
        $product_id = $this->_getParam('product_id', 0);
        $product = Engine_Api::_()->getItem('store_product', $product_id);
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $product->getPage();
        $form = new Store_Form_Page_Products_Delete();

        if (!$product) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Product doesn't exists or not authorized to delete");
            return;
        }

        if (!$product->isOwner($viewer) && !$page->isOwner($viewer)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Product doesn't exists or not authorized to delete");
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

        $db = $product->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->deleteData($product);
            $product->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product has been deleted.');

        return $this->redirect($this->view->url(array('page_id' => $page->page_id), 'store_products', true));
    }

    public function productsCopyAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $copied_product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));
        $page_id = $this->_getParam('page_id');
        if ($copied_product === null) {
            return $this->redirect($this->view->url(array('page_id' => $page_id), 'store_products', true));
        }

        if (!$copied_product->isOwner($viewer) && !$this->view->page->isOwner($viewer)) {
            return $this->redirect($this->view->url(array('page_id' => $page_id), 'store_products', true));
        }

        $this->_checkRequiredSettings();

        $form = new Store_Form_Page_Products_Copy(array('item' => $copied_product));
        $form->removeElement('file');
        $form->addElement('File', 'file', array(
            'label' => 'Add Photo',
            'order' => 9,
            'isArray' => true
        ));
        $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $form->getElement('additional_params')
            ->clearDecorators()
            ->addDecorator('FormAdditionalParams')
            ->addDecorator('viewScript', array(
                'viewScript' => '_StoreAdditionalParams.tpl',
                'placement' => '',
            ));
        $form->removeAttrib('style');
        $form->getDecorator('description')->setOption('escape', false);

        // Populate form
        $form->populate(array_merge($copied_product->toArray(), array('additional_params' => $copied_product->params)));
        $tagStr = '';
        foreach ($copied_product->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap->getTag();
            if (!isset($tag->text)) {
                continue;
            }
            if ('' !== $tagStr) {
                $tagStr .= ', ';
            }
            $tagStr .= $tag->text;
        }
        $form->populate(array(
            'tags' => $tagStr,
        ));

        //$this->view->tagNamePrepared = $tagStr;

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        foreach ($roles as $role) {
            if ($form->auth_comment) {
                if ($auth->isAllowed($copied_product, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
            }
        }

        // If not post or form not valid, return
        if (!$this->getRequest()->isPost()) {
            if (!$this->gatewaysEnabled) {
                $this->add($this->component()->html(
                    $this->view->translate('There are currently no ' .
                        'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('action' => 'gateway', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '">', '</a>')
                ));
            }

            if (!$this->hasShippingLocations) {
                $this->add($this->component()->html(
                    $this->view->translate('Store needs a default list of supported shipping locations for your tangible products, ' .
                        'customers from not-supported locations are not able to make a purchase. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('controller' => 'locations', 'action' => 'add', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '" class="smoothbox">', '</a>')
                ));
            }

            if (!$this->error) {
                $this->add($this->component()->subjectPhoto($this->pageObject))
                    ->add($this->component()->form($form))
                    ->add($this->component()->quickLinks($this->products_quickLinks))
                    ->add($this->component()->navigation($this->products_navigation));
            }

            $this->renderContent();
            return;
        }

        $values = $this->getRequest()->getPost();
        $values['type'] = $copied_product->type;

        if (!$form->isValid($values)) {
            if (!$this->gatewaysEnabled) {
                $this->add($this->component()->html(
                    $this->view->translate('There are currently no ' .
                        'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('action' => 'gateway', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '">', '</a>')
                ));
            }

            if (!$this->hasShippingLocations) {
                $this->add($this->component()->html(
                    $this->view->translate('Store needs a default list of supported shipping locations for your tangible products, ' .
                        'customers from not-supported locations are not able to make a purchase. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('controller' => 'locations', 'action' => 'add', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '" class="smoothbox">', '</a>')
                ));
            }

            if (!$this->error) {
                $this->add($this->component()->subjectPhoto($this->pageObject))
                    ->add($this->component()->form($form))
                    ->add($this->component()->quickLinks($this->products_quickLinks))
                    ->add($this->component()->navigation($this->products_navigation));
            }

            $this->renderContent();
            return;
        }

        $table = Engine_Api::_()->getDbtable('products', 'store');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create product
            $values = array_merge($form->getValues(), array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            ));
            $values['params'] = $values['additional_params'];

            // Convert times
            if ($values['discount_expiry_date'] != '0000-00-00') {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($oldTz);
                $discount_expiry_date = strtotime($values['discount_expiry_date']);
                $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
            } else {
                unset($values['discount_expiry_date']);
            }

            if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
                if (isset($values['via_credits'])) {
                    unset($values['via_credits']);
                }
            }

            /**
             * @var $product Store_Model_Product
             */
            unset($values['product_id']);

            $product = $table->createRow();
            $product->setFromArray($values);

            if ($product->save()) {
                $product->createAlbum($values);

                if (!$product->isDigital()) {
                    $product->createLocations();
                }

                // Auth
                if (empty($values['auth_view'])) {
                    $values['auth_view'] = 'everyone';
                }

                if (empty($values['auth_comment'])) {
                    $values['auth_comment'] = 'everyone';
                }

                $commentMax = array_search($values['auth_comment'], $roles);

                foreach ($roles as $i => $role) {
                    $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
                }

                // Save Photos
                $picupFiles = $this->getPicupFiles('file');
                if (empty($picupFiles))
                    $photos = $form->file->getFileName();
                else
                    $photos = $picupFiles;

                $photoTable = Engine_Api::_()->getDbtable('photos', 'store');
                if (is_array($photos)) {
                    foreach ($photos as $photoPath) {
                        $photo = $photoTable->createRow();
                        $photo->setFromArray(array(
                            'user_id' => $viewer->getIdentity()
                        ));
                        $photo->save();

                        $photo->setPhoto($photoPath);
                        $photo->collection_id = $product->getIdentity();
                        $photo->save();
                        if (!$product->photo_id) {
                            $product->photo_id = $photo->photo_id;
                        }
                    }
                } else {
                    $photo = $photoTable->createRow();
                    $photo->setFromArray(array(
                        'user_id' => $viewer->getIdentity()
                    ));
                    $photo->save();

                    $photo->setPhoto($photos);
                    $photo->collection_id = $product->getIdentity();
                    $photo->save();
                    $product->photo_id = $photo->photo_id;
                }

                $product->save();

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new', null, array('is_mobile' => true));

                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
                }
            }


            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));
            $product->tags()->addTagMaps($viewer, $tags);

            // Add fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($product);
            $customfieldform->saveValues();
            $customfieldform->removeElement('submit');

            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->saveData($product);

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        if ($product->isDigital()) {
            $this->redirect($this->view->url(array(
                        'controller' => 'digital',
                        'action' => 'edit-file',
                        'product_id' => $product->getIdentity()
                    ), 'store_extended', true
                )
            );
        }
        //$this->redirect($this->view->url(array('product_id' => $product->getIdentity()), 'store_product_locations', true));
        $this->redirect($this->view->url(array(), 'store_general', true));
    }

    public function productsCreateAction()
    {
        $page_id = $this->pageObject->getIdentity();
        $this->_checkRequiredSettings();
        $form = new Store_Form_Page_Products_Create();
        $form->removeElement('file');
        $form->addElement('File', 'file', array(
            'label' => 'Add Photo',
            'order' => 9,
            'isArray' => true
        ));


        $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $form->getElement('additional_params')
            ->clearDecorators()
            ->addDecorator('FormAdditionalParams')
            ->addDecorator('viewScript', array(
                'viewScript' => '_StoreAdditionalParams.tpl',
                'placement' => '',
            ));
        $form->removeAttrib('style');
        $form->getDecorator('description')->setOption('escape', false);
        $viewer = Engine_Api::_()->user()->getViewer();
        $form->populate(array('page_id' => $page_id));

        // If not post or form not valid, return
        if (!$this->getRequest()->isPost()) {
            if (!$this->gatewaysEnabled) {
                $this->add($this->component()->html($this->view->translate('There are currently no ' .
                    'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                    'page is available.', '<a href="' .
                    $this->view->escape($this->view->url(array('action' => 'gateway', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                    '">', '</a>')));
            }

            if (!$this->hasShippingLocations) {
                $this->add($this->component()->html(
                    $this->view->translate('Store needs a default list of supported shipping locations for your tangible products, ' .
                        'customers from not-supported locations are not able to make a purchase. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('controller' => 'locations', 'action' => 'add', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '" class="smoothbox">', '</a>')
                ));
            }

            if (!$this->error) {
                $this->add($this->component()->subjectPhoto($this->pageObject))
                    ->add($this->component()->form($form))
                    ->add($this->component()->quickLinks($this->products_quickLinks))
                    ->add($this->component()->navigation($this->products_navigation));
            }

            $this->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {

            if (!$this->gatewaysEnabled) {
                $this->add($this->component()->html($this->view->translate('There are currently no ' .
                    'enabled payment gateways. You must %1$sadd one%2$s before this ' .
                    'page is available.', '<a href="' .
                    $this->view->escape($this->view->url(array('action' => 'gateway', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                    '">', '</a>')));
            }

            if (!$this->hasShippingLocations) {
                $this->add($this->component()->html(
                    $this->view->translate('Store needs a default list of supported shipping locations for your tangible products, ' .
                        'customers from not-supported locations are not able to make a purchase. You must %1$sadd one%2$s before this ' .
                        'page is available.', '<a href="' .
                        $this->view->escape($this->view->url(array('controller' => 'locations', 'action' => 'add', 'page_id' => $this->view->subject()->getIdentity()), 'store_settings', true)) .
                        '" class="smoothbox">', '</a>')
                ));
            }

            if (!$this->error) {
                $this->add($this->component()->subjectPhoto($this->pageObject))
                    ->add($this->component()->form($form))
                    ->add($this->component()->quickLinks($this->products_quickLinks))
                    ->add($this->component()->navigation($this->products_navigation));
            }

            $this->renderContent();
            return;
        }

        $table = Engine_Api::_()->getDbTable('products', 'store');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Create product
            $values = array_merge($form->getValues(), array(
                'owner_type' => $viewer->getType(),
                'owner_id' => $viewer->getIdentity(),
            ));
            $values['params'] = $values['additional_params'];

            // Convert times
            if ($values['discount_expiry_date'] != '0000-00-00') {
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($oldTz);
                $discount_expiry_date = strtotime($values['discount_expiry_date']);
                $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
            } else {
                unset($values['discount_expiry_date']);
            }

            if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
                if (isset($values['via_credits'])) {
                    unset($values['via_credits']);
                }
            }

            /**
             * @var $product Store_Model_Product
             */
            $product = $table->createRow();
            $product->setFromArray($values);

            if ($product->save()) {
                $product->createAlbum($values);
                if (!$product->isDigital()) {
                    $product->createLocations();
                }

                // Auth
                $auth = Engine_Api::_()->authorization()->context;

                $auth->setAllowed($product, 'everyone', 'comment', 1);
                $auth->setAllowed($product, 'everyone', 'order', 1);
                $auth->setAllowed($product, 'everyone', 'view', 1);

                // Save Photos
                $picupFiles = $this->getPicupFiles('file');
                if (empty($picupFiles))
                    $photos = $form->file->getFileName();
                else
                    $photos = $picupFiles;

                $photoTable = Engine_Api::_()->getDbtable('photos', 'store');
                if (is_array($photos)) {
                    foreach ($photos as $photoPath) {
                        $photo = $photoTable->createRow();
                        $photo->setFromArray(array(
                            'user_id' => $viewer->getIdentity()
                        ));
                        $photo->save();

                        $photo->setPhoto($photoPath);
                        $photo->collection_id = $product->getIdentity();
                        $photo->save();
                        if (!$product->photo_id) {
                            $product->photo_id = $photo->photo_id;
                        }
                    }
                } else {
                    $photo = $photoTable->createRow();
                    $photo->setFromArray(array(
                        'user_id' => $viewer->getIdentity()
                    ));
                    $photo->save();

                    $photo->setPhoto($photos);
                    $photo->collection_id = $product->getIdentity();
                    $photo->save();
                    $product->photo_id = $photo->photo_id;
                }

                $product->save();

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new', null, array('is_mobile' => true));

                if ($action) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
                }

                $search_api = Engine_Api::_()->getDbTable('search', 'page');
                $search_api->saveData($product);
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $tags = array_filter(array_map("trim", $tags));
            $product->tags()->addTagMaps($viewer, $tags);

            // Add fields
            $customfieldform = $form->getSubForm('fields');
            $customfieldform->setItem($product);
            $customfieldform->saveValues();
            $customfieldform->removeElement('submit');

            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        if ($product->isDigital()) {
            $this->redirect($this->view->url(array('controller' => 'digital', 'action' => 'edit-file', 'product_id' => $product->getIdentity()), 'store_extended', true));
        }
        //$this->redirect($this->view->url(array('product_id' => $product->getIdentity()), 'store_product_locations', true));
        $this->redirect($this->view->url(array(), 'store_general', true));
    }

    /**
     *  Products Controller
     */

    /**
     *  Digita Controller
     */
    public function digitalInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        /**
         * @var $product Store_Model_Product
         */
        if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
            Engine_Api::_()->core()->setSubject($product);
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        $this->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->pageObject = $page = $product->getStore();
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
//      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
            !($page->getStorePrivacy() || $product->isOwner($viewer))
        ) {
            return $this->redirect($page->getHref());
        }

        $this->digital_navigation = $this->getNavigation(array('products'), array('page_id' => $page->page_id));

        if (!$product->hasFile() && $this->_getParam('action') == 'edit-file') {
            return $this->redirect($this->view->url(array('controller' => 'digital', 'action' => 'create-file', 'product_id' => $product->getIdentity()), 'store_extended', true));
        } elseif ($product->hasFile() && $this->_getParam('action') == 'create-file') {
            return $this->redirect($this->view->url(array('controller' => 'digital', 'action' => 'edit-file', 'product_id' => $product->getIdentity()), 'store_extended', true));
        }
    }

    public function digitalCreateFileAction()
    {
        $product = $this->product;
        $form = new Store_Form_Admin_Digital_Create();
        $form->removeElement('file');
        $form->addElement('File', 'file', array(
            'label' => 'Add File',
            'order' => 0
        ));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->digital_navigation))
                ->renderContent();
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->digital_navigation))
                ->renderContent();
            return;
        }

        $values = $form->getValues();
        $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
        $db->beginTransaction();
        try {
            $files = $this->getPicupFiles('file');
            if (!empty($values['file'])) {
                $storage = Engine_Api::_()->getItemTable('storage_file');

                $row = $storage->createRow();
                $row->setFromArray(array(
                    'parent_type' => 'store_product',
                    'parent_id' => $product->getIdentity(),
                    'user_id' => $this->viewer->getIdentity(),
                ));

                $row->store($form->file->getFileName());
            } else if (!empty($files)) {
                $file = $files[0];
                $storage = Engine_Api::_()->getItemTable('storage_file');

                $row = $storage->createRow();
                $row->setFromArray(array(
                    'parent_type' => 'store_product',
                    'parent_id' => $product->getIdentity(),
                    'user_id' => $this->viewer->getIdentity(),
                ));

                $row->store($file);
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        $this->redirect(
            $this->view->url(
                array(
                    'controller' => 'digital',
                    'action' => 'edit-file',
                    'product_id' => $product->getIdentity()
                ), 'store_extended', true
            )
        );
    }

    public function digitalEditFileAction()
    {
        $product = $this->product;
        $page = $product->getPage();
        $file = $product->getFile();

        $ul = $this->dom()->new_('ul', array('data-role' => "listview"), '', array(
            $this->dom()->new_('li', array(), '', array(
                $this->dom()->new_('a', array(), '', array(
                    $this->dom()->new_('h3', array(), $file->name),
                )),
                $this->dom()->new_('a', array('href' => $this->view->url(array('controller' => 'digital', 'action' => 'delete-file', 'product_id' => $this->product->getIdentity()), 'store_extended', true), 'data-rel' => 'dialog', 'data-icon' => 'delete')),
            ))
        ));

        $back_menu = $this->getNavigation(array('back'), array('product_id' => $product->getIdentity(), 'page_id' => $page->getIdentity()));

        $this->add($this->component()->subjectPhoto($product))
            ->add($this->component()->html($ul . ''))
            ->add($this->component()->quickLinks($back_menu))
            ->add($this->component()->navigation($this->digital_navigation))
            ->renderContent();
    }

    public function digitalDeleteFileAction()
    {
        $form = new Store_Form_Admin_Digital_Delete();
        $file = $this->product->getFile();

        if (!$file) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("File doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation($this->digital_navigation))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation($this->digital_navigation))
                ->renderContent();
            return;
        }

        if ($file) {
            $table = Engine_Api::_()->getDbTable('products', 'store');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getApi('core', 'store')->deleteFile($file->file_id);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->view->message = Zend_Registry::get('Zend_Translate')->_('File has been deleted.');
            return $this->redirect($this->view->url(array('controller' => 'digital', 'action' => 'create-file', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
        }
    }

    /**
     *  Digita Controller
     */

    /**
     *  Panel Controller
     */
    var $isPublic = false;

    public function panelInit()
    {
        $this->navigation = $this->getNavigation(array('panel'));
        $this->addPageInfo('contentTheme', 'd');

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->isPublic = $this->_isPublic = !((boolean)$viewer->getIdentity());

        $action = $this->getRequest()->getParam('action');
        if ($action != 'address' && $action != 'purchases' && $action != 'purchase' && $this->_isPublic) {
            return $this->redirect($this->view->url(array(), 'store_general'));
        }
    }

    public function panelIndexAction()
    {
        if (
            !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ||
            !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()
        ) {
            return $this->redirect($this->view->url(array('action' => 'purchases'), 'store_panel'));
        }

        /**
         * @var $viewer User_Model_User
         * @var $api    Store_Api_Page
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $api = Engine_Api::_()->getApi('page', 'store');

        $membershipTbl = Engine_Api::_()->getDbTable('membership', 'page');
        $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
        $name = $pagesTbl->info('name');
        $m_name = $membershipTbl->info('name');
        $select = $pagesTbl->select()
            ->setIntegrityCheck(false)
            ->from($name, array($name . '.*'))
            ->joinLeft($m_name, $name . '.page_id = ' . $m_name . '.resource_id', array())
            ->where($m_name . '.user_id = ?', $viewer->getIdentity())
            ->order($name . '.creation_date DESC');

        if ($this->_getParam('search', false)) {
            $select->where($name . '.title LIKE ? OR ' . $name . '.description LIKE ? OR ' . $name . '.keywords LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        $select = $api->setStoreIntegrity($select, false);

        $paginator = Zend_Paginator::factory($select);
        $ipp = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.browse_count', 10);
        $paginator->setItemCountPerPage($ipp);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        /**
         * @var $productsTbl Store_Model_DbTable_Products
         */

        $productsTbl = Engine_Api::_()->getDbTable('products', 'store');

        // Preload info
        $products = array();

        foreach ($paginator as $store) {
            $count = $productsTbl->getProducts(array('count' => 1,
                'page_id' => $store->page_id,
                'quantity' => true));
            $products['page_id'] = $store->page_id;
            $products['count'] = ($count) ? $count : 0;
        }

        /**
         * @var $pageTbl Page_Model_DbTable_Pages
         */

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($paginator, 'panelStoreList'))
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($this->navigation))
            ->renderContent();
    }

    public function panelPurchaseAction()
    {
        $session = new Zend_Session_Namespace('Store_Transaction');
        $session->unsetAll();

        $order_id = $this->_getParam('order_id', 0);
        $token = $this->_getParam('token', null);
        /**
         * @var  $viewer   User_Model_User
         * @var  $table    Store_Model_DbTable_Orderitems
         * @@var $ordersTb Store_Model_DbTable_Orders
         * @@var $order    Store_Model_Order
         *
         */
        $ordersTb = Engine_Api::_()->getDbTable('orders', 'store');

        if (is_integer($order_id) && $order_id) {
            $order = $ordersTb->findRow($order_id);
        } elseif (is_string($order_id) && strlen($order_id) >= 10) {
            $order = $ordersTb->getOrderByUkey($order_id);
        } else if (!$order && $token) {
            $order = $ordersTb->getOrderByToken($token);
        }

        if (isset($order) && $order->status != 'initial') {
            Engine_Api::_()->core()->setSubject($order);
        }

        // Set up require
        if (!$this->_helper->requireSubject('store_order')->isValid()) {
            return null;
        }

        //Order items
        $order = Engine_Api::_()->core()->getSubject('store_order');
        $table = Engine_Api::_()->getDbTable('orderitems', 'store');
        $select = $table->select()
            ->where('status != ?', 'initial')
            ->where('order_id = ?', $order->getIdentity())
            ->order('orderitem_id DESC');

        $items = Zend_Paginator::factory($select);
        $items->setItemCountPerPage(20);
        $items->setCurrentPageNumber(1);


        //Shipping Details
        if (isset($order->shipping_details) &&
            isset($order->shipping_details['location_id_1']) &&
            null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
        ) {
            if (isset($order->shipping_details['location_id_2']) &&
                null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
            ) {
//        $this->view->state = $state;
            }
        }

        $thisdownloadCount = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('store.download.count', 10);
        $api = Engine_Api::_()->store();
        $gateway = Engine_Api::_()->getItem('store_gateway', (int)$order->gateway_id);

        $values = array();
        $values[] = array(
            'title' => $this->view->translate('STORE_Order Details'),
            'content' => array(
                array(
                    'label' => $this->view->translate('Order Key'),
                    'value' => $order->ukey
                ),
                array(
                    'label' => $this->view->translate('Status'),
                    'value' => ucfirst($order->status)
                ),
            )
        );

        $gateway_title = ($gateway) ? $gateway->getTitle() : $this->view->translate('Unknown Gateway');

        $values[] = array(
            'title' => $this->view->translate('STORE_Payment Details'),
            'content' => array(
                array(
                    'label' => $this->view->translate('Gateway'),
                    'value' => $gateway_title
                ),
                array(
                    'label' => $this->view->translate('Payment Date'),
                    'value' => $order->payment_date
                ),
                array(
                    'label' => $this->view->translate('Item Amount'),
                    'value' => ($order->via_credits) ? $api->getCredits($order->item_amt) : $this->view->locale()->toCurrency($order->item_amt, $order->currency)
                ),
                array(
                    'label' => $this->view->translate('Tax Amount'),
                    'value' => ($order->via_credits) ? $api->getCredits($order->tax_amt) : $this->view->locale()->toCurrency($order->tax_amt, $order->currency)
                ),
                array(
                    'label' => $this->view->translate('Shipping Amount'),
                    'value' => ($order->via_credits) ? $api->getCredits($order->shipping_amt) : $this->view->locale()->toCurrency($order->shipping_amt, $order->currency)
                ),
                array(
                    'label' => $this->view->translate('Total Amount'),
                    'value' => ($order->via_credits) ? $api->getCredits($order->total_amt) : $this->view->locale()->toCurrency($order->total_amt, $order->currency)
                )
            )
        );
        if ($country) {
            $values[] = array(
                'title' => $this->view->translate('Shipping Details'),
                'content' => array(
                    array(
                        'label' => $this->view->translate('Country'),
                        'value' => ($country) ? $country->location : ''
                    ),
                    array(
                        'label' => $this->view->translate('STORE_State/Region'),
                        'value' => ($state) ? $state->location : ''
                    ),
                    array(
                        'label' => $this->view->translate('City'),
                        'value' => $order->shipping_details['city']
                    ),
                    array(
                        'label' => $this->view->translate('Zip'),
                        'value' => $order->shipping_details['zip']
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Address Line'),
                        'value' => $order->shipping_details['address_line_1']
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Address Line 2'),
                        'value' => ($order->shipping_details['address_line_2']) ? $order->shipping_details['address_line_2'] : ''
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Phone'),
                        'value' => $order->shipping_details['phone_extension'] + '-' + $order->shipping_details['phone']
                    ),
                )
            );
        }

        $this->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->itemList($items, 'panelProductList'))
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($this->navigation))
            ->renderContent();
    }

    public function panelPurchasesAction()
    {
        /**
         * @var $viewer   User_Model_User
         * @var $table    Store_Model_DbTable_Orders
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable('orders', 'store');

        $select = $table
            ->select()
            ->where('status != ?', 'initial')
            ->limit(30);

        $values = array();

        $token = Engine_Api::_()->store()->getToken();
        if (!$viewer->getIdentity() && $token) {
            $select->where('token = ?', $token);
        } else {
            $select->where('user_id = ?', $viewer->getIdentity());
        }


//    $this->view->filterForm = $filterForm = new Store_Form_Panel_PurchaseFilter();
//    $filterForm
//      ->addDecorator('FormElements')
//      ->addDecorator('Form')
//      ->setAttribs(array(
//      'id'    => 'search_form',
//      'class' => 'store_filter_form inner',
//    ))
//      ->setAction($this->view->url(array('action'=>'purchases'), 'store_panel', true))
//    ;
//
//    if ($filterForm->isValid($this->_getAllParams())) {
//      $values = $filterForm->getValues();
//    } else {
//      $values = array();
//    }

        foreach ($values as $key => $value) {
            if (null == $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'order_id',
            'direction' => 'DESC',
        ), $values);

//    $this->view->filterValues = $values;

        $select->order($values['order'] . ' ' . $values['direction']);

        if (!empty($values['status'])) {
            $select
                ->where('status = ?', $values['status']);
        }

        if (!empty($values['ukey'])) {
            $select
                ->where('ukey LIKE ?', '%' . $values['ukey'] . '%');
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $values = array();

        $api = Engine_Api::_()->store();
        foreach ($paginator as $order) {
            $gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id);
            $gateway_title = ($gateway) ? $gateway->getTitle() : $this->view->translate('Unknown Gateway');

            $values[] = array(
                'title' => $this->view->translate('ID') . ' ' . $order->order_id,
                'content' => array(
                    array(
                        'label' => $this->view->translate('Order Key'),
                        'value' => $order->ukey
                    ),
                    array(
                        'label' => $this->view->translate('Status'),
                        'value' => ucfirst($order->status)
                    ),
                    array(
                        'label' => $this->view->translate('Gross'),
                        'value' => ($order->via_credits) ? $api->getCredits($order->total_amt) : $this->view->locale()->toCurrency($order->total_amt, $order->currency)
                    ),
                    array(
                        'label' => $this->view->translate('Gateway'),
                        'value' => $gateway_title
                    ),
                    array(
                        'label' => $this->view->translate('Date'),
                        'value' => $this->view->timestamp($order->payment_date)
                    ),
                    array(
                        'label' => $this->view->translate('Options'),
                        'value' => $this->view->htmlLink(array('route' => 'store_purchase', 'order_id' => $order->ukey), $this->view->translate('details'), array())
                    ),
                )
            );
        }

        $this
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($this->navigation))
            ->renderContent();
    }

    public function panelWishListAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $table = Engine_Api::_()->getDbTable('products', 'store');
        $prefix = $table->getTablePrefix();

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($table->info('name'))
            ->joinLeft(array('w' => $prefix . 'store_wishes'), "w.product_id = " . $prefix . "store_products.product_id")
            ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
            ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
            ->where('w.user_id = ?', $viewer->getIdentity())
            ->group($prefix . 'store_products.product_id');

        $select = $table->setStoreIntegrity($select);

        $select
            ->where($prefix . 'store_products.quantity <> 0 OR ' . $prefix . 'store_products.type = ?', 'digital')
            ->where('w.user_id = ?', $viewer->getIdentity());

        if ($this->_getParam('search', false)) {
            $select->where($prefix . 'store_products.title LIKE ? OR ' . $prefix . 'store_products.description LIKE ?', '%' . $this->_getParam('search') . '%');
        }

        // Make paginator
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $form = $this->getSearchForm();
        $form->setMethod('get');
        $form->getElement('search')->setValue($this->_getParam('search'));

        $this->add($this->component()->itemSearch($form))
            ->add($this->component()->itemList($paginator, 'browseProductList', array('attrs' => array('class' => 'tile-view'))))
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($this->navigation))
            ->renderContent();
    }

    public function panelAddressAction()
    {
        if ($this->_getParam('format') == 'json' && $this->_getParam('just_locations')) {
            $parent_id = $this->_getParam('parent_id', 0);

            try {
                $element = new Engine_Form_Element_Select('state', array(
                    'Label' => 'STORE_State/Region',
                    'required' => true,
                    'decorators' => array(
                        'ViewHelper',
                    )
                ));

                /**
                 * @var $table    Store_Model_DbTable_Locations
                 * @var $location Store_Model_Location
                 */
                $table = Engine_Api::_()->getDbTable('locations', 'store');
                if (null == ($location = $table->findRow($parent_id))) {
                    return;
                }

                $select = $table->select()
                    ->from($table, array('location_id', 'location'))
                    ->where('parent_id =?', $location->getIdentity())
                    ->order('location ASC');

                foreach ($table->fetchAll($select) as $loc) {
                    $element->addMultiOption($loc->location_id, $loc->location);
                }

                $html = $element->render();
                $this->add($this->component()->html($html))
                    ->renderContent();
                return;
            } catch (Exception $e) {
                return;
            }
        }

        $form = new Store_Form_Panel_Address();

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation('store_main', true), -1)
                ->add($this->component()->quickLinks($this->navigation))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation('store_main', true), -1)
                ->add($this->component()->quickLinks($this->navigation))
                ->renderContent();
            return;
        }

        $details = $form->getValues();

        /**
         * @var $viewer       User_Model_User
         * @var $detailsTable Store_Model_DbTable_Details
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $detailsTable = Engine_Api::_()->getDbTable('details', 'store');

        try {
            $detailsTable->setDetails($viewer, $details);
            $form->addNotice('The details you have entered have been successfully saved.');
        } catch (Exception $e) {
            $form->addErrorMessage('An unexpected error has occurred! Please, make sure you have filled all the required fields correctly.');
        }

        $this->add($this->component()->form($form))
            ->add($this->component()->navigation('store_main', true), -1)
            ->add($this->component()->quickLinks($this->navigation))
            ->renderContent();
    }

    /**
     *  Panel Controller
     */

    /**
     *  Photo Controller
     */
    public function photoInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        /**
         * @var $product Store_Model_Product
         */
        if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
            Engine_Api::_()->core()->setSubject($product);
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        $this->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->pageObject = $page = $product->getStore();
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !($page->getStorePrivacy() || $product->isOwner($viewer))
            //      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            return $this->redirect($page->getHref());
        }
    }

    public function photoEditAction()
    {
        $product = $this->product;
        $page = $product->getPage();
        // Prepare data
        $paginator = $product->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(5);

        $navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
        $quick = $this->getNavigation(array('back', 'photo_add'), array('page_id' => $page->getIdentity(), 'product_id' => $product->getIdentity()));

        $this->add($this->component()->subjectPhoto($product))
            ->add($this->component()->itemList($paginator, 'photoManageList', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($paginator))
            ->add($this->component()->navigation($navigation))
            ->add($this->component()->quickLinks($quick))
            ->renderContent();
    }

    public function photoManageAction()
    {
        $photo_id = $this->_getParam('photo_id', 0);
        if ($photo_id) {
            $action_type = $this->_getParam('action_type');
            $photo = Engine_Api::_()->getItem('store_photo', $photo_id);
            if ($action_type == 'edit') {
                $form = new Engine_Form();
                $form->setAction('')
                    ->addElement('Text', 'title', array(
                        'label' => 'Title',
                        'value' => $photo->getTitle(),
                        'filters' => array(
                            new Engine_Filter_Censor(),
                            new Engine_Filter_HtmlSpecialChars(),
                        ),
                    ));
                $form->addElement('Textarea', 'description', array(
                    'label' => 'Description',
                    'value' => $photo->getDescription(),
                    'rows' => 2,
                    'cols' => 120,
                    'filters' => array(
                        new Engine_Filter_Censor(),
                    ),
                ));


                $form->addElement('Button', 'submit', array(
                    'type' => 'submit',
                    'label' => 'Save'
                ));

                if (!$this->getRequest()->isPost()) {
                    $this->add($this->component()->subjectPhoto($photo))
                        ->add($this->component()->form($form))
                        ->renderContent();
                    return;
                }

                if (!$form->isValid($this->getRequest()->getPost())) {
                    $this->add($this->component()->subjectPhoto($photo))
                        ->add($this->component()->form($form))
                        ->renderContent();
                    return;
                }
                $values = $form->getValues();
                $photo->title = $values['title'];
                $photo->description = $values['description'];
                $photo->save();
            } else
                if ($action_type == 'cover') {
                    $this->product->photo_id = $photo_id;
                    $this->product->save();
                }
        }

        return $this->redirect('parentRefresh');
    }

    public function photoRemoveAction()
    {
        $photo_id = $this->_getParam('photo_id', 0);

        if (!$photo_id) {
            return $this->redirect($this->view->url(array('controller' => 'photo', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
        }

        $photo = Engine_Api::_()->getItem('store_photo', $photo_id);

        if (!$photo) {
            return $this->redirect($this->view->url(array('controller' => 'photo', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
        }

        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();
        try {
            $storage = Engine_Api::_()->getItemTable('storage_file');
            $select = $storage->select()
                ->where('parent_file_id = ?', $photo->file_id);

            if (($file = $storage->fetchRow($select)) !== null) {
                $file->delete();
            }
            Engine_Api::_()->getApi('core', 'store')->deleteFile($photo->file_id);
            $photo->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->redirect('parentRefresh');
    }

    public function photoAddAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        /**
         * @var $product Store_Model_Product
         */
        $product = $this->product;
        $page = $product->getPage();

        $form = new Store_Form_Page_Products_Upload();
        $form->removeElement('file');
        $form->removeElement('fancyuploadfileids');
        $form->addElement('File', 'file', array(
            'label' => 'Add Photos',
            'order' => 0,
            'isArray' => true
        ));
        $form->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $form->getDecorator('description')->setOption('escape', false);

        $navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
        $quick = $this->getNavigation(array('back'), array('page_id' => $page->getIdentity(), 'product_id' => $product->getIdentity()));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation($navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->add($this->component()->navigation($navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
            return;
        }
        $form->getValues();
        $picupFiles = $this->getPicupFiles('file');
        if (empty($picupFiles))
            $photos = $form->file->getFileName();
        else
            $photos = $picupFiles;

        $photoTable = Engine_Api::_()->getItemTable('store_photo');
        $db = $photoTable->getAdapter();
        $db->beginTransaction();

        try {
            // Save Photos

            if (is_array($photos)) {
                foreach ($photos as $photoPath) {
                    $photo = $photoTable->createRow();
                    $photo->setFromArray(array(
                        'user_id' => $viewer->getIdentity()
                    ));
                    $photo->save();

                    $photo->setPhoto($photoPath);
                    $photo->collection_id = $product->getIdentity();
                    $photo->save();

                    if (!$product->photo_id) {
                        $product->photo_id = $photo->photo_id;
                        $product->save();
                    }
                }
            } else {
                $photo = $photoTable->createRow();
                $photo->setFromArray(array(
                    'user_id' => $viewer->getIdentity()
                ));
                $photo->save();

                $photo->setPhoto($photos);
                $photo->collection_id = $product->getIdentity();
                $photo->save();
                if (!$product->photo_id) {
                    $product->photo_id = $photo->photo_id;
                    $product->save();
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->redirect($this->view->url(array(
                'controller' => 'photo',
                'action' => 'edit',
                'product_id' => $product->getIdentity()),
            'store_extended', true));
    }

    /**
     *  Photo Controller
     */

    /**
     *  Video Controller
     */
    public function videoInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
            Engine_Api::_()->core()->setSubject($product);
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        $this->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->pageObject = $page = $product->getStore();
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !($page->getStorePrivacy() || $product->isOwner($viewer))
//      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid()
        ) {
            return $this->redirect($page->getHref());
        }

        $this->hasVideo = $product->hasVideo();
        $this->navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
    }

    public function videoCreateAction()
    {
        $viewer = $this->viewer;
        $product = $this->product;

        if ($this->hasVideo) {
            return $this->redirect($this->view->url(array('controller' => 'video', 'action' => 'edit', 'product_id' => $product->getIdentity()), 'store_extended'));
        }

        $video = $product->getVideo();

        // Create form
        $form = new Store_Form_Admin_Video_Upload();
        $form->getElement('type')->setAttrib('onchange', '');

        $form->getDecorator('description')->setOption('escape', false);
        if ($this->_getParam('type', false)) $form->getElement('type')->setValue($this->_getParam('type'));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->renderContent();
            return;
        }

        // Process
        $values = $form->getValues();

        $table = Engine_Api::_()->getDbtable('videos', 'store');
        $db = $table->getAdapter();
        $db->beginTransaction();

        /**
         * @var $video Store_Model_Video
         */
        try {
            // Check url
            $code = $this->extractCode($values['url'], $values['type']);
            if ($values['type'] == 1) {
                if (!$this->checkYouTube($code)) {
                    $form->getElement('url')->addError('We could not find a video there - please check the URL and try again.');
                    $this->add($this->component()->form($form))
                        ->add($this->component()->navigation($this->navigation))
                        ->renderContent();
                    return;
                }

            } elseif ($values['type'] == 2) {
                if (!$this->checkVimeo($code)) {
                    $form->getElement('url')->addError('We could not find a video there - please check the URL and try again.');
                    $this->add($this->component()->form($form))
                        ->add($this->component()->navigation($this->navigation))
                        ->renderContent();
                    return;
                }

            }
            // Create video
            $video = $table->createRow();

            $video->setFromArray($values);
            $video->product_id = (int)$this->_getParam('product_id');
            $video->owner_id = $viewer->getIdentity();
            $video->status = 1;
            $video->code = $code;
            $video->save();

            Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);

            $db->commit();
            $this->redirect('edit');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->message = $this->view->translate('Video has been saved.');
        return $this->redirect($this->view->url(array('controller' => 'video', 'action' => 'edit', 'product_id' => $product->getIdentity()), 'store_extended', true));
    }

    public function videoEditAction()
    {
        $product = $this->product;
        $page = $this->pageObject;
        $viewer = $this->viewer;

        if (!$this->hasVideo) {
            return $this->redirect($this->view->url(array('controller' => 'video', 'action' => 'create', 'product_id' => $product->getIdentity()), 'store_extended'));
        }

        $video = $product->getVideo();

        // Make form
        $form = new Store_Form_Admin_Video_Edit();
        $form->getElement('type')->setAttrib('onchange', '');
        $form->populate($video->toArray());

        $quick = $this->getNavigation(array(
                'back',
                'video_delete',
                'video_view',
                'create'), array(
                'product_id' => $product->getIdentity(),
                'page_id' => $page->getIdentity(),
                'title' => $product->getTitle())
        );

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
            return;
        }

        $values = $form->getValues();
        $table = $video->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            // Check url
            $code = $this->extractCode($values['url'], $values['type']);
            if ($values['type'] == 1) {
                if (!$this->checkYouTube($code)) {
                    $form->getElement('url')->addError('We could not find a video there - please check the URL and try again.');
                    $this->add($this->component()->form($form))
                        ->add($this->component()->navigation($this->navigation))
                        ->renderContent();
                    return;
                }

            } elseif ($values['type'] == 2) {
                if (!$this->checkVimeo($code)) {
                    $form->getElement('url')->addError('We could not find a video there - please check the URL and try again.');
                    $this->add($this->component()->form($form))
                        ->add($this->component()->navigation($this->navigation))
                        ->renderContent();
                    return;
                }

            }
            // Create video
            $video = $table->createRow();

            $video->setFromArray($values);
            $video->product_id = (int)$this->_getParam('product_id');
            $video->owner_id = $viewer->getIdentity();
            $video->status = 1;
            $video->code = $code;
            $video->save();

            Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);

            $db->commit();
            $this->redirect('edit');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->view->message = $this->view->translate('Video has been saved.');

        $this->add($this->component()->subjectPhoto($product))
            ->add($this->component()->form($form))
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->quickLinks($quick))
            ->renderContent();
    }

    public function videoDeleteAction()
    {
        $product = $this->product;
        $video = $product->getVideo();

        $form = new Store_Form_Admin_Video_Delete();

        if (!$video) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
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

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            Engine_Api::_()->getApi('core', 'store')->deleteVideo($video);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
        $this->redirect($this->view->url(array('controller' => 'video', 'action' => 'create', 'product_id' => $product->getIdentity()), 'store_extended', true));
    }

    /**
     *  Video Controller
     */

    /**
     *  Audios Controller
     */
    public function audiosInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        /**
         * @var $product Store_Model_Product
         */
        if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
            Engine_Api::_()->core()->setSubject($product);
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        $this->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->pageObject = $page = $product->getStore();
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
//      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
            !($page->getStorePrivacy() || $product->isOwner($viewer))
        ) {
            return $this->redirect($page->getHref());
        }


        $this->navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
    }

    public function audiosCreateAction()
    {
        $product = $this->product;
        $audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($product->getIdentity());
        $form = new Store_Form_Admin_Audios_Create();
        $form->removeElement('file');
        $form->addElement('File', 'file', array(
            'label' => 'Add Audios',
            'order' => 0,
            'isArray' => true
        ));
        $form->file->addValidator('Extension', false, 'mp3,ogg,aac,wma,wav');

        $quick = $this->getNavigation(array('back'), array('page_id' => $this->pageObject->getIdentity(), 'product_id' => $product->getIdentity()));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->subjectPhoto($product))
                ->add($this->component()->form($form))
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->quickLinks($quick))
                ->renderContent();
        }

        $form->getValues();

        $db = Engine_Api::_()->getDbTable('audios', 'store')->getAdapter();
        $db->beginTransaction();

        try {
            $picupFiles = $this->getPicupFiles('file');
            if (empty($picupFiles))
                $songs = $form->file->getFileName();
            else
                $songs = $picupFiles;

            if (is_array($songs)) {
                foreach ($songs as $song) {
                    $file = Engine_Api::_()->getApi('core', 'store')->createAudio($song);
                    $audio = $product->addAudio($file);
                    $file->setFromArray(array(
                        'parent_type' => 'store_audio',
                        'parent_id' => $audio->getIdentity(),
                        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                    ));
                    $file->save();
                }
            } else {
                $file = Engine_Api::_()->getApi('core', 'store')->createAudio($songs);
                $audio = $product->addAudio($file);
                $file->setFromArray(array(
                    'parent_type' => 'store_audio',
                    'parent_id' => $audio->getIdentity(),
                    'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
                ));
                $file->save();
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }

        return $this->redirect($this->view->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $product->getIdentity()), 'store_extended', true));
    }

    public function audiosEditAction()
    {
        $audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->product->getIdentity());

        if (!count($audios)) {
            $this->redirect($this->view->url(array('controller' => 'audios', 'action' => 'create', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
        }

        $quick = $this->getNavigation(array('back', 'audio_add'), array('page_id' => $this->pageObject->getIdentity(), 'product_id' => $this->product->getIdentity()));

        $this->add($this->component()->subjectPhoto($this->product));
        foreach ($audios as $audio) {
            $ul = $this->dom()->new_('ul', array('data-role' => "listview"), '', array(
                $this->dom()->new_('li', array(), '', array(
                    $this->dom()->new_('a', array(), '', array(
                        $this->dom()->new_('h3', array(), $audio->title),
                    )),
                    $this->dom()->new_('a', array('href' => $this->view->url(array('controller' => 'audios', 'action' => 'delete', 'product_id' => $this->product->getIdentity(), 'audio_id' => $audio->audio_id), 'store_extended', true), 'data-rel' => 'dialog', 'data-icon' => 'delete')),
                ))
            ));
            $this->add($this->component()->html($ul));
        }

        $this
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->quickLinks($quick))
            ->renderContent();
    }

    public function audiosDeleteAction()
    {
        $audio_id = (int)$this->_getParam('audio_id');

        $form = new Store_Form_Admin_Audios_Delete();
        $audio = Engine_Api::_()->getDbTable('audios', 'store')->findRow($audio_id);

        if (!$audio) {
            return $this->redirect($this->view->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
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

        if ($audio_id) {
            $db = $audio->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getApi('core', 'store')->deleteAudio($audio);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Audio has been deleted.');
        }
        return $this->redirect($this->view->url(array('controller' => 'audios', 'action' => 'edit', 'product_id' => $this->product->getIdentity()), 'store_extended', true));
    }

    /**
     *  Audios Controller
     */

    /**
     *  Transactions Controller
     */

    public function transactionsInit()
    {
        /**
         * @var $page Page_Model_Page
         */
        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            return $this->redirect($page->getHref());
        }

        $this->addPageInfo('contentTheme', 'd');
    }

    public function transactionsIndexAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = $this->pageObject;

        // Make form
        $formFilter = new Store_Form_Transaction_Filter(array('page' => $page));

        $formFilter
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->setAttribs(array(
                'id' => 'search_form',
                'class' => 'store_filter_form inner',
            ));

        // Process form
        if ($formFilter->isValid($this->_getAllParams())) {
            $filterValues = $formFilter->getValues();
        } else {
            $filterValues = array();
        }
        if (empty($filterValues['order'])) {
            $filterValues['order'] = 'transaction_id';
        }
        if (empty($filterValues['direction'])) {
            $filterValues['direction'] = 'DESC';
        }

        /**
         * Initialize select
         *
         * @var $table Store_Model_DbTable_Orderitems
         */
        $table = Engine_Api::_()->getDbtable('orderitems', 'store');
        $prefix = $table->getTablePrefix();
        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('oi' => $table->info('name')))
            ->joinInner(array('o' => $prefix . 'store_orders'), 'o.order_id=oi.order_id', array())
            ->joinInner(array('t' => $prefix . 'store_transactions'), 't.order_id=oi.order_id', array('t.user_id', 't.timestamp',
                'payment_state' => 't.state'))
            ->where('oi.item_type = ?', 'store_product')
            ->where("oi.status IN('completed', 'shipping', 'delivered')")
            ->group('oi.orderitem_id');

        // Add filter values
        if (!empty($filterValues['name'])) {
            $select
                ->where('oi.name LIKE ?', '%' . $filterValues['name'] . '%');
        }
        if (!empty($filterValues['member'])) {
            $select
                ->joinLeft(array('u' => $prefix . 'users'), 'u.user_id=t.user_id', array())
                ->where('u.displayname LIKE ?', '%' . $filterValues['member'] . '%');
        }
        if (!empty($filterValues['status'])) {
            $select->where('oi.status = ?', $filterValues['status']);
        }
        if (($user_id = $this->_getParam('user_id', @$filterValues['user_id']))) {
            $this->view->filterValues['user_id'] = $user_id;
            $select->where('t.user_id = ?', $user_id);
        }
        if (!empty($filterValues['order'])) {
            if (empty($filterValues['direction'])) {
                $filterValues['direction'] = 'DESC';
            }
            $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
        }


        $select->where('oi.page_id = ?', $page->getIdentity());

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        /**
         * @var $pageApi Store_Api_Page
         */
        $api = Engine_Api::_()->store();
        $pageApi = Engine_Api::_()->getApi('page', 'store');
        $balances = $pageApi->getBalance($page->getIdentity());
        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

        // Preload info
        $userIds = array();
        $orderIds = array();
        $productIds = array();
        foreach ($paginator as $item) {
            if (!empty($item->user_id)) {
                $userIds[] = $item->user_id;
            }

            if (!empty($item->order_id)) {
                $orderIds[] = $item->order_id;
            }

            if (!empty($item->order_id)) {
                $productIds[] = $item->item_id;
            }
        }

        $userIds = array_unique($userIds);
        $orderIds = array_unique($orderIds);
        $productIds = array_unique($productIds);

        // Preload users
        $users = array();
        if (!empty($userIds)) {
            foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
                $users[$user->user_id] = $user;
            }
        }

        // Preload orders
        $orders = array();
        if (!empty($orderIds)) {
            foreach (Engine_Api::_()->getItemTable('store_order')->find($orderIds) as $order) {
                $orders[$order->order_id] = $order;
            }
        }

        // Preload products
        $products = array();
        if (!empty($productIds)) {
            foreach (Engine_Api::_()->getItemTable('store_product')->find($productIds) as $product) {
                if ($product == null) continue;
                $products[$product->product_id] = $product;
            }
        }

        $values = array();
        foreach ($paginator as $item) {
            $user = @$users[$item->user_id];
            $order = @$orders[$item->order_id];
            /**
             * @var $product Store_Model_Product
             */
            $product = isset($products[$item->item_id]) ? @$products[$item->item_id] : null;

            $values[] = array(
                'title' => $this->view->translate('ID') . ' ' . $item->orderitem_id,
                'content' => array(
                    array(
                        'label' => $this->view->translate('Product'),
                        'value' => ($product instanceof Store_Model_Product) ? $product->__toString() : '<i>' . $item->name . '</i>'
                    ),
                    array(
                        'label' => $this->view->translate('Member'),
                        'value' => $user ? $user->__toString() : '<i>' . $this->view->translate('Deleted or Unknown Member') . '</i>'
                    ),
                    array(
                        'label' => $this->view->translate('Status'),
                        'value' => $this->view->translate(ucfirst($item->status))
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Quantity'),
                        'value' => $this->view->locale()->toNumber($item->qty)
                    ),
                    array(
                        'label' => $this->view->translate('Total Amount'),
                        'value' => $item->via_credits ? $api->getCredits($item->total_amt * $item->qty) : $this->view->locale()->toCurrency(($item->total_amt * $item->qty), $item->currency) .
                                $this->view->translate('(%s)', $item->currency)
                    ),
                    array(
                        'label' => $this->view->translate('Gateway fee'),
                        'value' => $item->via_credits ? $api->getCredits(0) : '-' . $this->view->locale()->toCurrency(($item->getGatewayFee()), $item->currency) .
                                $this->view->translate('(%s)', $item->currency)
                    ),
                    array(
                        'label' => $this->view->translate('Commission'),
                        'value' => $item->via_credits ? $api->getCredits($item->commission_amt * $item->qty) : $this->view->locale()->toCurrency(($item->commission_amt * $item->qty), $item->currency) .
                                $this->view->translate('(%s)', $item->currency)
                    ),
                    array(
                        'label' => $this->view->translate('Date'),
                        'value' => $this->view->locale()->toDateTime($item->timestamp)
                    ),
                    array(
                        'label' => '',
                        'value' => $this->view->htmlLink($this->view->url(array(
                                'action' => 'detail',
                                'orderitem_id' => $item->orderitem_id
                            )), $this->view->translate('details'))
                    ),
                )
            );
        }

        $navigation = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));
        $this
            ->add($this->component()->navigation($navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add($this->component()->navigation($navigation))
            ->add($this->component()->html($this->view->translate("STORE_VIEWS_SCRIPTS_STATISTICS_TRANSACTIONS_DESCRIPTION")))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    public function transactionsDetailAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = $this->pageObject;
        $item_id = $this->_getParam('orderitem_id');

        /**
         * @var $item Store_Model_Orderitem
         */
        if (null == ($item = Engine_Api::_()->getItem('store_orderitem', $item_id))) {
            $this->view->message = $this->view->translate("STORE_No order found with the provided id.");
            return $this->redirect($this->view->url(array('controller' => 'transactions', 'page_id' => $page->getIdentity()), 'store_extended', true));
        }

        /**
         * Preload Items
         *
         * @var $order Store_Model_Order
         */

        $user = $item->getOwner();
        $order = $item->getParent();
        $product = $item->getItem();
        $api = Engine_Api::_()->store();

        $country = null;
        $state = null;
        //Shipping Details
        if (!$item->isItemDigital()) {
            if (isset($order->shipping_details) &&
                isset($order->shipping_details['location_id_1']) &&
                null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
            ) {

                if (isset($order->shipping_details['location_id_2']) &&
                    null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
                ) {

                }
            }
        }

        $values = array();
        $values[] = array(
            'title' => $this->view->translate('STORE_Order Details'),
            'content' => array(
                array(
                    'label' => $this->view->translate('Member'),
                    'value' => ($user && $user->getIdentity()) ? $user->__toString() . '(' . $user->email . ')' : '<i>' . $this->view->translate('Deleted Member') . '</i>'
                ),
                array(
                    'label' => $this->view->translate('Status'),
                    'value' => ucfirst($item->status) . (in_array($item->status, array('processing', 'shipping')) ? '-' . $this->htmlLink($this->url(array('action' => 'change-status')), 'Change', array()) : '')
                ),
                array(
                    'label' => $this->view->translate('STORE_Product Title'),
                    'value' => $product ? $product->__toString() : $item->name
                ),
                array(
                    'label' => $this->view->translate('STORE_Parameters'),
                    'value' => $api->params_string($item->params)
                ),
                array(
                    'label' => $this->view->translate('STORE_Quantity'),
                    'value' => $this->view->locale()->toNumber($item->qty)
                ),
                array(
                    'label' => $this->view->translate('STORE_Last Update Date'),
                    'value' => $this->view->timestamp($item->update_date)
                ),
            )
        );
        $values[] = array(
            'title' => $this->view->translate('STORE_Payment Details'),
            'content' => array(
                array(
                    'label' => $this->view->translate('Item Amount'),
                    'value' => $item->via_credits ? $api->getCredits($item->item_amt * $item->qty) :
                            $this->view->locale()->toCurrency((float)($item->item_amt * $item->qty), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Tax Amount'),
                    'value' => $item->via_credits ? $api->getCredits($item->tax_amt * $item->qty) :
                            $this->view->locale()->toCurrency((float)($item->tax_amt * $item->qty), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Shipping Amount'),
                    'value' => $item->via_credits ? $api->getCredits($item->shipping_amt * $item->qty) :
                            $this->view->locale()->toCurrency((float)($item->shipping_amt * $item->qty), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Commission Amount'),
                    'value' => $item->via_credits ? $api->getCredits($item->commission_amt * $item->qty) :
                            $this->view->locale()->toCurrency((float)($item->commission_amt * $item->qty), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Gateway fee'),
                    'value' => $item->via_credits ? $api->getCredits(0) :
                            $this->view->locale()->toCurrency((float)($item->getGatewayFee()), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Total Gross Amount'),
                    'value' => $item->via_credits ? $api->getCredits($item->total_amt * $item->qty - ($item->commission_amt * $item->qty)) :
                            $this->view->locale()->toCurrency((float)($item->total_amt * $item->qty - ($item->commission_amt * $item->qty + $item->getGatewayFee())), $item->currency) . $this->view->translate('(%s)', $item->currency)
                ),
                array(
                    'label' => $this->view->translate('Payment Date'),
                    'value' => $this->view->timestamp($order->payment_date)
                ),

            )
        );

        if (!$item->isItemDigital()) {
            $values[] = array(
                'title' => $this->view->translate('Shipping Details'),
                'content' => array(
                    array(
                        'label' => $this->view->translate('Country'),
                        'value' => $country ? $country->location : ''
                    ),
                    array(
                        'label' => $this->view->translate('STORE_State/Region'),
                        'value' => $state ? $state->location : ''
                    ),
                    array(
                        'label' => $this->view->translate('City'),
                        'value' => $order->shipping_details['city']
                    ),
                    array(
                        'label' => $this->view->translate('Zip'),
                        'value' => $order->shipping_details['zip']
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Address Line'),
                        'value' => $order->shipping_details['address_line_1']
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Address Line 2'),
                        'value' => $order->shipping_details['address_line_2']
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Phone'),
                        'value' => $order->shipping_details['phone_extension'] + '-' + $order->shipping_details['phone']
                    ),
                )
            );
        }

        $navigation = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));

        $this
            ->add($this->component()->navigation($navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->renderContent();
    }

    /**
     *  Transactions Controller
     */

    /**
     *  Cart Controller
     */

    protected $_cart;

    public function cartInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->_helper->requireAuth()->setAuthParams('store_product', $viewer, 'order')->isValid();
    }

    public function cartIndexAction()
    {
        /**
         * @var $table  Store_Model_DbTable_Carts
         * @var $viewer User_Model_User
         * @var $cart   Store_Model_Cart
         */
        $page = $this->_getParam('page', 1);
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('store_cart');

        $via_credits = $this->_getParam('via_credits', 0);

        if (null == ($cart = $table->getCart($viewer->getIdentity())) || !$cart->hasItem()) {
            return $this->redirect($this->view->url(array(), 'store_general', true));
        }

        if ($cart->isPublic() && !$cart->hasPublicDetails($viewer)) {
            return $this->redirect($this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'details'), 'default', true));
        }

        $this->_cart = $cart;

        // Clear transaction session
        $session = new Zend_Session_Namespace('Store_Transaction');
        $session->unsetAll();

        /**
         * @var $paginator    Zend_Paginator
         * @var $detailsTbl   Store_Model_DbTable_Details
         * @var $locationsTbl Store_Model_DbTable_Locations
         */
        $paginator = Zend_Paginator::factory($cart->getItems());
        $paginator->setItemCountPerPage(6);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Shipping Details
        $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
        $locationsTbl = Engine_Api::_()->getDbTable('locations', 'store');
        $details = $detailsTbl->getDetails($viewer);
        $country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
        $region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));

        /**
         * Get totals
         * @var $item Store_Model_Cartitem
         */
        $totalItemAmt = 0;
        $totalShippingAmt = 0;
        $totalTaxAmt = 0;
        $location_id = (int)$cart->getShippingLocationId();
        $items = $cart->getPurchasableItems();
        foreach ($items as $item) {
            if ($via_credits && !$item->isStoreCredit()) {
                continue;
            }
            $totalItemAmt += $item->getPrice() * $item->qty;
            $totalShippingAmt += $item->getShippingPrice($location_id) * $item->qty;
            $totalTaxAmt += $item->getTax() * $item->qty;
        }

        $cartParams = $cart->getCartParams($via_credits);
        $totalTaxAmt = $cartParams['totalTaxAmt'];
        $totalItemAmt = $cartParams['totalItemAmt'];
        $totalShippingAmt = $cartParams['totalShippingAmt'];
        $total = $totalShippingAmt + $totalItemAmt + $totalTaxAmt;

        // Enabled Gateways
        $api = Engine_Api::_()->store();
        $mode = $api->getPaymentMode();
        if ($mode == 'client_store') {
            $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->fetchAll(array('title = ?' => 'PayPal'));
        } else {
            $gateways = Engine_Api::_()->getDbTable('gateways', 'store')->getEnabledGateways();
        }

        $token = Engine_Api::_()->store()->getToken();
        $prices = array(
            'items' => $this->view->toCurrency($totalItemAmt, $this->view->currency),
            'shipping' => $this->view->toCurrency($totalShippingAmt, $this->view->currency),
            'tax' => $this->view->toCurrency($totalTaxAmt, $this->view->currency),
            'total' => $this->view->toCurrency($total, $this->view->currency)
        );

        $gateways_array = array();
        if (count($gateways) > 0 && ($totalTaxAmt + $totalShippingAmt + $total) > 0) {
            $url = $this->view->layout()->staticBaseUrl . 'application/modules/Apptouch/externals/images/icons/';
            foreach ($gateways as $gateway) {
                if($token && $gateway->getIdentity() == 3) {
                    continue;
                }
                $gateways_array[] = array(
                    'id' => $gateway->getIdentity(),
                    'button' => '<div>' . $this->view->htmlImage(
                            $url . strtolower($gateway->getTitle()) . '.png',
                            $this->view->translate('STORE_Checkout with %s', $gateway->getTitle()),
                            array('alt' => $gateway->getTitle(),
                                'id' => 'gateway_button_' . $gateway->getIdentity()
                            )) . '</div>'
                );
            }
        }
        $gateway_params = array(
            'gateways' => $gateways_array,
            'cart_id' => $cart->getIdentity(),
            'url' => $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'order'), 'default', true)
        );

        $this->addPageInfo('gateway_params', $gateway_params);
        $this->addPageInfo('contentTheme', 'd');
        $this
            ->add($this->component()->navigation('store_main', true))
            ->add($this->component()->cartTotal($prices, $gateway_params))
            ->add($this->component()->itemList($paginator, 'cartProductList', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    public function cartAddAction()
    {
        $product_id = $this->_getParam('product_id', 0);

        if (!$product_id) {
            $this->view->message = $this->view->translate('Product doesn\'t exist');
            return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
        }

        $product = Engine_Api::_()->getItem('store_product', $product_id);

        if (!$product || !$product->getIdentity()) {
            $this->view->message = $this->view->translate('Product doesn\'t exist');
            return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
        }

        $form = $this->getInfoForm($product);
//    $form = new Engine_Form();
//    $form->setTitle($this->view->translate(
//      array('%s item available', '%s items available', (int)$product->getQuantity()),
//      $this->view->locale()->toNumber($product->getQuantity())));
//    $form->addElement('Text', 'quantity', array(
//      'label' => 'STORE_Quantity',
//      'required' => true
//    ));
//
//    if( is_array($product->params) && count($product->params) > 0 ) {
//      foreach($product->params as $param) {
//        $options = (isset($param['options']))?explode(',', $param['options']):array();
//        $multiOptions = array();
//        foreach( $options as $option ) {
//
//          $multiOptions[trim($option)] = trim($option);
//        }
//        $form->addElement('Select', $param['label'], array(
//          'label' => $param['label'],
//          'multiOptions' => $multiOptions
//        ));
//      }
//    }
//
//    $form->addElement('Button', 'submit', array(
//      'type' => 'submit',
//      'label' => 'STORE_Add to Cart'
//    ));

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

        $formValues = $form->getValues();
        $subject = null;

        if (!Engine_Api::_()->core()->hasSubject()) {
            $id = $this->_getParam('product_id');
            if (null !== $id) {
                $subject = Engine_Api::_()->getItem('store_product', $id);
                if ($subject->getStore()) {
                    $approved = $subject->getStore()->approved;
                } else {
                    $approved = 1;
                }

                if ($subject && $subject->getIdentity() && $approved) {
                    Engine_Api::_()->core()->setSubject($subject);
                } else {
                    if ($this->_getParam('format') == 'json') {
                        $this->view->status = 0;
                        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
                        return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
                    }
                    return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
                }
            }
        }

        $this->_helper->requireSubject('store_product');
        /**
         * @var $product    Store_Model_Product
         * @var $viewer     User_Model_User
         * @var $cartTb     Store_Model_DbTable_Carts
         * @var $table Store_Model_DbTable_Cartitems
         * @var $cart       Store_Model_Cart
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');
        $viewer = Engine_Api::_()->user()->getViewer();

        $params = array();
        $cartTb = Engine_Api::_()->getItemTable('store_cart');
        $table = Engine_Api::_()->getDbTable('cartitems', 'store');

        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        $cart = $cartTb->getCart($viewer->getIdentity());

        if ($cart && $cart->getRowByProduct($product->getIdentity())) {
            $this->view->status = false;
            $this->view->message = $this->view->translate('STORE_This product already added to your cart.');
            return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
        }

        /**
         * @var $settings Core_Model_DbTable_Settings
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        if ($product->getPrice() < $settings->getSetting('store.minimum.price', 0.15)) {
            $this->view->status = false;
            $this->view->message = $this->view->translate('STORE_This product cannot be added to your cart. '
                . 'The price of the product lower than allowed Minimum Price.');
            return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
        }

        if ($product->type == 'simple' && is_array($product->params) && count($product->params) > 0) {
            $params = array();
            foreach ($product->params as $param) {
                if (isset($formValues[$param['label']]) && $formValues[$param['label']]) {
                    $params[] = array(
                        'label' => $param['label'],
                        'value' => $formValues[$param['label']]
                    );
                }
            }

            if (count($params) <= 0) {
                $this->view->message = $this->view->translate('STORE_Additional parameters are required!');
                return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
            }

            $flag = true;
            foreach ($product->params as $key => $value) {
                if ($value['label'] != $params[$key]['label'] || !in_array($params[$key]['value'], explode(',', $value['options']))) {
                    $flag = false;
                }
            }

            if (!$flag) {
                $this->view->message = $this->view->translate('STORE_Please, check the additional parameters carefully! Wrong parameters have been assigned.');
                return;
            }
        }

        $quantity = $formValues['quantity'];
        if ($quantity > $product->quantity) {
            $quantity = $product->quantity;
        }

        // Process
        $db = Engine_Api::_()->getItemTable('store_product')->getAdapter();
        $db->beginTransaction();

        try {
            if (null == $cart) {
                $data = array(
                    'user_id' => $viewer->getIdentity()
                );

                $cart = $cartTb->createRow($data);
                $cart->save();
            }

            $data = array(
                'cart_id' => $cart->getIdentity(),
                'product_id' => $product->getIdentity(),
                'price' => $product->price,
                'title' => $product->getTitle(),
                'qty' => $quantity,
                'params' => $params,
            );

            $item = $table->createRow($data);
            $this->view->item_id = $item->save();

            // Commit
            $db->commit();
        } catch (Engine_Image_Exception $e) {
            $db->rollBack();
        }

        if (!($item instanceof Store_Model_Cartitem)) {
            $this->view->status = false;
            return;
        }

        $items = $table->fetchAll($table
                ->select()
                ->where('cart_id = ?', $cart->getIdentity())
                ->order('cartitem_id DESC')
        );


//    $this->view->status = true;
//    $totalCount = $cart->getItemCount();
//    $totalPrice = $this->view->locale()->toCurrency($cart->getPrice(), $currency);
//    $this->view->html = 'Items - ' . $totalCount . ', Total Price - ' . $totalPrice;

        $this->view->message = $this->view->translate('APPTOUCH_Product successfully added to Cart.');
        return $this->redirect('parentRefresh');
    }

    public function cartOrderAction()
    {
        /**
         * @var $viewer User_Model_User
         * @var $settings   Core_Model_DbTable_Settings
         */
        $cart_id = (int)$this->_getParam('cart');
        $gateway_id = (int)$this->_getParam('gateway_id');
        $offer_id = (int)$this->_getParam('offer_id', 0);
        $viewer = Engine_Api::_()->user()->getViewer();

        if (
            !$cart_id ||
            !Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order') ||
            !(Engine_Api::_()->getDbTable('gateways', 'store')->isGatewayEnabled($gateway_id))
        ) {
            $this->view->step = 1;
            return;
        }
        $gateway = Engine_Api::_()->getDbTable('gateways', 'store')->getGateway($gateway_id);
        $via_credits = ($gateway->title == 'Credit') ? true : false;

        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $currency = $settings->getSetting('payment.currency', 'USD');

        /**
         * @var $cartTb Store_Model_DbTable_Carts
         */
        $cartTb = Engine_Api::_()->getItemTable('store_cart');
        $select = $cartTb->select()
            ->where('cart_id = ?', $cart_id)
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('active = ?', 1)
            ->limit(1);

        /**
         * Get cart
         *
         * @var $cart Store_Model_Cart
         */
        if (null == ($cart = $cartTb->fetchRow($select))) {
            $this->view->step = 2;
            return;
        }

        //Get all purchasable items
        $cartItems = $cart->getPurchasableItems();
        if ($cartItems->count() <= 0) {
            $this->view->step = 3;
            return;
        }

        /**
         * @var $offer Offers_Model_Offer
         * @var $products Offers_Model_DbTable_Products
         */
        $isOffersEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers');
        if ($isOffersEnabled && $offer_id) {
            $productsIds = array();
            $ids = array();
            $offer = Engine_Api::_()->getItem('offer', $offer_id);
            $products = $offer->getProductsToArray();

            foreach ($cart->getPurchasableItems() as $item) {
                if ($via_credits && !$item->isStoreCredit()) {
                    continue;
                }
                foreach ($products as $index => $product) {
                    if ($product->product_id == $item->product_id) {
                        $ids[] = $product->product_id;
                        unset($products[$index]);
                        unset($product);
                    }
                }
                if (!count($products)) {
                    $productsIds[$offer_id] = $ids;
                }
            }
        }

        /**
         * Get all totals in a single loop
         *
         * @var $api  Store_Api_Core
         * @var $item Store_Model_Cartitem
         */
        $api = Engine_Api::_()->store();
        $shippingLocationId = $cart->getShippingLocationId();

        $totalItemAmt = 0;
        $totalTaxAmt = 0;
        $totalShippingAmt = 0;
        $totalCommissionAmt = 0;
        /*foreach ($cartItems as $item) {
            if ($via_credits && !$item->isStoreCredit()) {
                continue;
            }
            if ($isOffersEnabled && $offer_id && isset($productsIds[$offer_id]) && in_array($item->product_id, $productsIds[$offer_id])) {
                $totalItemAmt += (double)($offer->getDiscountPrice($item->getPrice()) + $item->getPrice() * ($item->qty - 1));
            } else {
                $totalItemAmt += (double)($item->getPrice() * $item->qty);
            }
            $totalCommissionAmt += (double)($api->getCommission($item->getPrice()) * $item->qty);
            $totalShippingAmt += (double)($item->getShippingPrice($shippingLocationId) * $item->qty);
            $totalTaxAmt += (double)($item->getTax() * $item->qty);
        }

        $totalAmt = $totalItemAmt + $totalTaxAmt + $totalShippingAmt;*/

        $cartParams = $cart->getCartParams($via_credits);
        $totalTaxAmt = $cartParams['totalTaxAmt'];
        $totalItemAmt = $cartParams['totalItemAmt'];
        $totalShippingAmt = $cartParams['totalShippingAmt'];

        $totalAmt = $totalItemAmt + $totalTaxAmt + $totalShippingAmt;

        if ($totalAmt <= 0) {
            return;
        }

        /**
         * @var $table      Store_Model_DbTable_Orders
         * @var $itemsTable Store_Model_DbTable_Orderitems
         */
        $table = Engine_Api::_()->getDbTable('orders', 'store');
        $itemsTable = Engine_Api::_()->getDbTable('orderitems', 'store');

        $shippingDetails = $cart->getShippingDetails();

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if (
                $cart->order_id == null ||
                null == ($order = Engine_Api::_()->getItem('store_order', $cart->order_id))
            ) {
                $data = array(
                    'user_id' => $viewer->getIdentity(),
                    'gateway_id' => $gateway_id,
                    'item_type' => $cart->getType(),
                    'item_id' => $cart->getIdentity(),
                    'item_amt' => $totalItemAmt,
                    'tax_amt' => $totalTaxAmt,
                    'shipping_amt' => $totalShippingAmt,
                    'total_amt' => $totalAmt,
                    'commission_amt' => $totalCommissionAmt,
                    'currency' => $currency,
                    'shipping_details' => $shippingDetails,
                    'via_credits' => $via_credits,
                    'offer_id' => $offer_id,
                    'token' => $cart->token
                );
                /**
                 * @var $order Store_Model_Order
                 */
                $order = $table->createRow();
                $order->setFromArray($data);
                $order->save();

                $cart->order_id = $order->getIdentity();
                $cart->save();
            } else {
                $order->gateway_id = $gateway_id;
                $order->status = 'initial';
                $order->item_amt = $totalItemAmt;
                $order->tax_amt = $totalTaxAmt;
                $order->shipping_amt = $totalShippingAmt;
                $order->total_amt = $totalAmt;
                $order->commission_amt = $totalCommissionAmt;
                $order->currency = $currency;
                $order->shipping_details = $shippingDetails;
                $order->via_credits = $via_credits;
                $order->offer_id = $offer_id;
                $order->updateUkey();
            }

            $cartItems = $cart->getPurchasableItems();

            /**
             * he@todo Should we clean all the old items?
             *
             * @var $cartItem Store_Model_Cartitem
             * @var $product  Store_Model_Product
             */
            $itemsTable->delete(array('order_id = ?' => $order->getIdentity()));
            $createdItems = array();
            $errorItems = array();
            foreach ($cartItems as $cartItem) {
                if ($via_credits && !$cartItem->isStoreCredit()) {
                    continue;
                }
                try {
                    $itemAmt = 0;
                    $offerQuantity = 0;
                    $product = $cartItem->getProduct();
                    $commissionAmt = (double)$api->getCommission($product->getPrice());
                    $shippingAmt = (double)$product->getShippingPrice($shippingLocationId);
                    $taxAmt = (double)$product->getTax();

                    if ($isOffersEnabled && $offer_id && isset($productsIds[$offer_id]) && in_array($cartItem->product_id, $productsIds[$offer_id])) {
                        $itemAmt += (double)($offer->getDiscountPrice($cartItem->getPrice()));
                        $totalAmt = (double)($itemAmt + $shippingAmt + $taxAmt);
                        $data = array(
                            'page_id' => $product->page_id,
                            'order_id' => $order->getIdentity(),
                            'item_id' => $product->getIdentity(),
                            'item_type' => $product->getType(),
                            'name' => $product->getTitle(),
                            'params' => $cartItem->params,
                            'qty' => 1,
                            'item_amt' => $itemAmt,
                            'tax_amt' => $taxAmt,
                            'shipping_amt' => $shippingAmt,
                            'commission_amt' => $commissionAmt,
                            'total_amt' => $totalAmt,
                            'currency' => $currency,
                            'via_credits' => $via_credits
                        );

                        $createdItems[] = $itemsTable->insert($data);
                        $offerQuantity = 1;
                    }

                    if ($cartItem->qty - $offerQuantity) {
                        $itemAmt = (double)$product->getPrice();
                        $totalAmt = (double)($itemAmt + $shippingAmt + $taxAmt);

                        $data = array(
                            'page_id' => $product->page_id,
                            'order_id' => $order->getIdentity(),
                            'item_id' => $product->getIdentity(),
                            'item_type' => $product->getType(),
                            'name' => $product->getTitle(),
                            'params' => $cartItem->params,
                            'qty' => $cartItem->qty - $offerQuantity,
                            'item_amt' => $itemAmt,
                            'tax_amt' => $taxAmt,
                            'shipping_amt' => $shippingAmt,
                            'commission_amt' => $commissionAmt,
                            'total_amt' => $totalAmt,
                            'currency' => $currency,
                            'via_credits' => $via_credits
                        );

                        $createdItems[] = $itemsTable->insert($data);
                    }

                    //Count totals
                } catch (Exception $e) {
                    print_firebug($e->__toString());
                    $errorItems[] = $cartItem->getIdentity();
                    continue;
                }
            }

            // Commit
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            print_firebug($e);

            $this->view->status = 0;
            $this->view->errorMessage = Zend_Registry::get('Zend_Translate')
                ->translate('STORE_An error has occurred while creating your order.'
                    . ' Please, try again with another gateway.');
            return;
        }


        //he@todo Should I inform a purchaser about these items?
        $this->view->createdItems = $createdItems;
        $this->view->errorItems = $errorItems;

        $this->view->status = 1;
        $this->view->link = $this->view->url(array('order_id' => $order->ukey), 'store_transaction_profile', true);
    }

    public function cartRemoveAction()
    {
        $subject = null;

        if (!Engine_Api::_()->core()->hasSubject()) {
            $id = $this->_getParam('product_id');
            if (null !== $id) {
                $subject = Engine_Api::_()->getItem('store_product', $id);
                if ($subject->getStore()) {
                    $approved = $subject->getStore()->approved;
                } else {
                    $approved = 1;
                }

                if ($subject && $subject->getIdentity() && $approved) {
                    Engine_Api::_()->core()->setSubject($subject);
                } else {
                    return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
                }
            }
        }

        $this->_helper->requireSubject('store_product');
        /**
         * @var $viewer     User_Model_User
         * @var $cartTb     Store_Model_DbTable_Carts
         * @var $cartitemTb Store_Model_DbTable_Cartitems
         * @var $cart       Store_Model_Cart
         */

        $viewer = Engine_Api::_()->user()->getViewer();

        $cartTb = Engine_Api::_()->getItemTable('store_cart');
        $cartitemTb = Engine_Api::_()->getDbTable('cartitems', 'store');

        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        $cart = $cartTb->getCart($viewer->getIdentity());

        $cartitem_id = $this->_getParam('item_id', 0);
        $cartitemTb->delete(array('cartitem_id =' . $cartitem_id));

        return $this->refresh();
    }

    public function cartDetailsAction()
    {
        $this->view->form = $form = new Store_Form_Cart_Details();

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

        $details = (array)$this->getRequest()->getPost();

        /**
         * @var $viewer       User_Model_User
         * @var $detailsTable Store_Model_DbTable_Details
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $detailsTable = Engine_Api::_()->getDbTable('details', 'store');

        try {
            $detailsTable->setDetails($viewer, $details);

            $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            return $this->redirect(
                $host_url . $this->view->url(
                    array(
                        'module' => 'store',
                        'controller' => 'cart',
                    ), 'default', true
                )
            );

        } catch (Exception $e) {
            $form->addErrorMessage($this->view->translate('An unexpected error has occurred! Please, make sure you have filled all the required fields correctly.'));
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }
        $this->view->message = $this->view->translate('The details you have entered have been successfully saved.');
        $this->redirect('parentRefresh');
    }

    public function cartSeeDetailsAction()
    {
        $item_id = $this->_getParam('item_id', 0);

        /**
         * Get models
         *
         * @var $item    Store_Model_Cartitem
         * @var $product Store_Model_Product
         */
        if (null == ($item = Engine_Api::_()->getItem('store_cartitem', $item_id)) ||
            null == ($product = $item->getProduct())
        ) {
            $this->view->message = $this->view->translate('STORE_No product found');
            return $this->redirect('parentRefresh');
        }

        $isProductQuantityEnough = ($product->quantity < $item->qty) ? 0 : 1;
        $isUserLocationSupported = $isLocationSupported = $item->isUserLocationSupported();

        if (!$isLocationSupported) {
            $parent_id = $this->_getParam('parent_id', 0);
            /**
             * @var $locationApi Store_Api_Location
             * @var $product     Store_Model_Product
             * @var $table       Store_Model_DbTable_Locations
             * @var $parent      Store_Model_Location
             * @var $locationApi Store_Api_Location
             */
            $locationApi = Engine_Api::_()->getApi('location', 'store');
            $table = Engine_Api::_()->getDbTable('locations', 'store');

            $select = $table->select()->where('location_id = ?', $parent_id);
            $parent = $table->fetchRow($select);

            $paginator = $locationApi->getPaginator($product->page_id, $this->_getParam('page', 1), $parent_id, 'product', $product->getIdentity());
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        }


        $html = '';
        if (!$isProductQuantityEnough) {
            $html .= $this->view->translate(
                'STORE_You can not purchase %1s amount of this product. Please, try to contact to the store owner or select available amount of quantity.',
                $item->qty
            );

            $html .= '<br>' . $this->view->translate(
                    'STORE_%1s - available quantity: %2s',
                    array(
                        $this->view->htmlLink($product->getHref(), $product->getTitle()),
                        $product->quantity
                    )
                );
        }

        if (!$isUserLocationSupported) {
            $html .= '<br>' . $this->view->translate(
                    'STORE_Unfortunately, the store of this product does not support your location. Please, try to contact to the store owner or %1s your shipping details',
                    $this->view->htmlLink($this->view->url(array(
                            'controller' => 'panel',
                            'action' => 'address',
                        ), 'store_extended', true),
                        $this->view->translate('update'))
                );

            $html .= '<br>' . $this->view->translate(
                    'STORE_%1s available shipping locations',
                    $this->view->htmlLink($product->getHref(), $product->getTitle()));

            if ($parent != null) {
                $html .= '<br><br>' . $this->view->htmlLink(array(
                        'route' => 'store_extended',
                        'controller' => 'cart',
                        'action' => 'see-details',
                        'item_id' => $item->getIdentity(),
                    ), $this->view->translate('Locations'));

                $location = $parent;

                do {
                    $html .= '<br>' . $this->view->htmlLink(array(
                            'route' => 'store_extended',
                            'controller' => 'cart',
                            'action' => 'see-details',
                            'parent_id' => $location->getIdentity(),
                            'item_id' => $item->getIdentity(),
                        ), $this->view->truncate($location->location));
                    $location = $location->getParent();
                } while ($location != null);
            }

            $html .= '<br><br>';

            if ($paginator->count() > 0) {
                $values = array();
                $counter = 0;
                foreach ($paginator as $item) {
                    $counter++;
                    $values[] = array(
                        'title' => $counter,
                        'content' => array(
                            array(
                                'label' => $this->view->translate("STORE_Location Name"),
                                'value' => '<i>' . $this->view->truncate($item->location, 160) . '</i>'
                            ),
                            array(
                                'label' => ($parent_id === 0) ? $this->view->translate("STORE_Sub-Locations") : '',
                                'value' => ($parent_id === 0) ? $this->view->htmlLink($this->view->url(array('parent_id' => $item->getIdentity())), (int)$item->sub_locations) : ''
                            ),
                            array(
                                'label' => $this->view->translate("STORE_Shipping Price"),
                                'value' => (is_null($item->shipping_amt)) ? $this->view->translate('STORE_Only Sub-Locations') : $this->view->locale()->toCurrency($item->shipping_amt, $this->view->settings('payment.currency', 'USD'))
                            ),
                            array(
                                'label' => $this->view->translate('STORE_Shipping Days'),
                                'value' => $this->view->locale()->toNumber($item->shipping_days)
                            ),
                        )
                    );
                }
            } else {
                $html .= $this->view->translate("STORE_No locations found.");
            }
        }

        $this->add($this->component()->html($html));
        if (count($values))
            $this->add($this->component()->customComponent('fieldsValues', $values));
        $this->renderContent();
    }

    /**
     *  Cart Controller
     */


    /**
     *  Requests Controller
     */
    public function requestsInit()
    {
        /**
         * @var $page Page_Model_Page
         */
        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isStore() ||
            !$page->isAllowStore() ||
            !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            $this->redirect($page->getHref());
        }

        $this->navigation = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));

        $this->addPageInfo('contentTheme', 'd');
    }

    public function requestsIndexAction()
    {
        /**
         * @var $page  Page_Model_Page
         * @var $table Store_Model_DbTable_Requests
         */
        $page = $this->pageObject;
        $table = Engine_Api::_()->getDbTable('requests', 'store');

        $select = $table
            ->select()
            ->where('page_id = ?', $page->getIdentity());

        $statuses = array('' => ' ');

        $requests = $table->fetchAll($select);
        foreach ($requests as $request) {
            $statuses[$request->status] = Zend_Registry::get('Zend_Translate')->_(ucfirst($request->status));
        }

        $values = array(
            'order' => 'request_id',
            'order_direction' => 'DESC',
        );

        $select->order($values['order'] . ' ' . $values['order_direction']);

        $valuesCopy = array_filter($values);

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        /**
         * @var $pageApi Store_Api_Page
         */
        $pageApi = Engine_Api::_()->getApi('page', 'store');
        $balances = $pageApi->getBalance($page->getIdentity());
        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

        $values = array();
        foreach ($paginator as $request) {
            $values[] = array(
                'title' => $this->view->translate('ID') . ' ' . $request->getIdentity(),
                'content' => array(
                    array(
                        'label' => $this->view->translate('Amount'),
                        'value' => $this->view->toCurrency($request->amt)
                    ),
                    array(
                        'label' => $this->view->translate('Status'),
                        'value' => $this->view->translate(ucfirst($request->status))
                    ),
                    array(
                        'label' => $this->view->translate('Request Message'),
                        'value' => Engine_String::strip_tags($request->request_message)
                    ),
                    array(
                        'label' => $this->view->translate('Response Message'),
                        'value' => Engine_String::strip_tags($request->response_message)
                    ),
                    array(
                        'label' => $this->view->translate('Request Date'),
                        'value' => $this->view->timestamp($request->request_date)
                    ),
                    array(
                        'label' => $this->view->translate('Response Date'),
                        'value' => ($request->response_date) ? $this->view->timestamp($request->request_date) : ''
                    ),
                    array(
                        'label' => $this->view->translate('Options'),
                        'value' => ($request->status == 'waiting') ? $this->view->htmlLink($this->view->url(array(
                                'controller' => 'requests',
                                'action' => 'cancel',
                                'request_id' => $request->request_id,
                                'page_id' => $this->pageObject->getIdentity(),
                            ), 'store_extended', true), $this->view->translate("cancel"), array('data-rel' => "dialog")) :
                                $this->view->htmlLink($this->view->url(array(
                                    'controller' => 'requests',
                                    'action' => 'detail',
                                    'request_id' => $request->request_id,
                                    'page_id' => $this->pageObject->getIdentity(),
                                ), 'store_extended', true), $this->view->translate("details"), array('data-rel' => "dialog"))
                    ),
                )
            );
        }

        $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
        $title = $this->dom()->new_('h3', array(), $this->view->translate('Short Information'));
        $text = '<table><tr><th>' . $this->view->translate('Current Balance') . '</th><td> - ' . $this->view->toCurrency($balances->getBalance()) . '</td></tr>';
        $text = $text . '<tr><th>' . $this->view->translate('Requested Amount') . '</th><td> - ' . $this->view->toCurrency($balances->getRequested()) . '</td></tr>';
        $text = $text . '<tr><th>' . $this->view->translate('Last Request Date') . '</th><td> - ' . (($balances->requested_date) ? $this->view->timestamp($balances->requested_date) : $this->view->translate('Never')) . '</td></tr>';
        $text = $text . '<tr><th>' . $this->view->translate('Pending Amount') . '</th><td> - ' . $this->view->toCurrency($balances->getPending()) . '</td></tr>';
        $text = $text . '<tr><th>' . $this->view->translate('Transferred Amount') . '</th><td> - ' . $this->view->toCurrency($balances->getTransfer()) . '</td></tr>';
        $text = $text . '<tr><th>' . $this->view->translate('Last Transfer Date') . '</th><td> - ' . (($balances->transferred_date) ? $this->view->timestamp($balances->transferred_date) : $this->view->translate('Never')) . '</td></tr>';
        $text .= '</table>';
        $href = $this->view->url(array('controller' => 'requests', 'action' => 'request', 'page_id' => $page->getIdentity(), 'balance_id' => $balances->getIdentity()), 'store_extended', true);
        $text = $text . '<br><a href="' . $href . '" data-role="button" data-rel="dialog">' . $this->view->translate('Request Money') . '</a>';

        $body = $this->dom()->new_('p', array(), $text);
        $element->append($title);
        $element->append($body);


        $this
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add($this->component()->html($this->view->translate('STORE_VIEWS_SCRIPTS_REQUEST_DESCRIPTION')))
            ->add($this->component()->html($element))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    public function requestsDetailAction()
    {
        $request_id = $this->_getParam('request_id');

        /**
         * @var $request Store_Model_Request
         * @var $page    Page_Model_Page
         */
        if (null == ($request = Engine_Api::_()->getItem('store_request', $request_id)) ||
            null == ($page = $request->getOwner('page')) ||
            !Engine_Api::_()->getApi('page', 'store')->isStore($page->getIdentity())
        ) {
            $this->view->message = $this->view->translate('STORE_No request was found with the provided ID.');
            return $this->redirect($this->view->url(array('module' => 'store', 'controller' => 'requests', 'action' => 'index', 'page_id' => $this->_getParam('page_id')), 'default', true));
        }

        $gateway = null;
        $order = null;
        /**
         * @var $order Store_Model_Order;
         * @var $order Store_Model_Gateway;
         */
        if (in_array($request->status, array('completed', 'pending')) &&
            null != ($order = $request->getOrderId()) &&
            null != ($gateway = Engine_Api::_()->getItem('store_gateway', $order->gateway_id))
        ) {


            $user = $order->getOwner();
        }

        $values = array();
        $values[0] = array(
            'title' => $this->view->translate('Information'),
            'content' => array(
                array(
                    'label' => $this->view->translate('STORE_Store Name'),
                    'value' => $this->pageObject->__toString()
                ),
                array(
                    'label' => $this->view->translate('STORE_Owner Name'),
                    'value' => $this->pageObject->getOwner()->__toString()
                ),
                array(
                    'label' => $this->view->translate('Status'),
                    'value' => ucfirst($request->status)
                ),
                array(
                    'label' => $this->view->translate('Requested Amount'),
                    'value' => $this->view->toCurrency($request->amt)
                ),
                array(
                    'label' => $this->view->translate('Gateway fee'),
                    'value' => $this->view->toCurrency($request->getGatewayFee())
                ),
                array(
                    'label' => $this->view->translate('Gross Amount'),
                    'value' => $this->view->toCurrency($request->amt - $request->getGatewayFee())
                ),
                array(
                    'label' => $this->view->translate('Requested Date'),
                    'value' => $this->view->timestamp($request->request_date)
                ),
                array(
                    'label' => $this->view->translate('Requested Message'),
                    'value' => Engine_String::strip_tags($request->request_message)
                ),
            )
        );
        if ($request->response_date && $request->status != 'waiting') {
            $values[0]['content'][] = array(
                'label' => $this->view->translate('Response Date'),
                'value' => $this->view->timestamp($request->response_date)
            );
            $values[0]['content'][] = array(
                'label' => $this->view->translate('Response Message'),
                'value' => Engine_String::strip_tags($request->response_message)
            );
        }

        if ($gateway) {
            $values[1] = array(
                'title' => $this->view->translate('Payment'),
                'content' => array(
                    array(
                        'label' => $this->view->translate('Member'),
                        'value' => $user->__toString()
                    ),
                    array(
                        'label' => $this->view->translate('Date'),
                        'value' => $this->view->timestamp($order->payment_date) . ' (' . $this->view->locale()->toDateTime($order->payment_date) . ')'
                    ),
                    array(
                        'label' => $this->view->translate('Gateway'),
                        'value' => $gateway->getTitle()
                    ),
                    array(
                        'label' => $this->view->translate('Currency'),
                        'value' => $order->currency
                    ),
                )
            );
        }

        $this
            ->add($this->component()->navigation($this->navigation))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->renderContent();
    }

    public function requestsCancelAction()
    {
        /**
         * @var $page    Page_Model_Page
         * @var $request Store_Model_Request
         */
        $page = $this->pageObject;
        $request_id = $this->_getParam('request_id');

        if (null == ($request = Engine_Api::_()->getItem('store_request', $request_id)) || !$request->isOwner($page)) {
            $this->view->message = $this->view->translate('STORE_No request was found with the provided ID.');
            return $this->redirect($this->view->url(array('module' => 'store', 'controller' => 'requests', 'action' => 'index', 'page_id' => $page->getIdentity()), 'default', true));
        }

        $form = new Store_Form_Statistics_Cancel(array('request' => $request));

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

        $request->cancel();

        $this->view->message = $this->view->translate('STORE_Your request has been successfully cancelled');
        return $this->redirect($this->view->url(array('module' => 'store', 'controller' => 'requests', 'action' => 'index', 'page_id' => $page->getIdentity()), 'default', true));
    }

    public function requestsRequestAction()
    {
        /**
         * @var $page     Page_Model_Page
         * @var $settings Core_Model_DbTable_Settings
         * @var $balance Store_Model_Balance
         */
        $page = $this->pageObject;
        $settings = Engine_Api::_()->getDbTable('settings', 'core');

        $balance = Engine_Api::_()->getItem('store_balance', $this->_getParam('balance_id'));
        $allowedAmt = (double)$settings->getSetting('store.request.amount', 100);

        if ($balance->getBalance() < $allowedAmt) {
            $message = $this->view->translate("STORE_YOU_DO_NOT_HAVE_ENOUGH_MONEY_FOR_REQUESTING %1s", $this->view->toCurrency($allowedAmt));
            $this->add($this->component()->html($message))
                ->renderContent();
            return;
        }

        $form = new Store_Form_Statistics_Request(array('params' => array(
            'current' => $balance->getBalance(),
            'allowed' => $allowedAmt
        )));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $values = $form->getValues();

        /**
         * @var $table   Store_Model_DbTable_Requests
         * @var $request Store_Model_Request
         */
        $table = Engine_Api::_()->getDbTable('requests', 'store');
        $db = $table->getDefaultAdapter();

        $db->beginTransaction();
        try {
            $request = $table->createRow(array(
                'page_id' => $page->getIdentity(),
                'amt' => $values['request_amt'],
                'request_message' => $values['request_message'],
                'request_date' => new Zend_Db_Expr('NOW()')
            ));

            if ($request->save()) {
                $balance->decrease($request->amt);
                $balance->increaseRequested($request->amt);
            }
            $db->commit();
        } catch (Exception $e) {
            if (APPLICATION_ENV == 'development')
                throw $e;
            else {
                $form->addError('STORE_An unexpected error has occurred. Please, try again.');
                $this->add($this->component()->form($form))
                    ->renderContent();
            }
            return;
        }

        // Add notification
//    Engine_Api::_()->getDbtable('notifications', 'activity')
//      ->addNotification($user, $viewer, $viewer, 'friend_follow');

        $this->view->message = $this->view->translate('STORE_Your request has been successfully completed.');
        return $this->redirect($this->view->url(array('module' => 'store', 'controller' => 'requests', 'action' => 'index', 'page_id' => $page->getIdentity()), 'default', true));
    }

    /**
     *  Requests Controller
     */


    /**
     *  Settings Controller
     */
    public function settingsInit()
    {
        /**
         * @var $page Page_Model_Page
         */
        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            return $this->redirect($page->getHref());
        }

        $this->quick = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));
        $this->navigation = $this->getNavigation(array('settings'), array('page_id' => $page->getIdentity()));

        $this->addPageInfo('contentTheme', 'd');
    }

    public function settingsGatewayAction()
    {
        /**
         * @var $table    Payment_Model_DbTable_Gateways
         * @var $paypal   Payment_Model_Gateway
         * @var $settings Core_Api_Settings
         * @var $api      Store_Model_Api
         */
        // Make paginator
        $select = Engine_Api::_()->getDbtable('gateways', 'store')->select()
            ->where('`plugin` != ?', 'Store_Plugin_Gateway_Testing')
            ->where('`plugin` != ?', 'Store_Plugin_Gateway_Credit');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $page_api = Engine_Api::_()->getApi('page', 'store');
        $page = $this->pageObject;

        $values = array();
        foreach ($paginator as $item) {
            $values[] = array(
                'title' => $item->title,
                'content' => array(
                    array(
                        'label' => $this->view->translate('ID'),
                        'value' => $item->gateway_id
                    ),
                    array(
                        'label' => $this->view->translate('Enabled'),
                        'value' => ($page_api->isGatewayEnabled($page->getIdentity(), $item->gateway_id) ? $this->view->translate('Yes') : $this->view->translate('No'))
                    ),
                    array(
                        'label' => $this->view->translate('Options'),
                        'value' => $this->view->htmlLink($this->view->url(array('action' => 'gateway-edit', 'gateway_id' => $item->gateway_id)), $this->view->translate('edit'))
                    ),
                )
            );
        }
        $this
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add($this->component()->html($this->view->translate('STORE_GATEWAY_ACCOUNT_SETTINGS')))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->add($this->component()->quickLinks($this->quick))
            ->renderContent();
    }

    public function settingsGatewayEditAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject('page');
        $gateway_id = $this->_getParam('gateway_id', false);

        if (!$gateway_id) {
            return $this->redirect($this->view->url(array('action' => 'gateway', 'page_id' => $page->getIdentity()), 'store_settings', true));
        }
        /**
         * @var $api Store_Model_Api;
         */
        if (null == ($api = Engine_Api::_()->getDbTable('apis', 'store')->getApi($page->getIdentity(), $gateway_id))) {
            $api = Engine_Api::_()->getDbTable('apis', 'store')->createRow(array(
                'page_id' => $page->getIdentity(),
                'gateway_id' => $gateway_id,
            ));
            $api->save();
        }
        $mode = Engine_Api::_()->store()->getPaymentMode();
        $G2CO = Engine_Api::_()->getDbTable('gateways', 'store')->fetchRow(array('title = ?' => '2Checkout'));

        if ($mode == 'client_store' && $api && $G2CO && $api->gateway_id == $G2CO->gateway_id) {
            return $this->redirect($this->view->url(array('action' => 'gateway', 'page_id' => $page->getIdentity()), 'store_settings', true));
        }

        $plugin = $api->getPlugin();
        /**
         * @var $form Engine_Form;
         */
        $form = $plugin->getAdminGatewayForm(array('isTestMode' => $api->test_mode));
        $form->cancel->href = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit',
            'page_id' => $page->getIdentity()), 'page_team');

        // Populate form
        $form->populate($api->toArray());
        if (is_array($api->config)) {
            $form->populate($api->config);
        }

        if (!$api->email) {
            $form->populate(array('email' => $api->getEmail()));
        }

        // Check method/valid
        if (!$this->getRequest()->isPost()) {
            $this
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->subjectPhoto($page))
                ->add($this->component()->form($form))
                ->add($this->component()->quickLinks($this->quick))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this
                ->add($this->component()->navigation($this->navigation))
                ->add($this->component()->subjectPhoto($page))
                ->add($this->component()->form($form))
                ->add($this->component()->quickLinks($this->quick))
                ->renderContent();
            return;
        }

        // Process
        $values = $form->getValues();

        $enabled = (bool)$values['enabled'];
        $email = $values['email'];
        unset($values['enabled']);
        unset($values['email']);

        // Validate gateway config
        if ($enabled) {
            $gatewayObject = $api->getGateway();

            try {
                $gatewayObject->setConfig($values);
                $response = $gatewayObject->test();
            } catch (Exception $e) {
                $enabled = false;
                $form->populate(array('enabled' => false));
                $form->addError(sprintf('Gateway login failed. Please double check ' .
                    'your connection information. The gateway has been disabled. ' .
                    'The message was: [%2$d] %1$s', $e->getMessage(), $e->getCode()));
            }
        } else {
            $form->addError('Gateway is currently disabled.');
        }

        // Process
        $message = null;
        try {
            $values = $api->getPlugin()->processAdminGatewayForm($values);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $values = null;
        }

        if (null !== $values) {
            $api->setFromArray(array(
                'email' => $email,
                'enabled' => $enabled,
                'config' => $values,
            ));
            $api->save();

            $form->addNotice('Changes saved.');
        } else {
            $form->addError($message);
        }

        $this->add($this->component()->form($form))
            ->add($this->component()->quickLinks($this->quick))
            ->add($this->component()->navigation($this->navigation))
            ->renderContent();
    }

    /**
     *  Settings Controller
     */


    /**
     *  Locations Controller
     */
    public function locationsInit()
    {
        /**
         * @var $page Page_Model_Page
         */
        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            return $this->redirect($page->getHref());
        }

        $this->quick = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));
        $this->navigation = $this->getNavigation(array('settings'), array('page_id' => $page->getIdentity()));

        $this->addPageInfo('contentTheme', 'd');
    }

    public function locationsIndexAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject('page');
        $parent_id = $this->_getParam('parent_id', 0);

        /**
         * @var $table       Store_Model_DbTable_Locations
         * @var $parent      Store_Model_Location
         * @var $locationApi Store_Api_Location
         */
        $table = Engine_Api::_()->getDbTable('locations', 'store');
        $locationApi = Engine_Api::_()->getApi('location', 'store');

        $select = $table->select()->where('location_id = ?', $parent_id);
        $parent = $table->fetchRow($select);
        $paginator = $locationApi->getPaginator($page->getIdentity(), $this->_getParam('page', 1), $parent_id, 'supported');
        $paginator->setItemCountPerPage(10);

        $values = array();
        foreach ($paginator as $item) {
            $edit = $this->view->htmlLink($this->view->url(array('action' => 'edit', 'location_id' => $item->getIdentity())), $this->view->translate('STORE_Edit Location'), array(
                'data-role' => 'button',
                'data-inline' => 'true',
                'data-mini' => 'true',
                'data-rel' => 'dialog'
            ));

            $delete = $this->view->htmlLink($this->view->url(array('action' => 'remove', 'location_id' => $item->getIdentity())), $this->view->translate('STORE_Delete Location'), array(
                'data-role' => 'button',
                'data-inline' => 'true',
                'data-mini' => 'true',
                'data-rel' => 'dialog'
            ));

            $editSub = '';
            $subL = '';
            if ($parent_id === 0) {
                $editSub = $this->view->htmlLink($this->view->url(array('controller' => 'locations', 'page_id' => $page->page_id, 'parent_id' => $item->getIdentity()), 'store_settings', true), $this->view->translate('STORE_Edit Sub-location'), array(
                    'data-role' => 'button',
                    'data-inline' => 'true',
                    'data-mini' => 'true'
                ));
                $subL = $this->view->translate('STORE_Sub-Locations');
            }

            $buttons = '<div data-role="controlgroup" data-type="horizontal">' . $edit . $delete . $editSub . '</div>';
            $values[] = array(
                'title' => $this->view->translate('STORE_Location Name') . ' (' . $this->view->truncate($item->location, 60) . ')',
                'content' => array(
                    array(
                        'label' => $subL,
                        'value' => ($parent_id === 0) ? $this->view->htmlLink($this->view->url(array(
                                'controller' => 'locations',
                                'page_id' => $page->page_id,
                                'parent_id' => $item->getIdentity()
                            ), 'store_settings', true), (int)$item->sub_locations) : ''
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Shipping Price'),
                        'value' => (is_null($item->shipping_amt)) ? $this->view->translate('STORE_Only Sub-Locations') :
                                $this->view->locale()->toCurrency($item->shipping_amt, $this->view->settings('payment.currency', 'USD'))
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Shipping Days'),
                        'value' => $this->view->locale()->toNumber($item->shipping_days)
                    ),
                    array(
                        'label' => $this->view->translate('Options'),
                        'value' => $buttons
                    ),
                )
            );
        }

        $button = $this->dom()->new_('a', array(
            'href' => $this->view->url(array('action' => 'add')),
            'data-role' => 'button',
            'data-inline' => 'true',
            'data-mini' => 'true',
            'data-rel' => 'dialog'), $this->view->translate('STORE_Add Locations'));

        $this
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add($this->component()->html($this->view->translate('STORE_PAGE_LOCATIONS_SUPPORTED_DESCRIPTION')))
            ->add($this->component()->html($button))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->add($this->component()->quickLinks($this->quick))
            ->renderContent();
    }

    public function locationsEditAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject('page');
        $location_id = $this->_getParam('location_id');

        /**
         * @var $location Store_Model_Locations
         */
        if (null == $location = Engine_Api::_()->getDbTable('locations', 'store')->findRow($location_id)) {
            $this->add($this->component()->html('STORE_No location found'))
                ->renderContent();
            return;
        }

        /**
         * @var $shipTable Store_Model_DbTable_Locationships
         */
        $shipTable = Engine_Api::_()->getDbTable('locationships', 'store');
        $select = $shipTable
            ->select()
            ->where('page_id = ?', $page->getIdentity())
            ->where('location_id = ?', $location->getIdentity());
        $ship = $shipTable->fetchRow($select);

        $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));
        $form->removeElement('location');
        $form->getElement('shipping_amt')->setValue($ship->shipping_amt);
        $form->getElement('shipping_days')->setValue($ship->shipping_days);

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $data = $this->getRequest()->getParams();

        if (!$form->isValid($data)) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
        $db->beginTransaction();

        try {
            if ((float)$data['shipping_amt'] <= 0)
                $ship->shipping_amt = null;
            else
                $ship->shipping_amt = (float)$data['shipping_amt'];

            if ((int)$data['shipping_days'] <= 0)
                $ship->shipping_days = 1;
            else
                $ship->shipping_days = (int)$data['shipping_days'];

            $ship->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->view->status = true;
        $this->redirect('parentRefresh');
    }

    public function locationsRemoveAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject('page');
        $location_id = $this->_getParam('location_id');

        /**
         * @var $table    Store_Model_DbTable_Locations
         * @var $location Store_Model_Location
         */
        $table = Engine_Api::_()->getDbTable('locations', 'store');
        if (null == $location = $table->findRow($location_id)) {
            return $this->redirect('parentRefresh', $this->view->translate('STORE_No location found'));
        }

        $form = new Store_Form_Admin_Locations_Remove(array('location' => $location));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getParams())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }
        /**
         * @var $tableShips Store_Model_DbTable_Locationships
         */
        $tableShips = Engine_Api::_()->getDbTable('locationships', 'store');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $tableShips->delete(array('location_id IN (' . $table->getTreeIds($location_id) . ')',
                'page_id = ?' => $page->getIdentity()));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->view->status = true;
        $this->redirect('refresh');
    }

    public function locationsAddAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject('page');
        $parent_id = $this->_getParam('parent_id', 0);

        /**
         * @var $locationApi Store_Api_Location
         * @var $lTable      Store_Model_DbTable_Locations
         */
        $locationApi = Engine_Api::_()->getApi('location', 'store');
        $lTable = Engine_Api::_()->getDbTable('locations', 'store');

        $paginator = $locationApi->getPaginator($page->getIdentity(), $this->_getParam('page', 1), $parent_id, 'supported-add');

        $parent = $lTable->fetchRow($lTable->select()->where('location_id = ?', $parent_id));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        $form = $this->getLocationAddForm($paginator);

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $params = $this->getRequest()->getParams();

        if (count($params['locations']) <= 0) {
            return $this->redirect('parentRefresh');
        }

        $ids = array();
        foreach ($params['locations'] as $id) {
            // Get parent locations
            $tmp_id = $id;

            while (null != ($loc = $lTable->fetchRow($lTable->select()->where('location_id=?', $tmp_id)))) {
                $ids[] = $loc->location_id;
                $tmp_id = $loc->parent_id;
            }

            // Get child locations
            $ids = array_merge($ids, explode(',', $lTable->getTreeIds($id)));
        }

        /**
         * @var $lsTable Store_Model_DbTable_Locationships
         */
        $lsTable = Engine_Api::_()->getDbTable('locationships', 'store');
        $ids = array_unique($ids);

        $db = $lsTable->getDefaultAdapter();
        $db->beginTransaction();

        try {

            // Add location's nodes
            foreach ($ids as $location_id) {
                $lsSelect = $lsTable->select()->where('page_id = ?', 0)->where('location_id = ?', $location_id);
                $existSelect = $lsTable->select()->where('page_id = ?', (int)$page->getIdentity())->where('location_id = ?', $location_id);
                if (
                    (null == ($location = $lsTable->fetchRow($lsSelect)) && null == ($location = $lTable->findRow($location_id))) ||
                    (null != ($lsTable->fetchRow($existSelect)))
                ) continue;

                $lsTable->insert(array(
                    'location_id' => $location->location_id,
                    'page_id' => $page->getIdentity(),
                    'shipping_amt' => $location->shipping_amt,
                    'shipping_days' => $location->shipping_days,
                    'creation_date' => new Zend_Db_Expr('NOW()')
                ));
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->message = $this->view->translate('STORE_Selected locations have been added successfully');
        $this->redirect('parentRefresh');
    }

    /**
     *  Locations Controller
     */


    /**
     *  Statistics Controller
     */
    public function statisticsInit()
    {
        /**
         * @var $page Page_Model_Page
         */
        if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
            Engine_Api::_()->core()->setSubject($page);
        }

        // Set up requires
        $this->_helper->requireSubject('page')->isValid();

        $this->pageObject = $page = Engine_Api::_()->core()->getSubject('page');
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            $this->redirect($page->getHref());
        }

//    $this->navigation = $this->getNavigation(array('statistics'), array('page_id' => $page->getIdentity()));
        $this->navigation = $this->getNavigation(array('transactions'), array('page_id' => $page->getIdentity()));

        $this->addPageInfo('contentTheme', 'd');
    }

    public function statisticsListAction()
    {
        /**
         * @var $page Page_Model_Page
         */
        $page = $this->pageObject;

        $values = array(
            'order' => 'total_amount',
            'order_direction' => 'DESC',
        );

        /**
         * @var $table Store_Model_DbTable_Orderitems
         */
        $table = Engine_Api::_()->getDbtable('orderitems', 'store');

        $select = $table->select()
            ->setIntegrityCheck(false)
            ->from(array('o' => $table->info('name')), array(
                'page_id',
                'item_id',
                'item_type',
                'name',
                'quantity' => 'SUM(qty)',
                'total_amount' => 'SUM(total_amt)'))
            ->where('item_type = ?', 'store_product')
            ->where('page_id = ?', $page->getIdentity())
            ->group('item_id');

        $select->order((!empty($values['order']) ? $values['order'] : 'total_amount') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

        if (!empty($values['name'])) {
            $select->where('name LIKE ?', '%' . $values['name'] . '%');
        }

        /**
         * Make paginator
         *
         * @var $paginator Zend_Paginator
         */
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $values = array();
        foreach ($paginator as $item) {
            $product = $item->getItem();
            $values[] = array(
                'title' => $this->view->translate('ID') . ' (' . $item->item_id . ')',
                'content' => array(
                    array(
                        'label' => $this->view->translate('Title'),
                        'value' => $product ? $this->view->htmlLink($product->getHref(), $this->view->string()->truncate($product->getTitle(), 60), array()) : $item->getTitle()
                    ),
                    array(
                        'label' => $this->view->translate('Gross Amount'),
                        'value' => $this->view->toCurrency($item->total_amount)
                    ),
                    array(
                        'label' => $this->view->translate('Sell Count'),
                        'value' => $item->quantity
                    ),
                )
            );
        }

        $this
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->subjectPhoto($page))
            ->add($this->component()->html($this->view->translate("STORE_VIEWS_SCRIPTS_STATISTICS_LIST_DESCRIPTION")))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->paginator($paginator))
            ->renderContent();
    }

    /**
     *  Statistics Controller
     */


    /**
     *  ProductLocations Controller
     */
    public function productLocationsInit()
    {
        // he@todo this may not work with some of the content stuff in here, double-check
        $product = null;

        if (!Engine_Api::_()->core()->hasSubject('store_product')) {
            if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) ;

            if ($product && $product->getIdentity()) {
                Engine_Api::_()->core()->setSubject($product);
            } else {
                $this->view->status = 0;
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
                return $this->redirect($this->view->url(array('action' => 'index'), 'store_general', true));
            }
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        /**
         * @var $page Page_Model_Page
         */
        $this->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        $this->pageObject = $page = $product->getStore();
        $this->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (
            !$page->isAllowStore() ||
            !($page->getStorePrivacy() || $product->isOwner($viewer))
            // !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
        ) {
            $this->redirect($page->getHref());
        }

        $this->navigation = $this->getNavigation(array('products'), array('page_id' => $page->getIdentity()));
    }

    public function productLocationsIndexAction()
    {
        $parent_id = $this->_getParam('parent_id', 0);
        $page = $this->pageObject;
        /**
         * @var $locationApi Store_Api_Location
         * @var $product     Store_Model_Product
         * @var $table       Store_Model_DbTable_Locations
         * @var $parent      Store_Model_Location
         * @var $locationApi Store_Api_Location
         */
        $locationApi = Engine_Api::_()->getApi('location', 'store');
        $product = Engine_Api::_()->core()->getSubject('store_product');
        $table = Engine_Api::_()->getDbTable('locations', 'store');

        $select = $table->select()->where('location_id = ?', $parent_id);
        $parent = $table->fetchRow($select);

        $paginator = $locationApi->getPaginator($product->page_id, $this->_getParam('page', 1), $parent_id, 'product', $product->getIdentity());
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $quick = $this->getNavigation(array('back'), array(
            'page_id' => $page->getIdentity(),
            'product_id' => $product->getIdentity()
        ));

        $button = $this->dom()->new_('a', array(
            //'href' => $this->view->url(array('action'     => 'add', 'product_id' => $product->getIdentity(), 'parent_id'  => $parent_id), 'store_product_locations', true),
            'href' => $this->view->url(array(), 'store_general', true),
            'data-role' => 'button',
            'data-inline' => 'true',
            'data-mini' => 'true',
            'data-rel' => 'dialog'), $this->view->translate('STORE_Add Locations'));

        $values = array();
        foreach ($paginator as $item) {
            $edit = $this->view->htmlLink($this->view->url(array('action' => 'edit', 'location_id' => $item->getIdentity())), $this->view->translate('STORE_Edit Location'), array(
                'data-role' => 'button',
                'data-inline' => 'true',
                'data-mini' => 'true',
                'data-rel' => 'dialog'
            ));

            $delete = $this->view->htmlLink($this->view->url(array('action' => 'remove', 'location_id' => $item->getIdentity())), $this->view->translate('STORE_Delete Location'), array(
                'data-role' => 'button',
                'data-inline' => 'true',
                'data-mini' => 'true',
                'data-rel' => 'dialog'
            ));

            $editSub = '';
            $subL = '';
            if ($parent_id === 0) {
                $editSub = $this->view->htmlLink($this->view->url(array('parent_id' => $item->getIdentity())), $this->view->translate('STORE_Edit Sub-location'), array(
                    'data-role' => 'button',
                    'data-inline' => 'true',
                    'data-mini' => 'true'
                ));
                $subL = $this->view->translate('STORE_Sub-Locations');
            }

            $buttons = '<div data-role="controlgroup" data-type="horizontal">' . $edit . $delete . $editSub . '</div>';
            $values[] = array(
                'title' => $this->view->translate('STORE_Location Name') . ' (' . $this->view->truncate($item->location, 60) . ')',
                'content' => array(
                    array(
                        'label' => $subL,
                        'value' => ($parent_id === 0) ? $this->view->htmlLink($this->view->url(array(
                                'controller' => 'locations',
                                'page_id' => $page->page_id,
                                'parent_id' => $item->getIdentity()
                            ), 'store_settings', true), (int)$item->sub_locations) : ''
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Shipping Price'),
                        'value' => (is_null($item->shipping_amt)) ? $this->view->translate('STORE_Only Sub-Locations') :
                                $this->view->locale()->toCurrency($item->shipping_amt, $this->view->settings('payment.currency', 'USD'))
                    ),
                    array(
                        'label' => $this->view->translate('STORE_Shipping Days'),
                        'value' => $this->view->locale()->toNumber($item->shipping_days)
                    ),
                    array(
                        'label' => $this->view->translate('Options'),
                        'value' => $buttons
                    ),
                )
            );
        }
        $this->addPageInfo('contentTheme', 'd');
        $this->add($this->component()->subjectPhoto($product))
            ->add($this->component()->html($this->view->translate('STORE_PRODUCT_MANAGE_SHIPPING_LOCATIONS_SETTINGS')))
            ->add($this->component()->html($button))
            ->add(($this->component()->customComponent('fieldsValues', $values)))
            ->add($this->component()->navigation($this->navigation))
            ->add($this->component()->quickLinks($quick))
            ->renderContent();
    }

    public function productLocationsAddAction()
    {
        $parent_id = (int)$this->_getParam('parent_id', 0);

        /**
         * @var $product Store_Model_Product
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');

        /**
         * @var $locationApi Store_Api_Location
         * @var $lTable      Store_Model_DbTable_Locations
         */
        $locationApi = Engine_Api::_()->getApi('location', 'store');
        $lTable = Engine_Api::_()->getDbTable('locations', 'store');

        $paginator = $locationApi->getPaginator($this->pageObject->getIdentity(), $this->_getParam('page', 1), $parent_id, 'product-add', $product->getIdentity());

        $parent = $lTable->fetchRow(array('location_id = ?' => $parent_id));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        $form = $this->getLocationAddForm($paginator);

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->add($this->renderContent());
            return;
        }

        $params = $this->getRequest()->getParams();

        if (count($params['locations']) <= 0) {
            return $this->redirect('parentRefresh');
        }

        $ids = array();
        foreach ($params['locations'] as $id) {
            // Get parent locations
            $tmp_id = $id;
            while (null != ($loc = $lTable->findRow($tmp_id))) {
                $ids[] = $loc->location_id;
                $tmp_id = $loc->parent_id;
            }

            // Get child locations
            $ids = array_merge($ids, explode(',', $lTable->getTreeIds($id)));
        }

        /**
         * @var $psTable Store_Model_DbTable_Productships
         * @var $lsTable Store_Model_DbTable_Locationships
         */
        $psTable = Engine_Api::_()->getDbTable('productships', 'store');
        $lsTable = Engine_Api::_()->getDbTable('locationships', 'store');
        $ids = array_unique($ids);

        $db = $psTable->getDefaultAdapter();
        $db->beginTransaction();

        try {

            // Add location's nodes
            foreach ($ids as $location_id) {
                $lsSelect = $lsTable->select()->where('page_id = ?', 0)->where('location_id = ?', $location_id);
                if (
                    $product->isLocationSupported($location_id) ||
                    (null == ($location = $lsTable->fetchRow($lsSelect)) && null == ($location = $lTable->findRow($location_id)))
                ) continue;

                $psTable->insert(array(
                    'product_id' => $product->getIdentity(),
                    'location_id' => $location->location_id,
                    'shipping_amt' => $location->shipping_amt,
                    'shipping_days' => $location->shipping_days,
                    'creation_date' => new Zend_Db_Expr('NOW()'),
                ));
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->redirect('parentRefresh', $this->view->translate('STORE_Selected locations have been added successfully'));
    }

    public function productLocationsEditAction()
    {
        $location_id = $this->_getParam('location_id');

        /**
         * @var $product Store_Model_Product
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');

        /**
         * @var $psTable  Store_Model_DbTable_Productships
         * @var $location Store_Model_Location
         */
        $psTable = Engine_Api::_()->getDbTable('productships', 'store');
        if (null == ($location = $psTable->getLocation($location_id, $product->getIdentity()))) {
            $this->view->message = $this->view->translate('STORE_No location found');
            return;
        }

        $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));
        $form->removeElement('location');

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $data = $this->getRequest()->getParams();

        if (!$form->isValid($data)) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
        $db->beginTransaction();

        try {
            if ((float)$data['shipping_amt'] <= 0)
                $shipping_amt = null;
            else
                $shipping_amt = (float)$data['shipping_amt'];

            if ((int)$data['shipping_days'] <= 0)
                $shipping_days = 1;
            else
                $shipping_days = (int)$data['shipping_days'];

            $psTable->update(array(
                'shipping_amt' => $shipping_amt,
                'shipping_days' => $shipping_days,
            ), array(
                'location_id = ?' => $location->location_id,
                'product_id = ?' => $location->product_id,
            ));

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->view->status = true;
        $this->redirect('parentRefresh');
    }

    public function productLocationsRemoveAction()
    {
        $location_id = $this->_getParam('location_id');

        /**
         * @var $product Store_Model_Product
         */
        $product = Engine_Api::_()->core()->getSubject('store_product');

        /**
         * @var $psTable  Store_Model_DbTable_Productships
         * @var $location Store_Model_Location
         */
        $psTable = Engine_Api::_()->getDbTable('productships', 'store');
        if (null == ($location = $psTable->getLocation($location_id, $product->getIdentity()))) {
            return $this->redirect('parentRefresh', $this->view->translate('STORE_No location found'));
        }

        $form = new Store_Form_Admin_Locations_Remove(array('location' => $location));

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }
        if (!$form->isValid($this->getRequest()->getParams())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        /**
         * @var $lTable Store_Model_DbTable_Locations
         */
        $lTable = Engine_Api::_()->getDbTable('locations', 'store');
        $db = $psTable->getAdapter();
        $db->beginTransaction();

        try {
            $psTable->delete(array('location_id IN (' . $lTable->getTreeIds($location_id) . ')',
                'product_id = ?' => $product->getIdentity()));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        $this->view->status = true;
        $this->redirect('parentRefresh');
    }

    /**
     *  ProductLocations Controller
     */


    /**
     *  Transaction Controller
     */

    /**
     * @var User_Model_User
     */
    protected $_user;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Store_Model_Gateway
     */
    protected $_gateway;

    /**
     * @var Store_Model_Order
     */
    protected $_order;

    public function transactionInit()
    {
        $this->addPageInfo('contentTheme', 'd');

        // Get user and session
        $this->_user = Engine_Api::_()->user()->getViewer();
        $this->_session = new Zend_Session_Namespace('Store_Transaction');
        if ($ppmeclToken = $this->_getParam('ppmeclToken') && Engine_Api::_()->hasModuleBootstrap('appmanager')) {
            if ((!$this->_user || !$this->_user->getIdentity())) {
                if (Engine_Api::_()->appmanager()->ppMeclLogin())
                    $this->_user = Engine_Api::_()->getDbTable('appmanager', 'tokens')->getUserByPPMeclToken($ppmeclToken);
            }
            $this->_session->__set('ppmeclToken', $ppmeclToken);
        }
        // Check viewer and user
        if (!$this->_user || !$this->_user->getIdentity()) {
            if ($this->_session->__isset('user_id')) {
                $this->_user = Engine_Api::_()->getItem('user', $this->_session->__get('user_id'));
            }
            // If no user, redirect to home?
            $token = Engine_Api::_()->store()->getToken(true);
            if (!$token && (!$this->_user || !$this->_user->getIdentity())) {
                return $this->_transactionRedirector();
            }
        }
        $this->_session->__set('user_id', $this->_user->getIdentity());
        // Get Store order

//        $order_ukey = $this->_session->__get('order_id', $this->_getParam('order_id'));
//        if (!$order_ukey) {
//            $order_ukey = $this->_getParam('order_id');
//        }
        $order_ukey = $this->_getParam('order_id', $this->_session->__get('order_id'));
        if (!$order_ukey || null == ($this->_order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey))
        ) {
            return $this->_transactionRedirector();
        }

        $this->_session->__set('order_id', $this->_order->ukey);

        $mode = Engine_Api::_()->store()->getPaymentMode();

        if ($mode == 'client_store') {
            $gateway = Engine_Api::_()->getItem('store_gateway', $this->_order->gateway_id);
            if ($gateway->getTitle() != 'PayPal') {
                return $this->_transactionRedirector();
            }

            $apisTbl = Engine_Api::_()->getDbTable('apis', 'store');
            $stores = $this->_order->getStores();
            foreach ($stores as $page_id => $store) {
                $api = $apisTbl->getApi($page_id, $this->_order->gateway_id);
                if ($api && $api->enabled) {
                    $this->_gateway = $gateway;
                    break;
                }
            }
        } else {
            // Get Store gateway
            if (!Engine_Api::_()->getDbtable('gateways', 'store')->isGatewayEnabled($this->_order->gateway_id)) {
                return $this->_transactionRedirector();
            }
            $this->_gateway = Engine_Api::_()->getItem('store_gateway', $this->_order->gateway_id);
        }
    }

    public function transactionIndexAction()
    {
        $url = $this->view->url(array('action' => 'process'), 'store_transaction', true);
        $this->redirect($url);
    }

    public function transactionProcessAction()
    {
        $item = $this->_order->getItem();
        $this->add($this->component()->html('<div id="payment_loading">' . $this->view->translate('STORE_Please Wait') . '</div>'));
        if (!($item instanceof Store_Model_Item_Abstract) || $item->getPrice() <= 0) {
            return $this->_transactionRedirector();
        }
        // Unset unnecessary values
        $this->_session->__unset('order_id');
        $this->_session->__unset('errorMessage');
        $this->_session->__unset('token');

        /**
         * Make the order unique
         */
        $this->_order->updateUkey();
        $this->_session->__set('order_id', $this->_order->ukey);

        // Get gateway plugin

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];
        $ukey = $this->_order->ukey;

        // Prepare transaction
        $params = array();
        if ($this->_user->getIdentity()) {
            $params['language'] = $this->_user->language;
            $localeParts = explode('_', $this->_user->language);
            if (count($localeParts) > 1) {
                $params['region'] = $localeParts[1];
            }
        }

        $params['vendor_order_id'] = $ukey;
        $params['return_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $ukey
            . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $ukey
            . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
            . $this->view->url(array('action' => 'index',
                'controller' => 'ipn',
                'module' => 'store',
            ), 'default', true)
            . '?order_id=' . $ukey
            . '&state=' . 'ipn';

        /**
         * Get gateway plugin
         *
         * @var $plugin Experts_Payment_Plugin_Abstract
         */

        $gatewayPlugin = $this->_gateway->getGateway();
        $plugin = $this->_gateway->getPlugin();
        try {
            $transaction = $plugin->createCartTransaction($this->_order, $params);

        } catch (Exception $e) {

            if ('development' == APPLICATION_ENV) {
                if ('development' == APPLICATION_ENV)
                    throw $e;
            } elseif (in_array($e->getCode(), array(10736, 10731))) {
                $this->_session->__set('errorMessage', array(
                    'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR',
                    $this->view->translate(
                        'STORE_TRANSACTION_REPORT_FORM %1$scontact%2$s',
                        '<a href="javascript:void(0);" onclick="goToContactPageAfterError();return false;">',
                        '</a>'
                    ),
                    $e->getMessage()
                ));
                $this->_session->__set('errorName', $e->getCode());
            } else {
                $this->_session->__set('errorMessage', 'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR');
                print_log($e->__toString());
            }
            return $this->_finishPayment('failed');
        }

        $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $transactionData = $transaction->getData();
        if ($this->_session->__isset('ppmeclToken'))
            $transactionData['drt'] = $this->_session->__get('ppmeclToken');
        // Pull transaction params
        $this->page['transaction_params'] = array(
            'transactionUrl' => $transactionUrl,
            'transactionMethod' => $transactionMethod,
            'transactionData' => Zend_Json::encode($transactionData)
        );

        $form = new Engine_Form(
            array(
                'name' => 'transaction_form',
                'id' => 'transaction_form',
                'method' => 'post',
                'style' => 'display: none',
                'action' => $transactionUrl)
        );

        $order = 0;
        foreach ($transactionData as $key => $value) {
            $form->addElement('hidden',
                $key,
                array(
                    'value' => $value,
                    'order' => $order--
                )
            );
        }

        $this->add($this->component()->form($form));
        if (!$transaction->isValid()) {
            if ('development' == APPLICATION_ENV) {
                throw new Engine_Exception('Transaction is invalid');
            }
            return $this->_finishPayment('failed');
        }

        $this->_session->lock();

        // Handle redirection
        if ($transactionMethod == 'GET') {
            $transactionUrl .= '?' . http_build_query($transactionData);
            $this->redirect($transactionUrl);
        }
        $this->attrPage('data-role', 'dialog')->renderContent();
        // Post will be handled by the view script
    }

    public function transactionReturnAction()
    {
        /**
         * Get gateway plugin
         *
         * @var $plugin Experts_Payment_Plugin_Abstract
         */
        try {
            $plugin = $this->_gateway->getPlugin();
            try {
                $status = $plugin->onCartTransactionReturn($this->_order, $this->_getAllParams());
            } catch (Store_Model_Exception $e) {
                $this->_session->__set('errorMessage', $e->getMessage());
                $status = 'failed';
            }
        } catch (Exception $e) {
            if ('development' == APPLICATION_ENV) {
                throw $e;
            }
            $status = 'failed';
        }

        $this->_finishPayment($status);
    }

    protected function _finishPayment($status = 'completed')
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Log the user in, if they aren't already
        if (($status == 'completed') &&
            $this->_user &&
            !$this->_user->isSelf($viewer) &&
            !$viewer->getIdentity()
        ) {
            Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
            Engine_Api::_()->user()->setViewer();
            $viewer = $this->_user;
        }

        // Handle email verification or pending approval
        if ($viewer->getIdentity() && !$viewer->enabled) {
            Engine_Api::_()->user()->setViewer(null);
            Engine_Api::_()->user()->getAuth()->getStorage()->clear();
            $this->redirect($this->view->url(array('action' => 'confirm'), 'user_signup', true));
        }

        // Clear session
        $errorMessage = $this->_session->__get('errorMessage');
        $errorName = $this->_session->__get('errorName');
        $this->_session->unsetAll();
        $this->_session->__set('order_id', $this->_order->ukey);
        $this->_session->__set('user_id', $viewer->getIdentity());
        $this->_session->__set('errorMessage', $errorMessage);
        $this->_session->__set('errorName', $errorName);
        // Redirect

        return $this->_helper->redirector->gotoRoute(array('action' => 'finish',
            'status' => $status));
//    $this->redirect($this->view->url(array('action' => 'finish', 'status' => $status)));
    }

    public function transactionFinishAction()
    {
        $status = $this->_getParam('status');
        $caption = $this->view->translate('Undefined caption.');
        $description = $this->view->translate('Undefined description.');
        $error = $this->view->translate('Error name.');
        if (in_array($status, array('completed', 'shipping', 'processing'))) {
            switch ($status) {
                case 'completed':
                    $caption = $this->view->translate('Payment Complete');
                    $description = $this->view->translate('STORE_PAYMENT_PENDING_THANK_YOU');
                    break;
                case 'shipping':
                    $caption = $this->view->translate('Payment Complete');
                    $description = $this->view->translate('Thank you! Your payment has completed successfully.');
                    break;
                case 'processing':
                    $caption = $this->view->translate('Payment Pending');
                    $description = $this->view->translate('STORE_PAYMENT_PENDING_THANK_YOU');
                    break;

            }
            $url = $this->view->escape($this->view->url(array('order_id' => $this->_order->ukey), 'store_purchase', true));
        } else {
            $button = $this->view->translate('Back to Cart');
            $caption = $this->view->translate('Payment Failed');
            $url = $this->view->escape($this->view->url(array('controller' => 'cart'), 'store_extended', true));

            if (!$this->_session->__isset('errorMessage')) {
                $description = $error = 'There was an error processing your transaction. Please try again later.';
            } else {
                $description = $error = $this->view->translate($this->_session->__get('errorMessage'));
                $errorName = $this->_session->__get('errorName');
            }
            if (empty($error)) {
                $description = $error = $this->view->translate('Our payment processor has notified ' .
                    'us that your payment could not be completed successfully. ' .
                    'We suggest that you try again with another credit card ' .
                    'or funding source.');
            }
            $string = '';
            if (is_array($error)) {
                foreach ($error as $err)
                    $string .= "<p>{$err}</p>";
                $description = $error = $string;
            }
        }


        $params = array(
            'url' => $url,
            'status' => $status,
            'error' => $error,
            'errorName' => $errorName,
            'caption' => $caption,
            'description' => $description
        );
        $this
            ->clearClientCache()
            ->add($this->component()->transactionFinish($params))
            ->renderContent();
    }

    protected function _transactionRedirector()
    {
        $this->_session->unsetAll();
        return $this->_helper->redirector->gotoRoute(array('controller' => 'cart'), 'store_extended', true);
//    $this->redirect($this->view->url(array('controller' => 'cart'), 'store_extended', true));
    }

    /**
     *  Transaction Controller
     */

    public function browseProductList(Core_Model_Item_Abstract $item)
    {
        /**
         * @var $settings Core_Model_DbTable_Settings
         */
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $allowFree = $settings->getSetting('store.free.products', 0);

        $description = '';
        $quantity = false;
        if ($item->type == 'simple') {
            $description = $this->view->translate(
                array('%s item available', '%s items available', (int)$item->getQuantity()),
                $this->view->locale()->toNumber($item->getQuantity()));
            $quantity = true;
        }
        if ($item->isFree() && $allowFree) {
            $option = array(
                'label' => $this->view->translate('Download Free Product'),
                'attrs' => array(
                    //'onclick' => "Initializer.store.add_to_cart({$item->getIdentity()}, '{$href}')",
                    'href' => $this->view->url(array('id' => $item->getIdentity()), 'store_download_free'),
                    'class' => 'buttonlink',
                    'data-icon' => 'download',
                    'data-ajax' => false
                )
            );
        } else {
            $href = $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'add', 'product_id' => $item->getIdentity()), 'default', true);
            $option = array(
                'label' => $this->view->translate('Add to Cart'),
                'attrs' => array(
                    'onclick' => "Initializer.store.add_to_cart({$item->getIdentity()}, '{$href}')",
                    'href' => $href,
                    'class' => 'buttonlink',
                    'data-icon' => $item->isAddedToCart() ? 'minus' : 'plus'
                )
            );
        }
        $options = array($option);

        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $price_block = "<a class='price-block' href=\"$href\" data-rel='dialog'><i class=\"icon-tag\"></i>" . $this->view->getPrice($item) . "</a>";
        } else {
            $price_block = "<div class='price-block'><i class=\"icon-tag\"></i>" . $this->view->getPrice($item) . "</div>";
        }
        if ($item->sponsored) {
            $price_block .= '<i class="product-priority icon-certificate"></i>';
        }
        if ($item->featured) {
            $price_block .= '<i class="product-priority icon-star"></i>';
        }
        $customize_fields = array(
            'title' => $item->getTitle(),
            'creation_date' => '',
            'href' => $item->getHref(),
            'descriptions' => array(
                $this->view->string()->truncate($item->getDescription(), 250, '...'),
                $description,
                $price_block
            ),
            'manage' => $options
        );

        return $customize_fields;
    }


    private function cart()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('store_cart');

        if (!$viewer->getIdentity()) return;

        if (null == ($cart = $table->getCart($viewer->getIdentity())) || !$cart->hasItem()) {
            $cartItemsCount = 0;
            $total = 0;
        } else {
            $cartItemsCount = count($cart->getItems());
            $total = $cart->getPrice();
        }
        $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
        $isStoreEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store');
        $cartBtn = '';
        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        if ($isPageEnabled && $isStoreEnabled) {
            $cartBtn = $this->dom()->new_('a', array(
                'data-role' => 'button',
                'data-icon' => 'shopping-cart',
                'data-theme' => 'a',
                'data-inline' => true,
                'data-transition' => 'slide',
                'data-iconpos' => 'left',
                'data-direction' => 'reverse',
                'href' => $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'index'), 'default', true),
                'class' => 'cart-btn',
                'id' => 'cart-btn'
            ), 'Items - ' . $cartItemsCount . ', Total Price - ' . $this->view->locale()->toCurrency($total, $currency));
        }

        $controlgroup = $this->dom()->new_('div', array(
            'data-role' => 'controlgroup',
            'data-mini' => true,
            'data-type' => 'horizontal',
            'class' => 'notifier-group'
        ), '', array(
            $cartBtn
        ));
        $this->add($this->component()->html($controlgroup));
    }

    public function panelStoreList(Core_Model_Item_Abstract $page)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $options = array();
        $options[] = array(
            'label' => $this->view->translate('STORE_Manage Products'),
            'attrs' => array(
                'href' => $this->view->url(array('page_id' => $page->page_id), 'store_products'),
                'class' => 'buttonlink',
            )
        );
        if ($page->isOwner($viewer)) {
            $options[] = array(
                'label' => $this->view->translate('Transactions'),
                'attrs' => array(
                    'href' => $this->view->url(array('controller' => 'transactions', 'page_id' => $page->page_id), 'store_extended'),
                    'class' => 'buttonlink',
                )
            );
            $options[] = array(
                'label' => $this->view->translate('Request Money'),
                'attrs' => array(
                    'href' => $this->view->url(array('controller' => 'requests', 'page_id' => $page->page_id), 'store_extended'),
                    'class' => 'buttonlink',
                )
            );
            $options[] = array(
                'label' => $this->view->translate('Settings'),
                'attrs' => array(
                    'href' => $this->view->url(array('action' => 'gateway', 'page_id' => $page->page_id), 'store_settings'),
                    'class' => 'buttonlink',
                )
            );
            $options[] = array(
                'label' => $this->view->translate('Statistics'),
                'attrs' => array(
                    'href' => $this->view->url(array('page_id' => $page->page_id, 'action' => 'list'), 'store_statistics'),
                    'class' => 'buttonlink',
                )
            );
        }

        $productsTbl = Engine_Api::_()->getDbTable('products', 'store');
        $count = $productsTbl->getProducts(array('count' => 1,
            'page_id' => $page->page_id,
            'quantity' => true));
        $count = ($count) ? $count : 0;

        $count = $this->view->translate(
            array('%s product', '%s products', $count),
            $this->view->locale()->toNumber($count)
        );

        $customize_fields = array(
            'counter' => $count,
            'manage' => $options
        );

        return $customize_fields;
    }

    private function _getPurchasedProductHref($id = null)
    {
        if (!$id) return $this->fileNotFound();
        /**
         * Declare Variables
         *
         * @var $viewer  User_Model_User
         * @var $item    Store_Model_Orderitem
         * @var $product Store_Model_Product
         * @var $order   Store_Model_Order
         */
        $viewer = Engine_Api::_()->user()->getViewer();

        if (
            !$viewer->getIdentity() ||
            (null == ($item = Engine_Api::_()->getItem('store_orderitem', $id))) ||
            !$item->isDownloadable() ||
            (null == ($product = $item->getItem())) ||
            (null == ($order = $item->getParent())) ||
            !$order->isOwner($viewer) ||
            (null == ($storage = $product->getFile()))
        ) {
            return $this->fileNotFound();
        }


        if (!($storage instanceof Storage_Model_File))
            return $this->fileNotFound($order->getIdentity());

        // Process the file
        $file = APPLICATION_PATH . DS . $storage->storage_path;
        if (!is_file($file)) {
            return $this->fileNotFound($order->getIdentity());
        } else {
            return $file;
        }
    }

    public function panelProductList(Core_Model_Item_Abstract $item)
    {
        $product = $item->getItem();
        $download = '';
        $options = array();
        $option = array(
            'label' => $this->view->translate('STORE_Download'),
            'attrs' => array(
//        'href'      => $this->view->url(array('id' => $item->getIdentity()), 'store_download', true),
                'onclick' => 'window.open("' . $this->view->url(array('id' => $item->getIdentity()), 'store_download', true) . '", "_system")',
//                'href'      => $this->_getPurchasedProductHref($item->getIdentity()),
                'data-icon' => 'download-alt',
                'data-ajax' => false,
                'target' => '_blank'
            )
        );
        if ($item->getProduct()->type == 'digital') {
            $options[] = $option;
        } else {
            if ($item->status != 'shipping' && $item->status != 'pending' && $item->status != 'processing') {
                $options[] = $option;
            }
        }

        $customize_fields = array(
            'title' => $product->getTitle(),
            'creation_date' => '',
            'descriptions' => array($this->view->getPrice($product)),
            'counter' => '',
            'photo' => $product->getPhotoUrl('thumb.normal'),
            'manage' => $options
        );

        return $customize_fields;
    }

    public function productsManageList(Core_Model_Item_Abstract $item)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $options = array();
        if ($item->owner_id == $viewer->getIdentity() || $item->getPage()->isOwner($viewer)) {
            $options[] = array(
                'label' => $this->view->translate('STORE_Edit Product'),
                'attrs' => array(
                    'href' => $this->view->url(array('action' => 'edit', 'product_id' => $item->getIdentity()), 'store_products'),
                    'class' => 'buttonlink',
                )
            );
            $options[] = array(
                'label' => $this->view->translate('Delete Product'),
                'attrs' => array(
                    'href' => $this->view->url(array('action' => 'delete', 'product_id' => $item->getIdentity()), 'store_products'),
                    'class' => 'buttonlink',
                    'data-rel' => 'dialog'
                )
            );
            $options[] = array(
                'label' => $this->view->translate('STORE_Copy Product'),
                'attrs' => array(
                    'href' => $this->view->url(array('action' => 'copy', 'product_id' => $item->getIdentity()), 'store_products'),
                    'class' => 'buttonlink',
                )
            );
        }

        $description = '';
        if ($item->type == 'simple') {
            $description = $this->view->translate(
                array('%s item available', '%s items available', (int)$item->getQuantity()),
                $this->view->locale()->toNumber($item->getQuantity()));
        }
        $customize_fields = array(
            'title' => $item->getTitle(),
            'creation_date' => null,
            'counter' => $this->view->getPrice($item),
            'descriptions' => array($description),
            'manage' => $options
        );

        return $customize_fields;
    }

    public function photoManageList(Core_Model_Item_Abstract $item)
    {
        $photo_id = $item->getIdentity();
        $options = array();

        $options[] = array(
            'label' => $this->view->translate('Edit Photo'),
            'attrs' => array(
                'href' => $this->view->url(array('controller' => 'photo', 'action' => 'manage', 'product_id' => $this->product->getIdentity(), 'photo_id' => $photo_id, 'action_type' => 'edit', 'no_cache' => rand(0, 1000)), 'store_extended', true),
                'class' => 'buttonlink'
            ),
        );

        $options[] = array(
            'label' => $this->view->translate('Set as Main Photo'),
            'attrs' => array(
                'href' => $this->view->url(array('controller' => 'photo', 'action' => 'manage', 'product_id' => $this->product->getIdentity(), 'photo_id' => $photo_id, 'action_type' => 'cover', 'no_cache' => rand(0, 1000)), 'store_extended', true),
                'class' => 'buttonlink'
            )
        );

        $options[] = array(
            'label' => $this->view->translate('Delete Photo'),
            'attrs' => array(
                'href' => $this->view->url(array('controller' => 'photo', 'action' => 'remove', 'product_id' => $this->product->getIdentity(), 'photo_id' => $photo_id, 'no_cache' => rand(0, 1000)), 'store_extended', true),
                'class' => 'buttonlink'
            )
        );

        $customize_fields = array(
            'creation_date' => null,
            'manage' => $options
        );

        return $customize_fields;
    }

    public function cartProductList(Core_Model_Item_Abstract $item)
    {
        $product = $item->getProduct();

        $options = array();
        $href = $this->view->url(array('controller' => 'cart', 'action' => 'remove', 'product_id' => $product->getIdentity(), 'item_id' => $item->getIdentity()), 'store_extended', true);
        $options[] = array(
            'label' => $this->view->translate('Remove'),
            'attrs' => array(
                'data-icon' => 'delete',
                'href' => $href,
                'class' => 'buttonlink'
            ),
        );
        $quantity = '';
        if ($product->type == 'simple') {
            $quantity = $this->dom()->new_('div', array('class' => 'store_products_count'), $this->view->translate('STORE_Quantity') . ': ' . $item->qty, array());
        }

        $desc = $this->view->getPrice($product) . $quantity;

        if (!$item->isCheckable()) {
            $desc = "<span style='color: red; font-weight: bold;'>" .
                $this->view->translate('STORE_Purchasing is not possible. %1s', $this->view->htmlLink(array(
                        'route' => 'store_extended',
                        'controller' => 'cart',
                        'action' => 'see-details',
                        'item_id' => $item->getIdentity(),
                    ),
                    $this->view->translate('See Details')
                )) .
                "</span>";
        }

        $customize_fields = array(
            'title' => $product->getTitle(),
            'creation_date' => '',
            'descriptions' => array($desc),
            'photo' => $product->getPhotoUrl('thumb.normal'),
            'manage' => $options
        );

        return $customize_fields;
    }

    public function getNavigation($type = null, $params = array())
    {
        $menu = $this->_getParam('action', 'index');

        $navigation = new Zend_Navigation();
        $isPageEnabled = (
        Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')
            //$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()
        );

        if (in_array('panel', $type)) {
            $navigation->addPages(array(
                array(
                    'label' => "My Purchases",
                    'route' => 'store_panel',
                    'action' => 'purchases',
                    'class' => (in_array($menu, array('purchase', 'purchases'))) ? 'active' : '',
                    'data_attrs' => ''
                ),
                array(
                    'label' => "My Wishlist",
                    'route' => 'store_panel',
                    'action' => 'wish-list',
                    'class' => ($menu == 'wish-list') ? 'active' : '',
                    'data_attrs' => ''
                ),
                array(
                    'label' => "Shipping Details",
                    'route' => 'store_panel',
                    'action' => 'address',
                    'class' => ($menu == 'address') ? 'active' : '',
                    'data_attrs' => ''
                )
            ));

            if ($isPageEnabled) {
                $navigation->addPages(array(
                        array(
                            'label' => "Manage Stores",
                            'route' => 'store_panel',
                            'class' => ($menu == 'index') ? 'active' : '',
                            'data_attrs' => ''
                        ),
                        array(
                            'label' => "Create New Store",
                            'route' => 'page_create',
                            'target' => '_blank',
                            'data_attrs' => ''
                        ))
                );
            }

        }
        if (in_array('products', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Edit Products",
                        'route' => 'store_products',
                        'params' => array('page_id' => $params['page_id']),
                        'data_attrs' => ''
                    )
                )
            );
        }
        if (in_array('create', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "STORE_Add New Product",
                        'route' => 'store_products',
                        'action' => 'create',
                        'params' => array('page_id' => $params['page_id']),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('manage', $type)) {
            $product = Engine_Api::_()->getItem('store_product', $params['product_id']);

            if ($product->isDigital()) {
                $navigation->addPages(array(
                        array(
                            'label' => "STORE_Manage File",
                            'route' => 'store_extended',
                            'controller' => 'digital',
                            'action' => 'edit-file',
                            'params' => array('product_id' => $params['product_id']),
//              'data_attrs' => ''
                        )
                    )
                );
            } else {
                $navigation->addPages(array(
                        array(
                            'label' => "STORE_Manage Shipping Locations",
                            'route' => 'store_settings',
                            'params' => array('controller' => 'locations', 'page_id' => $params['page_id']),
                            //'data_attrs' => ''
                        ),
                    )
                );
            }

            $navigation->addPages(array(
                    array(
                        'label' => "STORE_Manage photos",
                        'route' => 'store_extended',
                        'controller' => 'photo',
                        'action' => 'edit',
                        'params' => array('product_id' => $params['product_id']),
                        //'data_attrs' => ''
                    ),
                    array(
                        'label' => "STORE_Manage video",
                        'route' => 'store_extended',
                        'controller' => 'video',
                        'action' => 'edit',
                        'params' => array('product_id' => $params['product_id']),
                        //'data_attrs' => ''
                    ),
                    array(
                        'label' => "STORE_Manage audios",
                        'route' => 'store_extended',
                        'controller' => 'audios',
                        'action' => 'edit',
                        'params' => array('product_id' => $params['product_id']),
                        //'data_attrs' => ''
                    )
                )
            );

        }
        if (in_array('video_delete', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Delete Video",
                        'route' => 'store_extended',
                        'controller' => 'video',
                        'action' => 'delete',
                        'params' => array('product_id' => $params['product_id']),
                        'data_attrs' => array('data-rel' => 'dialog'),
                    ))
            );
        }
        if (in_array('back', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Back",
                        'route' => 'store_products',
                        'action' => 'edit',
                        'params' => array('product_id' => $params['product_id'], 'page_id' => $params['page_id']),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('video_view', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Play Video",
                        'route' => 'store_profile',
                        'params' => array(
                            'product_id' => $params['product_id'],
                            'title' => $params['title'],
                            'tab' => 'video'
                        ),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('photo_add', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Add Photos",
                        'route' => 'store_extended',
                        'controller' => 'photo',
                        'action' => 'add',
                        'params' => array('product_id' => $params['product_id']),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('audio_add', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Add Audios",
                        'route' => 'store_extended',
                        'controller' => 'audios',
                        'action' => 'create',
                        'params' => array('product_id' => $params['product_id']),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('transactions', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Edit Products",
                        'route' => 'store_products',
                        'params' => array('page_id' => $params['page_id']),
                        'data_attrs' => ''
                    ),
                )
            );

            $page = Engine_Api::_()->getItem('page', $params['page_id']);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($page->isOwner($viewer)) {
                $navigation->addPages(array(
                        array(
                            'label' => "Transactions",
                            'route' => 'store_extended',
                            'controller' => 'transactions',
                            'params' => array('page_id' => $params['page_id']),
                            //'data_attrs' => ''
                        ),
                        array(
                            'label' => "Request Money",
                            'route' => 'store_extended',
                            'controller' => 'requests',
                            'params' => array('page_id' => $params['page_id']),
                            //'data_attrs' => ''
                        ),
                        array(
                            'label' => "Settings",
                            'route' => 'store_settings',
                            'action' => 'gateway',
                            'params' => array('page_id' => $params['page_id']),
                            //'data_attrs' => ''
                        ),
                        array(
                            'label' => "Statistics",
                            'route' => 'store_statistics',
                            'action' => 'list',
                            'params' => array('page_id' => $params['page_id']),
                            //'data_attrs' => ''
                        ),
                    )
                );
            }
        }
        if (in_array('settings', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "Gateway",
                        'route' => 'store_settings',
                        'action' => 'gateway',
                        'params' => array('page_id' => $params['page_id']),
                        //'data_attrs' => ''
                    ),
                    array(
                        'label' => "Locations",
                        'route' => 'store_settings',
                        'controller' => 'locations',
                        'params' => array('page_id' => $params['page_id']),
                        //'data_attrs' => ''
                    ),
                )
            );
        }
        if (in_array('statistics', $type)) {
            $navigation->addPages(array(
//        array(
//          'label'  => "Chart Statistic",
//          'route'  => 'store_statistics',
//          'action' => 'chart',
//          'params' => array('page_id' => $params['page_id']),
//          'data_attrs' => ''
//        ),
                array(
                    'label' => "List Statistic",
                    'route' => 'store_statistics',
                    'action' => 'list',
                    'params' => array('page_id' => $params['page_id']),
                    'data_attrs' => ''
                )
            ));
        }
        if (in_array('prod_loc_add', $type)) {
            $navigation->addPages(array(
                    array(
                        'label' => "STORE_Add Locations",
                        'route' => 'store_settings',
                        'action' => 'add',
                        'params' => array('controller' => 'locations', 'page_id' => $params['page_id']),
                        //'data_attrs' => ''
                    ))
            );
        }
        if (in_array('prod_profile', $type)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $subject = Engine_Api::_()->core()->getSubject();

            if ($subject->isOwner($viewer) || ($subject->getStore() && $subject->getStore()->isOwner($viewer))) {
                if (null != ($page = Engine_Api::_()->getItem('page', $subject->page_id))) {
                    $navigation->addPages(array(
                            array(
                                'label' => "STORE_Edit Product",
                                'route' => 'store_products',
                                'action' => 'edit',
                                'params' => array('page_id' => $page->getIdentity(), 'product_id' => $subject->getIdentity()),
                                //'data_attrs' => ''
                            ))
                    );
                }
            }

            if (null != ($page = Engine_Api::_()->getItem('page', $subject->page_id))) {
                $navigation->addPages(array(
                        array(
                            'label' => "STORE_Back to Store",
                            'route' => 'page_view',
                            'params' => array('page_id' => $page->url),
                            //'data_attrs' => ''
                        ),
                        array(
                            'label' => "STORE_Back to Products",
                            'route' => 'store_general',
                            'action' => 'products',
                            'params' => array(),
                            //'data_attrs' => ''
                        ),
                    )
                );
            }

            if ($viewer && $viewer->getIdentity()) {
                $navigation->addPages(array(
                        array(
                            'label' => "Share Product",
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'share',
                            'params' => array(
                                'type' => $subject->getType(),
                                'id' => $subject->getIdentity(),
                            ),
                            'data_attrs' => array('data-rel' => 'dialog')
                        ),
                    )
                );
            }

            if ($subject->isOwner($viewer) || ($subject->getStore() && $subject->getStore()->isOwner($viewer))) {
                $navigation->addPages(array(
                        array(
                            'label' => 'Delete',
                            'route' => 'default',
                            'module' => 'store',
                            'controller' => 'product',
                            'action' => 'delete',
                            'params' => array('product_id' => $subject->getIdentity()),
                            'data_attrs' => array('data-rel' => 'dialog')
                        ),
                    )
                );

            }
        }

        return $navigation;
    }

    public function extractCode($url, $type)
    {
        switch ($type) {
            //youtube
            case "1":
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url = preg_replace("/#!/", "?", $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                $code = "code";
                $parameters = $arr["query"];
                parse_str($parameters, $data);
                $code = $data['v'];
                if ($code == "") {
                    $code = $new_code['basename'];
                }

                return $code;
            //vimeo
            case "2":
                // get the first variable after slash
                $code = @pathinfo($url);
                return $code['basename'];
        }
    }

    // YouTube Functions
    public function checkYouTube($code)
    {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

        if (!$data = @file_get_contents($prefix . "gdata.youtube.com/feeds/api/videos/" . $code)) return false;
        if ($data == "Video not found") return false;
        return $data;
    }

    // Vimeo Functions
    public function checkVimeo($code)
    {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
        $data = @simplexml_load_file($prefix . "vimeo.com/api/v2/video" . $code . ".xml");
        $id = count($data->video->id);
        if ($id == 0) return false;
        return true;
    }

    protected function fileNotFound($order_id = 0)
    {
        $this->view->message = $this->view->translate('STORE_Sorry, we could not find requested download file.');

        $this->add($this->component()->html($this->view->message));

        if (!$order_id) {
            return;
        }
        $this->redirect($this->view->url(array('action' => 'transactions', 'order_id' => $order_id), 'store_panel', true));
    }

    protected function getInfoForm($product)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $allowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');

        $form = new Engine_Form();
        if ($allowOrder) {
            if (!$product->isAddedToCart()) {
                if ($product->type == 'simple') {
                    $form->addElement('Text', 'quantity', array(
                        'label' => 'STORE_Quantity',
                        'description' => $this->view->translate(array('%s item available', '%s items available', (int)$product->getQuantity()), $this->view->locale()->toNumber($product->getQuantity()))
                    ));

                    if (is_array($product->params) && count($product->params) > 0) {
                        foreach ($product->params as $param) {
                            $options = (isset($param['options'])) ? explode(',', $param['options']) : array();
                            $multiOptions = array('-1' => 'STORE_-Select-');
                            foreach ($options as $option) {
                                $multiOptions[trim($option)] = trim($option);
                            }

                            $form->addElement('Select', $param['label'], array(
                                'label' => $param['label'],
                                'multiOptions' => $multiOptions
                            ));
                        }
                    }
                } else {
                    $form->addElement('Hidden', 'quantity', array(
                        'value' => 1,
                    ));
                }
                $form->addElement('Button', 'add_to', array(
                    'type' => 'submit',
                    'label' => 'STORE_Add to Cart'
                ));
            } else {
                $form->addElement('Button', 'remove_from', array(
                    'type' => 'submit',
                    'label' => 'STORE_Remove from Cart'
                ));
            }
        }

        return $form;
    }

    protected function getLocationAddForm(Zend_Paginator $paginator)
    {
        $form = new Engine_Form();
        $form->setTitle('STORE_Location Name');
        $options = array();
        foreach ($paginator as $item) {
            $options[$item->location_id] = $this->view->truncate($item->location, 60);
        }
        if (!empty($options))
            $form->addElement('MultiCheckbox', 'locations', array(
                'multiOptions' => $options,
            ));

        $form->addElement('Button', 'add_locations', array(
            'label' => 'STORE_Add Locations',
            'type' => 'submit'
        ));

        return $form;
    }

    protected function _checkRequiredSettings()
    {
        $error = false;
        $page_id = $this->pageObject->getIdentity();
        $this->gatewaysEnabled = $gatewaysEnabled = (int)Engine_Api::_()->getDbTable('apis', 'store')->getEnabledGatewayCount($page_id);
        if (!$gatewaysEnabled) {
            $error = true;
        }
        $this->hasShippingLocations = $hasShippingLocations = Engine_Api::_()->getDbTable('locationships', 'store')->hasShippingLocations($page_id);
        if (!$hasShippingLocations) {
            $error = true;
        }
        $this->error = $error;
    }

    private function _showCart()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('store_cart');

        $token = Engine_Api::_()->store()->getToken();
        if (!$viewer->getIdentity() && !$token) {
            return;
        }

        if (null == ($cart = $table->getCart($viewer->getIdentity())) || !$cart->hasItem()) {
            $cartItemsCount = 0;
            $total = 0;
        } else {
            $cartItemsCount = count($cart->getItems());
            $total = $cart->getPrice();
        }
        $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
        $isStoreEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store');
        $cartBtn = '';
        $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
        $total = $this->view->locale()->toCurrency($total, $currency);
        $items = array();
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();
            /**
             * @var $product Store_Model_Product
             */
            $href = $this->view->url(array('controller' => 'cart', 'action' => 'remove', 'product_id' => $product->getIdentity(), 'item_id' => $item->getIdentity()), 'store_extended', true);
            $items[] = $this->dom()->new_('li', array(), '', array(
                $this->dom()->new_('a', array('href' => $product->getHref()), '', array(
                    $this->dom()->new_('img', array('src' => $product->getPhotoUrl('thumb.icon'))),
                    $this->dom()->new_('h3', array(), $product->getTitle()),
                    $this->dom()->new_('p', array(), $product->getDescription()),
                    $this->dom()->new_('span', array('class' => 'ui-li-count'), $this->view->locale()->toCurrency($product->getPrice(), $currency)),
                )),
                $this->dom()->new_('a', array(
                    'data-icon' => 'remove-sign',
                    'href' => $href,
                    'onclick' => "Initializer.store.removeFromCart(this)",
                    'data-theme' => 'c',
                ), 'Remove'),
            ));
        }

        $items[] = $this->dom()->new_('li', array('data-role' => 'list-divider'), ucfirst($this->view->translate('STORE_total')) . ':' . $total, array());
        $list = $this->dom()->new_('ul', array(
            'data-role' => 'listview',
        ), '', $items);

        $cartEl = $this->dom()->new_('div', array(
            'data-role' => 'collapsible',
            'data-theme' => 'b',
            'class' => 'store-cart',
            'data-content-theme' => 'c',
            'data-collapsed-icon' => 'shopping-cart',
            'data-expanded-icon' => 'arrow-u',
        ), '', array(
            $this->dom()->new_('h2', array(), '<span class="item-count">' . $cartItemsCount . '</span> ' . $this->view->translate('Cart') . ' <span>' . $total . '</span>'),
            $list,
            $this->dom()->new_('br'),
        ));
        if ($cartItemsCount) {
            $coBtn = $this->dom()->new_('div', array('class' => 'ui-grid-solo'), '', array(
                $this->dom()->new_('div', array('class' => 'ui-block-a'), '', array(
                    $this->dom()->new_('a', array(
                            'data-role' => 'button',
                            'data-icon' => 'credit-card',
                            'data-theme' => 'c',
                            'href' => $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'index'), 'default', true),
                            'class' => 'ui-block-a'
                        ),
                        $this->view->translate('STORE_Checkout'), array())
                )),
            ));
            $cartEl->append($coBtn);
        } else {
            $emptyCartInf = $this->dom()->new_('div', array('class' => 'ui-grid-solo'), '', array(
                $this->dom()->new_('div', array('class' => 'ui-block-a'), '', array(
                    $this->dom()->new_('div', array(
                            'class' => 'ui-bar ui-bar-e'
                        ),
                        $this->view->translate('STORE_Your cart is empty'), array())
                )),
            ));
            $cartEl->append($emptyCartInf);
        }
        return $this->add($this->component()->html($cartEl));
    }
}

?>

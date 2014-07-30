<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="headline">
  <h4>
    &raquo; <?php echo $this->translate('Classified Listings');?>
  </h4>
</div>

<?php if( count($this->navigation) > 0 ): ?>
<div class="tabs">
  <ul>
    <?php foreach( $this->navigation as $item ): ?>

      <?php if ($item->active):?>
      <li class="active">
        <a href="<?php echo $item->getHref(); ?>">
          <?php echo $this->translate($item->getLabel()) ?>
          <img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
        </a>
      </li>
      <li class="content">

        <div class='layout_right'>
          <?php echo $this->form->render($this) ?>

          <?php if( count($this->quickNavigation) > 0 ): ?>
            <div class="quicklinks">
              <?php
                // Render the menu
                echo $this->navigation()
                  ->menu()
                  ->setContainer($this->quickNavigation)
                  ->render();
              ?>
            </div>
          <?php endif; ?>
        </div>

        <div class='layout_middle'>
          <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
            <div class="tip">
              <span>
                <?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.');?>
              </span>
            </div>
            <br/>
          <?php endif; ?>
          <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
            <ul class="items classifieds_profile_tab">
              <?php foreach( $this->paginator as $item ): ?>
                <li>
                  <div class='item_photo'>
                    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
                  </div>
                  <div class='item_body'>

                    <div class='item_options'>

                      <?php if( !$item->closed ): ?>
                        <?php echo $this->htmlLink(array(
                          'route' => 'classified_specific',
                          'action' => 'close',
                          'classified_id' => $item->getIdentity(),
                          'closed' => 1,
                          'return_url' => urlencode($_SERVER['REQUEST_URI'])
                        ), $this->translate('Close Listing'), array(
                        )) ?>

                      <?php else: ?>
                        <?php echo $this->htmlLink(array(
                          'route' => 'classified_specific',
                          'action' => 'close',
                          'classified_id' => $item->getIdentity(),
                          'closed' => 0,
                          'return_url' => urlencode($_SERVER['REQUEST_URI'])
                        ), $this->translate('Open Listing'), array(
                        )) ?>
                      <?php endif; ?>

                          <br />

                      <?php
                      echo $this->htmlLink(array('route' => 'default', 'module' => 'classified', 'controller' => 'index', 'action' => 'delete', 'classified_id' => $item->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Delete'));
                      ?>
                    </div>

                    <div class='classifieds_browse_info_title'>
                      <div>
                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                        <?php if( $item->closed ): ?>
                          <img alt="close" src='application/modules/Classified/externals/images/close.png'/>
                        <?php endif;?>
                      </div>
                    </div>
                    <div class='item_date classifieds_browse_info_date'>
                      <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                      -
                      <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
                    </div>
                    <div class='classifieds_browse_info_blurb'>
                      <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
                      <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
                      <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>

          <?php elseif($this->search): ?>
            <div class="tip">
              <span>
                <?php echo $this->translate('You do not have any classified listing that match your search criteria.');?>
              </span>
            </div>
          <?php else:?>
            <div class="tip">
              <span>
                <?php echo $this->translate('You do not have any classified listings.');?>
              </span>
            </div>
          <?php endif; ?>
          <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array('params' => array('search' => $this->search, 'user' => $this->user))); ?>
        </div>

        </li>
        <?php else: ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<?php endif; ?>

      <?php endforeach;?>
    </ul>
  </div>
<?php endif;?>
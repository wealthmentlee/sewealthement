<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>
  &raquo; <?php echo $this->translate('Classified Listings');?>
  <?php if ($this->userObj && $this->userObj->getIdentity()):?>&raquo; <?php echo $this->userObj->__toString()?><?php endif;?>
</h4>

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
          </div>

          <div class='layout_middle'>
            <?php if( $this->tag ): ?>
              <h3>
                <?php echo $this->translate('Showing classified listings using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
              </h3>
            <?php endif; ?>

            <?php if( $this->start_date ): ?>
              <?php foreach ($this->archive_list as $archive): ?>
                <h3>
                  <?php echo $this->translate('Showing classified listings created on');?> <?php if ($this->start_date==$archive['date_start']) echo $archive['label']?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
                </h3>
              <?php endforeach; ?>
            <?php endif; ?>

            <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
              <ul class="items classifieds_profile_tab">
                <?php foreach( $this->paginator as $item ): ?>
                  <li>
                    <div class='item_photo'>
                      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
                    </div>
                    <div class='item_body'>
                      <div class='classifieds_browse_info_title'>
                        <div>
                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                        <?php if( $item->closed ): ?>
                          <img src='application/modules/Classified/externals/images/close.png'/>
                        <?php endif;?>
                        </div>
                      </div>
                      <div class='item_date classifieds_browse_info_date'>
                        <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
                        - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
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

            <?php elseif( $this->search ):?>
              <div class="tip">
                <span>
                  <?php echo $this->translate('Nobody has posted a classified listing with that criteria.');?>
                </span>
              </div>
            <?php else:?>
              <div class="tip">
                <span>
                  <?php echo $this->translate('Nobody has posted a classified listing yet.');?>
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



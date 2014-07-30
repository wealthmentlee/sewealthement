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
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_ALBUMS')) ?>
</h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if($item->active):?>
					<li class="active">
						<a href="<?php echo $item->getHref(); ?>">
							<?php echo $this->translate($item->getLabel()) ?>
							<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
						</a>
					</li>

          <li class="content">
              <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

                <ul class="items">
                  <?php foreach( $this->paginator as $album ): ?>
                    <li>
                      <div class="item_photo">
                        <a href="<?php echo $album->getHref(); ?>">
                          <img src="<?php echo $album->getPhotoUrl('thumb.normal'); ?>" width="60px"/>
                        </a>
                      </div>

                      <div class="item_body">
                        <div class="item_title">
                          <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'view', 'album_id' => $album->getIdentity()), $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10)) ?>
                        </div>
                        <div class="item_date">
                          <?php echo $this->translate('By');?>
                          <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
                          -
                          <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
                        </div>
                      </div>
                  </li>

                  <?php endforeach;?>
                </ul>

                <?php if( $this->paginator->count() > 1 ): ?>
                  <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
                <?php endif; ?>
              <?php else: ?>
                <div class="tip">
                  <span>
                    <?php echo $this->translate('Nobody has created an album yet.');?>
                  </span>
                </div>
              <?php endif; ?>
          </li>

					<?php else: ?>
					<li>
						<a href="<?php echo $item->getHref(); ?>">
							<?php echo $this->translate($item->getLabel()) ?>
							<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
						</a>
					</li>
				<?php endif; ?>

			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
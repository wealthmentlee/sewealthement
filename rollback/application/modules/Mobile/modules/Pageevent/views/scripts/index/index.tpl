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
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_event', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_EVENTS')) ?>
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

<ul class="items">

  <?php foreach ($this->paginator as $event):?>

    <li>
      <div class="item_photo">
        <?php $this->itemPhoto($event, 'thumb.icon')?>
      </div>

      <div class="item_body">

        <div class="item_title">

          <a href="<?php echo $this->url(array('action' => 'view', 'event_id' => $event->getIdentity()), 'page_event', true)?>">
            <?php echo $event->getTitle()?>
          </a>

        </div>

        <div class="item_date">

          <?php echo $this->locale()->toDateTime($event->starttime)?>
          <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
          <?php echo $this->translate('led by') ?>
          <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>

        </div>

        <?php echo $this->mobileSubstr($event->getDescription()) ?>

      </div>

    </li>

  <?php endforeach;?>

</ul>

<?php if( $this->paginator->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
<?php endif; ?>

<?php if( !$this->paginator->getTotalItemCount() ): ?>
  <?php echo $this->translate('PAGEEVENT_NOITEMS');?>
<?php endif;?>


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
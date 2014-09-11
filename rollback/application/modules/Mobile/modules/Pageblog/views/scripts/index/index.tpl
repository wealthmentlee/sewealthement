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
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_BLOGS')) ?>
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
                  <?php foreach( $this->paginator as $blog ): ?>
                    <li>

                      <div class="item_body">
                        <div class="item_title">
                          <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'view', 'blog_id' => $blog->getIdentity()), $this->string()->chunk(Engine_String::substr($blog->getTitle(), 0, 45), 10)) ?>
                        </div>
                        <div class="item_date">
                          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($blog->creation_date); ?>
                        </div>

                        <?php echo $this->mobileSubstr(Engine_String::strip_tags($blog->body))?>

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
                    <?php echo $this->translate('Nobody has created a blog yet.');?>
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
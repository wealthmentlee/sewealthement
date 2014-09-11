<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-02-14 12:38:39 michael $
 * @author     Michael
 */

?>
<h4>
  &raquo; <?php echo $this->translate('Articles');?>
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
					<div	class="search">
						<?php echo $this->form->render($this) ?>
					</div>

						<?php if( count($this->paginator) > 0 ): ?>

						<ul class='items'>

              <?php foreach( $this->paginator as $article ): ?>

              <li class="<?php if ($article->featured) echo 'active'; ?> <?php if ($article->sponsored) echo 'active'; ?>">
                <div class='item_photo'>
                  <?php echo $this->htmlLink($article->getHref(), $this->itemPhoto($article, 'thumb.normal')) ?>
                </div>
                <div class='item_body'>
                  <div class='item_title'>
                    <?php echo $this->htmlLink($article->getHref(), $article->getTitle()) ?>
                    <?php if( $article->featured ): ?>
                      <img src='application/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
                    <?php endif;?>
                    <?php if( $article->sponsored ): ?>
                      <img src='application/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
                    <?php endif;?>
                  </div>
                  <div class='itme_date'>
                    <?php echo $this->timestamp(strtotime($article->creation_date)) ?>
                    - <?php echo $this->translate('posted by %s', $article->getOwner()->__toString());?>
                      - <?php echo $this->translate(array("%s view", "%s views", $article->view_count), $this->locale()->toNumber($article->view_count)); ?>
                      - <?php echo $this->translate(array("%s comment", "%s comments", $article->comment_count), $this->locale()->toNumber($article->comment_count)); ?>
                      - <?php echo $this->translate(array('%1$s like', '%1$s likes', $article->like_count), $this->locale()->toNumber($article->like_count)); ?>
                  </div>
                  <div class='articles_browse_info_blurb'>
                    <?php // $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($article)?>
                    <?php // echo $this->fieldValueLoop($article, $fieldStructure) ?>
                    <?php echo $article->getExcerpt(256) ?>
                  </div>
                </div>
              </li>

              <?php endforeach ;?>

						</ul>

						<?php else: ?>
							<div class="tip">
								<?php echo $this->translate('Nobody has posted an article yet.') ?>
							</div>
						<?php endif; ?>

						<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array('query' => array('search' => $this->search, 'user' => $this->user))); ?>

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
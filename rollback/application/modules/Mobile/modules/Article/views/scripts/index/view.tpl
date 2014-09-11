<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 12:38:39 michael $
 * @author     Michael
 */

?>

<?php if ($this->article->owner_id != $this->viewer->getIdentity() && !$this->article->isPublished()): ?>
  <div class="result_message">
    <?php echo $this->translate('This article has not been published yet.'); ?>
  </div>
<?php return; ?>
<?php endif;?>

<h4>
  &raquo;
  <?php echo $this->htmlLink($this->url(array(),'article_browse',true), $this->translate('Browse Articles')); ?>
  <?php if ($this->category):?>
    &raquo; <?php echo $this->translate($this->category->category_name); ?>
  <?php endif; ?>
  <?php // echo $this->translate('%1$s\'s Article', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
</h4>

<?php if (!$this->article->isPublished()): ?>
  <div class="result_message">
    <?php echo $this->translate('No one will be able to view this article until you <a href=\'%1$s\'>publish</a> it.', array($this->url(array('article_id' => $this->article->article_id), 'article_publish', true))); ?>
  </div>
<?php endif; ?>

<div class="layout_content">
<ul class="items subcontent<?php if ($this->article->featured):?> articles_entrylist_featured<?php endif;?><?php if ($this->article->sponsored):?> articles_entrylist_sponsored<?php endif;?>">
	<li>
		<div class="item_photo">
			<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class' => 'articles_gutter_photo')) ?>
		</div>
		<div class="item_body">
			<h3>
        <?php echo $this->article->getTitle() ?>
        <?php if( $this->article->featured ): ?>
          <img src='application/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
        <?php endif;?>
        <?php if( $this->article->sponsored ): ?>
          <img src='application/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
        <?php endif;?>
			</h3>
			<h4>
				<div class="item_date" style="font-weight:normal; font-size: 0.9em;">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->article->getParent(), $this->article->getParent()->getTitle()) ?>
        <?php echo $this->timestamp($this->article->creation_date) ?>
        - <?php echo $this->translate(array("%s view", "%s views", $this->article->view_count), $this->article->view_count); ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <?php echo $this->translate($this->category->category_name) ?> <?php endif; ?>
				</div>
			</h4>

      <div style="font-size: 0.9em;margin-top:10px;">

        <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'article', 'id' => $this->article->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate("Share")); ?>

        <?php if ($this->suggestUrl): ?>
         - <a href="<?php echo $this->suggestUrl?>"><?php echo $this->translate('Suggest To Friends')?></a>
        <?php endif; ?>

       </div>

		</div>
	</li>

	<li style="border-top: 0px;background:none;">

    <div class="item_body">
      <?php echo $this->article->body ?>
    </div>

    <?php if ($this->settings('article.showmainphoto', 0) && $this->main_photo): ?>
    <div class="article_entrylist_entry_photo">
      <a href="<?php echo $this->main_photo->getHref(); ?>"><?php echo $this->itemPhoto($this->article, 'thumb.profile') ?></a>
    </div>
    <?php endif; ?>

    <?php if ($article_field_values = $this->fieldValueLoop($this->article, $this->fieldStructure)): ?>
    <div class="profile_fields">
      <h4>
        <span><?php echo $this->translate('Article Details');?></span>
      </h4>
      <?php echo $article_field_values; ?>
    </div>
    <?php endif; ?>


    <?php $photoCount = $this->paginator->getTotalItemCount(); ?>
    <?php if ($photoCount): ?>
    <div class="article_entrylist_entry_photos">
      <h4>
        <span><?php echo $this->translate('Article Album'); ?>
        (<?php echo $this->htmlLink(array(
            'route' => 'article_extended',
            'controller' => 'photo',
            'action' => 'list',
            'subject' => $this->article->getGuid(),
          ), $this->translate(array("%s photo", "%s photos", $photoCount), $photoCount), array(
        )) ?>)
        </span>
      </h4>
      <ul class="items">
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
              <div class="item_photo">
                <img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" width="80px"/>
              </div>
              <div class="item_body">
                <?php echo $photo->getTitle(); ?>
              </div>
            </a>
          </li>
        <?php endforeach;?>
       </ul>
      <div style="clear:both;"></div>
    </div>
    <?php endif; ?>

	</li>

</ul>

<div style="padding-bottom: 5px;"></div>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"article", "id"=>$this->article->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>
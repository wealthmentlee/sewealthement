<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 12:40:00 michael $
 * @author     Michael
 */

?>

<ul class="items">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='item_photo'>
        <?php echo $this->htmlLink($item, $this->itemPhoto($item, 'thumb.normal')) ?>
      </div>
      <div class='item_body'>
        <div class='item_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          <?php if( $item->featured ): ?>
            <img src='application/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
          <?php endif;?>
          <?php if( $item->sponsored ): ?>
            <img src='application/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
          <?php endif;?>
        </div>
        <div class='article_details'>
          <?php echo $this->timestamp($item->creation_date) ?>
              - <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
              - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
              - <?php echo $this->translate(array('%1$s like', '%1$s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>
        </div>
        <div class="article_description">
          <?php echo $this->mobileSubstr($item->getDescription());?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<div class="articles_profile_tab_links">
  <?php echo $this->htmlLink($this->url(array('user' => $this->subject()->getIdentity()), 'article_browse'), $this->translate("View All Article"), array('class' => 'buttonlink item_icon_article')) ?>
</div>

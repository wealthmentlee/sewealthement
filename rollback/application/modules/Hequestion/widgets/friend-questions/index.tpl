<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>


<ul class="hqBrowseQuestions">
  <?php foreach ($this->paginator as $item):?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
      </div>
      <div class="item_body">
        <div class="item_title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="item_date">
          <?php echo $this->translate(array('%s vote', '%s votes', $item->vote_count), $item->vote_count);?>
          <span>&middot;</span>
          <?php echo $this->translate(array('%s follower', '%s followers', $item->follower_count), $item->follower_count);?>
          <span>&middot;</span>
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          <?php echo $this->translate('by');?>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      </div>
    </li>
  <?php endforeach;?>
</ul>
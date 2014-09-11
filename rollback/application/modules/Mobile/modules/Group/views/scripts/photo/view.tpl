<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<div class="layout_content">
<h4>
    <?php echo $this->group->__toString(); ?>
  &raquo;
  <?php echo $this->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->group->getGuid(),
      ), $this->translate('Photos')) ?>
</h4>

<br/>

<div>

  <div class="clr"></div>

  <div class='photo_view_container'>
    <div class="photo">
      <a href='<?php echo $this->escape($this->photo->getNextCollectible()->getHref()) ?>'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl('thumb'), $this->photo->getTitle()); ?>
      </a>
    </div>
    <div class="photo_details"></div>
    <?php if( $this->photo->getTitle() ): ?>
      <div class="title">
        <?php echo $this->photo->getTitle(); ?>
      </div>
    <?php endif; ?>

    <?php if( $this->photo->getDescription() ): ?>
      <div class="caption">
        <?php echo $this->photo->getDescription() ?>
      </div>
    <?php endif; ?>

    <div class="date">
      <?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->modified_date) ?>
    </div>
  </div>

  <br/>

  <?php echo $this->mobileAction("list", "comment", "core", array("type"=>"group_photo", "id"=>$this->photo->getIdentity(), 'viewAllLikes'=>true)); ?>


</div>

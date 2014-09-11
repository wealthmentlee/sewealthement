<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

?>
<ul class="items">
  <?php foreach( $this->paginator as $page ): ?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($page, $this->itemPhoto($page, 'thumb.normal')) ?>
      </div>
      <div class="item_body">
        <div class="item_title">
          <?php echo $this->htmlLink($page->getHref(), $page->getTitle()) ?>
        </div>
        <div class="item_date">
          <?php
            $page_id = $page->getIdentity();
            $like_count = (!empty($this->like_counts[$page_id])) ? $this->like_counts[$page_id] : 0;
          ?>
          <?php if ($like_count): ?>
            <?php echo $this->translate(array('%s person like.', '%s people like it.', $like_count), $this->locale()->toNumber($like_count)); ?>
          <?php else: ?>
            <?php echo $this->translate('No one like it yet.'); ?>
          <?php endif; ?>
           | <?php echo $this->translate('Status: ').($page->admin_title ? $page->admin_title : $this->translate('Admin')); ?>
        </div>
        <div class="pages_profile_tab_desc">
          <?php echo $this->truncate($page->getDescription(), 300, '...'); ?>
        </div>
      </div>
      <div class="clr"></div>
    </li>
  <?php endforeach; ?>
</ul>

<?php echo $this->htmlLink($this->url(array(), 'page_browse') . '?user=' . $this->subject->getIdentity() , $this->translate('View All Pages'), array('class' => 'buttonlink item_icon_page')) ?>

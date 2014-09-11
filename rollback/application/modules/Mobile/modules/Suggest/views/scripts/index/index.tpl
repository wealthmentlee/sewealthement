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
<h4>&raquo; <?php echo $this->translate('suggest_suggests_page_header'); ?></h4>
<p><?php echo $this->translate('suggest_suggests_page_description'); ?></p>

<div class="suggest-container">
  <?php if (count($this->paginator) > 0): ?>
  <?php foreach( $this->paginator as $key => $suggests ): ?>
    <div class="suggest-item">
      <div class="suggest-title">
        <?php echo
          $this->translate(array(
            'suggest_'.$key.'_suggestions_title',
            'suggest_'.$key.'_suggestions_title',
            count($suggests)
          ), count($suggests)); ?>
      </div>
      <div class="suggest-list">
        <?php
          foreach ($suggests as $suggest) {
            echo $suggest;
            echo '<div class="clr"></div>';
          }
        ?>
        <div class="clr"></div>
      </div>
    </div>
  <? endforeach; ?>
  <?php else: ?>
    <br />
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no suggestions.'); ?>
      </span>
    </div>
  <?php endif; ?>
</div>
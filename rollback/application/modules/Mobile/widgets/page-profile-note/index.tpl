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

<div class="profile_note result_message">
	<div class="profile_note_text" id="profile_note_text">
    <?php if ($this->subject->note): ?>
      <?php echo nl2br($this->subject->note); ?>
    <?php else: ?>
      <?php if($this->isTeamMember): ?>
        <?php echo $this->translate('Write something about %s page.', $this->subject->getTitle()); ?>
      <?php endif; ?>
    <?php endif; ?>
    <?php if ($this->isTeamMember):?>
      <a href="<?php echo $this->url(array('action' => 'post-note', 'page_id' => $this->subject->getIdentity()), 'page_team') .'?return_url='.urlencode($_SERVER['REQUEST_URI']) ?>">
        <?php echo $this->translate('Edit')?>
      </a>
    <?php endif;?>
  </div>
</div>
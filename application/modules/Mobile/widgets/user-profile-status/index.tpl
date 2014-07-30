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

<div id='mobile_profile_status'>
	<span class="mobile_profile_title">
    <?php echo $this->subject()->getTitle() ?>
	</span>
  <?php if( $this->auth ): ?>
    <span class="profile_status_text" id="user_profile_status_container">
      <?php echo $this->subject()->status ?>
    </span>
  <?php endif; ?>
</div>


<?php if( !$this->auth ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.');?>
    </span>
  </div>
  <br />
<?php endif; ?>
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

<div id='page_photo'>
  <?php echo $this->htmlLink( $this->subject()->getHref(), $this->itemPhoto($this->subject(), 'thumb.profile', array('border' => 0)) ); ?>
</div>
<br />
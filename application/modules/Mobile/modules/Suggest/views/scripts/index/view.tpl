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
  
<?php echo $this->suggest; ?>

<div class="suggest-view-all">
  <?php
    echo $this->htmlLink(
    $this->url(array(), 'suggest_view', true),
    $this->translate('View All Suggestions'),
    array('class' => 'bold'));
  ?>
</div>
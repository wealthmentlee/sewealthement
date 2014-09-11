<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<div class="wall_welcome_container">
  <div class="wall_welcome">
    <?php echo $this->translate('WALL_WELCOME', array($this->layout()->siteinfo['title'], Engine_Api::_()->user()->getViewer()->getTitle()));?>
  </div>
</div>
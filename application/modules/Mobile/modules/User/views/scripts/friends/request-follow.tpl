<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: request-follow.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<li id="user-widget-request-<?php echo $this->notification->notification_id ?>">
  <ul class="items">
    <li>
      <div class="item_photo">
        <?php echo $this->itemPhoto($this->notification->getSubject(), 'thumb.icon')?>
      </div>
      <div class="item_body">
        <div>
          <?php echo $this->translate('%1$s has requested to follow you.', $this->htmlLink($this->notification->getSubject()->getHref(), $this->notification->getSubject()->getTitle())); ?>
        </div>
        <div>

          <form method="post" action="<?php echo $this->url(array('controller' => 'friends', 'action' => 'follow'), 'user_extended', true)?>">

            <button type="submit" onclick='userWidgetRequestSend("confirm", <?php echo $this->notification->getSubject()->getIdentity() ?>, <?php echo $this->notification->notification_id ?>)'>
              <?php echo $this->translate('Allow');?>
            </button>

            <?php echo $this->translate('or');?>

            <?php echo $this->htmlLink(array(
              'route' => 'user_extended',
              'controller' => 'friends',
              'action' => 'ignore'
            ), $this->translate('ignore request'))?>

          </form>

        </div>
      </div>
    </li>
  </ul>
</li>
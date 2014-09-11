<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: request-friend.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<li>
  <ul class="items">
    <li>
      <div class="item_photo">
        <?php echo $this->itemPhoto($this->notification->getSubject(), 'thumb.icon')?>
      </div>
      <div class="item_body">
        <div>
          <?php echo $this->translate('%1$s has sent you a friend request.', $this->notification->getSubject()->__toString(), $this->notification->getSubject()->__toString()); ?>
        </div>
        <div>
          <form action="<?php echo $this->url(array('controller' => 'friends', 'action' => 'confirm'), 'user_extended', true) ?>" method="get">
            <input type='hidden' name='user_id' value='<?php echo $this->notification->getSubject()->getIdentity(); ?>'/>
            <input type='hidden' name='return_url' value='<?php echo urlencode($_SERVER["REQUEST_URI"]); ?>' />
            <button type="submit">
              <?php echo $this->translate('Add Friend');?>
            </button>
            <?php echo $this->translate('or');?>
            <a href="<?php echo $this->url(array('controller' => 'friends', 'action' => 'reject', 'user_id'=>$this->notification->getSubject()->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'user_extended', true) ?>">
              <?php echo $this->translate('ignore request');?>
            </a>
          </form>
        </div>
      </div>
    </li>
  </ul>
</li>


<li id="user-widget-request-<?php echo $this->notification->notification_id ?>">

  <div>

  </div>
</li>
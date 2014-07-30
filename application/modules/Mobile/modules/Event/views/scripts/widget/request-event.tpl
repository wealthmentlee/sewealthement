<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: request-event.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<li>
  <ul class="items">
    <li>
      <div class="item_photo">
        <?php echo $this->itemPhoto($this->notification->getObject(), 'thumb.icon') ?>
      </div>
      <div class="item_body">
        <?php echo $this->translate('%1$s has invited you to the event %2$s', $this->notification->getObject()->__toString(), $this->notification->getObject()->__toString()); ?>
        <div>

          <form method="post" action="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'return_url' => urlencode($_SERVER['REQUEST_URI']), 'event_id' => $this->notification->getObject()->getIdentity()), 'event_extended')?>">

            <button type="submit" name="rsvp" value="2"><?php echo $this->translate('Attending')?></button>
            <button type="submit" name="rsvp" value="1"><?php echo $this->translate('Maybe Attending')?></button>

            <?php echo $this->translate('or');?>&nbsp;

            <?php echo $this->htmlLink(array(
            'route' => 'event_extended',
            'controller' => 'member',
            'action' => 'reject',
            'return_url' => urlencode($_SERVER['REQUEST_URI']),
            'event_id' => $this->notification->getObject()->getIdentity()
          ), $this->translate('ignore request'))?>

          </form>

        </div>
      </div>
    </li>
  </ul>
</li>
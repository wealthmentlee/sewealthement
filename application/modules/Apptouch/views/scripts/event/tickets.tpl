<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>

<div>
<?php if( count($this->paginator) > 0 ): ?>
    <ul>
    <?php foreach( $this->paginator as $card ): ?>
        <?php $type = 'event'; ?>
        <?php $event = Engine_Api::_()->getItem('event', $card['event_id']); ?>
        <li style="list-style: none">
            <?php
            $eventPhotoUrl = $event->getPhotoUrl('thumb.pin');
            if(!$eventPhotoUrl)
                $eventPhotoUrl = $this->layout()->staticBaseUrl ."application/modules/Heevent/externals/images/event-list-nophoto.gif";
            $viewer = Engine_Api::_()->user()->getViewer();

            ?>
            <div class="he-ticket-img"  >
                <img src="<?php echo $eventPhotoUrl;?>"  />
            </div>
            <div class="he-ticket-desc">
                <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
                <p> <div class="event_ticket_code">
                   <strong> <?php echo $this->translate('Ticket code').' - '. $card['ticked_code']?></strong>
                </div></p>
            </div>

        </li>

    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="tip">
  <span>
      <?php echo $this->translate('You have not buy any events yet.') ?>

  </span>
  </div></div>

<?php endif; ?>


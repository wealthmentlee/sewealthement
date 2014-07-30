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

<ul id="events-upcoming">
  <?php foreach( $this->paginator as $event ):
    // Convert the dates for the viewer
    $startDateObject = new Zend_Date(strtotime($event->starttime));
    $endDateObject = new Zend_Date(strtotime($event->endtime));
    if( $this->viewer() && $this->viewer()->getIdentity() ) {
      $tz = $this->viewer()->timezone;
      $startDateObject->setTimezone($tz);
      $endDateObject->setTimezone($tz);
    }
    $isOngoing = ( $startDateObject->toValue() < time() );
    ?>
    <li<?php if( $isOngoing ):?> class="ongoing"<?php endif ?>>
      <?php echo $event->__toString() ?>
      <div class="events-upcoming-date">
        <?php echo $this->timestamp($event->starttime, array('class'=>'eventtime')) ?>
      </div>
      <?php if( $isOngoing ): ?>
        <div class="events-upcoming-ongoing">
          <?php echo $this->translate('Ongoing') ?>
        </div>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
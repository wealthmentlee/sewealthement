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

<?php // @todo add in a human-readable column to $requestInfo to properly display text ?>
<ul class="requests_widget">
  <?php foreach( $this->requests as $requestInfo ):
    ob_start();
    try { ?>
      <li>
      <?php
      $request_type = str_replace('_', ' ', $requestInfo['info']['type']);
      echo $this->htmlLink(array('route'=> 'recent_activity'),
        $this->translate(array("%s {$request_type}", "%s {$request_type}s", $requestInfo['count']), $this->locale()->toNumber($requestInfo['count'])),
        array('class' => 'buttonlink notification_item_general notification_type_'.$requestInfo['info']['type'])) ?>
      </li>
  <?php
    } catch( Exception $e ) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
      continue;
    }
    ob_end_flush();
  endforeach; ?>
</ul>
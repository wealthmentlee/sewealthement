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
<?php

$href = array(
  'route' => 'default',
  'module' => 'event',
  'controller' => 'member',
  'action' => 'rsvp',
  'event_id' => $this->subject->getIdentity(),
  'return_url' => urlencode($_SERVER['REQUEST_URI'])
);

?>

<ul class="mobile_event_rsvp">
  <li><?php echo $this->htmlLink(array_merge($href, array('rsvp' => 2)), $this->translate('Attending'), array('class' => ($this->member->rsvp == 2) ? 'active' : ''));?></li>
  <li><?php echo $this->htmlLink(array_merge($href, array('rsvp' => 1)), $this->translate('Maybe Attending'), array('class' => ($this->member->rsvp == 1) ? 'active' : ''));?></li>
  <li><?php echo $this->htmlLink(array_merge($href, array('rsvp' => 0)), $this->translate('Not Attending'), array('class' => ($this->member->rsvp == 0) ? 'active' : ''));?></li>
</ul>
<div style="clear:both;"></div>

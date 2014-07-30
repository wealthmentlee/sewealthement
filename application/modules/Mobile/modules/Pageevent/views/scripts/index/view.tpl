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

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_event', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_EVENTS')) ?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_event', 'action' => 'view', 'event_id' => $this->event->getIdentity()), ($this->event->getTitle()) ? $this->event->getTitle() : $this->translate('Untitled')) ?>
</h4>

<div class="layout_content">
<ul class="items subcontent">
	<li>
		<div class="item_photo">
			<?php echo $this->itemPhoto($this->event, 'thumb.profile') ?>
		</div>
		<div class="item_body pageevent_event">
			<h3><?php echo $this->event->getTitle() ?></h3>

      <div class="header">

        <span><?php echo $this->translate('PAGEVENT_ONWER', $this->event->getOwner()->__toString())?></span>

        <div class="options mobile_page_event_rsvp_options">

          <?php if ($this->member && !$this->member->resource_approved):?>

            <div class="item">
              <span><?php echo $this->translate('PAGEEVENT_MEMBER_WAITING')?></span>
            </div>
            <div class="item">
              <form method="post" action="<?php echo $this->url(array('action' => 'member-approve', 'event_id' => $this->event_id, 'approve' => 0), 'page_event', true)?>">
                <button class="<?php if ($this->member && $this->member->rsvp == 2):?>active<?php endif;?>" type="submit"><?php echo $this->translate('PAGEEVENT_CANCEL')?></button>
              </form>
            </div>

          <?php elseif ($this->isLogin):?>

            <?php if ($this->event->approval && !$this->member):?>

              <div class="item">
                <form method="post" action="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 0), 'page_event', true)?>">
                  <button class="<?php if ($this->member && $this->member->rsvp == 0):?>active<?php endif;?>" type="submit"><?php echo $this->translate('PAGEEVENT_REQUEST_INVITE')?></button>
                </form>
              </div>

            <?php else:?>

              <form method="post" action="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id), 'page_event', true)?>">
                <div class="item">
                 <?php if ($this->member && $this->member->rsvp == 2):?>
                  <button class="active" type="submit" name="rsvp" value="2"><?php echo $this->translate('PAGEEVENT_ATTENDING')?></button>
                 <?php else: ?>
                  <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 2), 'page_event', true)?>" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_ATTENDING')?></a>
                 <?php endif;?>
                </div>
                <div class="item">
                <?php if ($this->member && $this->member->rsvp == 1):?>
                  <button class="active" type="submit" name="rsvp" value="1"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING')?></button>
                <?php else: ?>
                  <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 1), 'page_event', true)?>" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING')?></a>
                <?php endif;?>
                </div>
                <div class="item">
                <?php if ($this->member && $this->member->rsvp == 0):?>
                  <button class="active" type="submit" name="rsvp" value="0"><?php echo $this->translate('PAGEEVENT_NOTATTENDING')?></button>
                <?php else: ?>
                  <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 0), 'page_event', true)?>" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_NOTATTENDING')?></a>
                <?php endif;?>
                </div>
              </form>

            <?php endif;?>

          <?php endif;?>

          <div class="clr"></div>
        </div>

        <div class="clr"></div>

      </div>

    <div class="event_info">

      <div class="item">
        <div class="label"><?php echo $this->translate('Posted')?></div>
        <div class="value"><?php echo $this->timestamp($this->event->creation_date)?></div>
        <div class="clr"></div>
      </div>

      <?php if ($this->event->starttime == $this->event->endtime ): ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value"><?php echo $this->locale()->toDate($this->startDateObject)?> <?php echo $this->locale()->toTime($this->startDateObject) ?></div>
          <div class="clr"></div>
        </div>

      <?php elseif( $this->startDateObject->toString('y-MM-dd') == $this->endDateObject->toString('y-MM-dd') ): ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value"><?php echo $this->locale()->toDate($this->startDateObject) ?></div>
          <div class="clr"></div>
        </div>
        <div class="item">
          <div class="label"><?php echo $this->translate('Time')?></div>
          <div class="value">
            <?php echo $this->locale()->toTime($this->startDateObject)?> -
            <?php echo $this->locale()->toTime($this->endDateObject)?>
          </div>
          <div class="clr"></div>
        </div>

      <?php else: ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value">
          <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->startDateObject), $this->locale()->toTime($this->startDateObject))?> -
          <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->endDateObject), $this->locale()->toTime($this->endDateObject))?>
          </div>
          <div class="clr"></div>
        </div>

      <?php endif;?>

      <?php if (!empty($this->event->location)):?>

        <div class="item">
          <div class="label"><?php echo $this->translate('PAGEEVENT_WHERE')?></div>
          <div class="value">
            <?php echo $this->event->location?>
            <?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($this->event->location), $this->translate('PAGEEVENT_MAP'), array('target' => 'blank'))?>
          </div>
          <div class="clr"></div>
        </div>

      <?php endif;?>

      <?php if (
        $this->attending->getTotalItemCount() ||
        $this->maybe_attending->getTotalItemCount() ||
        $this->not_attending->getTotalItemCount()
    ):?>

        <br />

        <div class="item">
          <div class="label">
            <?php echo $this->translate('MOBILE_PAGE_EVENT_MEMBERS');?>
          </div>
          <div class="value">


        <?php if ($this->attending->getTotalItemCount()):?>

          <?php

            $title = $this->translate('MOBILE_PAGE_EVENT_MEMBERS_ATTENDING');

          ?>
           <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'hecore',
              'controller' => 'list',
              'action' => 'index',
              'mm' => 'mobile',
              'l' => 'getEventMembers',
              't' => $title,
              'return_url' => urlencode($_SERVER['REQUEST_URI']),
              'QUERY' => array(
                'params' => array(
                  'event_id' => $this->event->getIdentity(),
                  'rsvp' => 2,
                  'list_title2' => $this->translate('MOBILE_FRIEND_TAB')
                )
              )
          ), $title .' ('.$this->attending->getTotalItemCount().')')?>

              <br />

        <?php endif;?>
        <?php if ($this->maybe_attending->getTotalItemCount()):?>

          <?php

            $title = $this->translate('MOBILE_PAGE_EVENT_MEMBERS_MAYBE_ATTENDING');

          ?>
           <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'hecore',
              'controller' => 'list',
              'action' => 'index',
              'mm' => 'mobile',
              'l' => 'getEventMembers',
              't' => $title,
              'return_url' => urlencode($_SERVER['REQUEST_URI']),
              'QUERY' => array(
                'params' => array(
                  'event_id' => $this->event->getIdentity(),
                  'rsvp' => 1,
                  'list_title2' => $this->translate('MOBILE_FRIEND_TAB')
                )
              )
          ), $title .' ('.$this->maybe_attending->getTotalItemCount().')')?>

              <br />

        <?php endif;?>


          <?php if ($this->not_attending->getTotalItemCount()):?>

          <?php

            $title = $this->translate('MOBILE_PAGE_EVENT_MEMBERS_NOT_ATTENDING');

          ?>
           <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'hecore',
              'controller' => 'list',
              'action' => 'index',
              'mm' => 'mobile',
              'l' => 'getEventMembers',
              't' => $title,
              'return_url' => urlencode($_SERVER['REQUEST_URI']),
              'QUERY' => array(
                'params' => array(
                  'event_id' => $this->event->getIdentity(),
                  'rsvp' => 0,
                  'list_title2' => $this->translate('MOBILE_FRIEND_TAB')
                )
              )
          ), $title .' ('.$this->not_attending->getTotalItemCount().')')?>

              <br />

            <?php if (($this->isTeamMember || $this->isOwner) && $this->count_waiting):?>

              <?php echo $this->htmlLink(array(
                'route' => 'page_event',
                'action' => 'waiting',
                'event_id' => $this->event->getIdentity(),
              ), $this->translate('MOBILE_PAGE_EVENT_MEMBERS_WAITING') . '')?>

            <?php endif;?>

        <?php endif;?>

        </div>
          <div class="clr"></div>
        </div>


      <?php endif;?>

    </div>


		</div>

	</li>

	<li style="border-top: 0px;">
			<div class="item_body">
				<?php echo $this->event->getDescription() ?>
			</div>
	</li>
</ul>

<div style="padding-bottom: 5px;"></div>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"pageevent", "id"=>$this->event->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>
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

<h4>&raquo; <?php echo $this->translate('Messages');?>&raquo;
	<?php if( '' != ($title = trim($this->conversation->getTitle())) ): ?>
    <?php echo $title ?>
  <?php else: ?>
    <em>
      <?php echo $this->translate('(No Subject)') ?>
    </em>
  <?php endif; ?>
</h4>

<div class="layout_content">

<div>
  <?php
  $you  = array_shift($this->recipients);
  $you  = $this->htmlLink($you->getHref(), ($you==$this->viewer()?$this->translate('You'):$you->getTitle()));
  $them = array();
  foreach ($this->recipients as $r) {
    if ($r != $this->viewer()) {
        $them[] = ($r==$this->blocker?"<s>":"").$this->htmlLink($r->getHref(), $r->getTitle()).($r==$this->blocker?"</s>":"");
    } else {
        $them[] = $this->htmlLink($r->getHref(), $this->translate('You'));
    }
  }
  
  if (count($them)) echo $this->translate('Between %1$s and %2$s', $you, $this->fluentList($them));
  else echo 'Conversation with a deleted member.';
  ?>
</div>

<ul class="items">
  <?php foreach( $this->messages as $message ):
    $user = $this->user($message->user_id); ?>
    <li>
        <div class='item_photo'>
          <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
        </div>
        <div class='item_body'>
          <p>
            <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
          </p>
          <p class="item_date">
            <?php echo $this->timestamp($message->date) ?>
          </p>
        <?php echo nl2br(html_entity_decode($message->body)) ?>
        <?php if( !empty($message->attachment_type) && null !== ($attachment = $this->item($message->attachment_type, $message->attachment_id))): ?>
          <div class="message_attachment">
            <?php if(null != ( $richContent = $attachment->getRichContent(false, array('message'=>$message->conversation_id)))): ?>
              <?php echo $richContent; ?>
            <?php else: ?>
              <div class="message_attachment_photo">
                <?php if( null !== $attachment->getPhotoUrl() ): ?>
                  <?php echo $this->itemPhoto($attachment, 'thumb.normal') ?>
                <?php endif; ?>
              </div>
              <div class="message_attachment_info">
                <div class="message_attachment_title">
                  <?php echo $this->htmlLink($attachment->getHref(array('message'=>$message->conversation_id)), $attachment->getTitle()) ?>
                </div>
                <div class="message_attachment_desc">
                  <?php echo $attachment->getDescription() ?>
                </div>
              </div>
           <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </li>
  <?php endforeach; ?>

  <li>
    <div>
    <?php if( !$this->blocked || (count($this->recipients)>1)): ?>
      <?php echo $this->form->render($this) ?>
    <?php else:?>
      <?php echo $this->translate('You can no longer respond to this message because %1$s has blocked you.', $this->blocker->getTitle())?>
    <?php endif; ?>
    </div>

  </li>
</ul>
</div>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
</ul>
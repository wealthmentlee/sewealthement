<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<?php

  $this->headScript()
   ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hequestion/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js')
  ;


  $request = Zend_Controller_Front::getInstance()->getRequest();
  $is_smoothbox = ($request->getParam('format') == 'smoothbox');

?>



<script type="text/javascript">
  Hequestion.is_smoothbox = <?php echo (int)$is_smoothbox;?>;
  en4.core.runonce.add(function() {
    Hequestion.bindAskedBlock($$('.layout_hequestion_asked')[0], $$('.hqQuestion')[0]);
  });
</script>


<h3>
  <?php echo $this->translate('HEQUESTION_ASKED');?>
</h3>

<div class="hqAsked">

  <div class="hqAskedLeft">

    <div class="item_photo">
      <a href="<?php echo $this->subject()->getOwner()->getHref();?>">
        <?php echo $this->itemPhoto($this->subject()->getOwner(), 'thumb.icon');?>
      </a>
    </div>

    <div class="item_body">
      <div class="item_title">
        <a href="<?php echo $this->subject()->getOwner()->getHref();?>">
          <?php echo $this->subject()->getOwner()->getTitle();?>
        </a>
      </div>

      <div class="item_description">
        <div class="item_date">
          <?php echo $this->translate(array('%s vote', '%s votes', $this->subject()->vote_count), $this->subject()->vote_count);?>
          <span>&middot;</span>
          <?php echo $this->translate(array('%s follower', '%s followers', $this->subject()->follower_count), $this->subject()->follower_count);?>
          <span>&middot;</span>
          <?php echo $this->translate('Posted');?>
          <?php echo $this->timestamp(strtotime($this->subject()->creation_date)) ?>
          <?php echo $this->translate('by');?>
          <?php echo $this->htmlLink($this->subject()->getOwner()->getHref(), $this->subject()->getOwner()->getTitle()) ?>
        </div>

        <ul class="item_options hqLineOptions">

          <?php if ($this->viewer()->getIdentity()):?>
            <?php if ($this->subject()->hasVote($this->viewer())):?>
              <li>
                <span>&middot;</span>
                <a href="javascript:void(0);" class="hqQuestionUnvote"><?php echo $this->translate('HEQUESTION_UNVOTE');?></a>
              </li>
            <?php endif;?>
          <?php endif;?>
          <?php if ($this->viewer()->getIdentity()):?>
            <li>
              <span>&middot;</span>
              <a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'format' => 'smoothbox', 'type' => $this->subject()->getType(), 'id' => $this->subject()->getIdentity()), 'default', true);?>" <?php if (!$is_smoothbox):?>class="smoothbox"<?php endif;?>><?php echo $this->translate('Share');?></a>
            </li>
          <?php endif;?>
          <?php if ($this->viewer()->getIdentity() && $this->subject()->isOwner($this->viewer())):?>
            <li>
              <span>&middot;</span>
              <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'format' => 'smoothbox', 'subject' => $this->subject()->getGuid()), 'default', true);?>" <?php if (!$is_smoothbox):?>class="smoothbox"<?php endif;?>><?php echo $this->translate('Report');?></a>
            </li>
          <?php endif;?>

          <?php if ($this->viewer()->getIdentity() && $this->subject()->authorization()->isAllowed($this->viewer(), 'edit')):?>
            <li>
              <span>&middot;</span>
              <a href="javascript:void(0);" class="hqQuestionEditOptions"><?php echo $this->translate('HEQUESTIONS_Edit options');?></a>
            </li>
          <?php endif;?>
          <?php if ($this->viewer()->getIdentity() && $this->subject()->authorization()->isAllowed($this->viewer(), 'delete')):?>
            <li>
              <span>&middot;</span>
              <a href="<?php echo $this->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'delete', 'format' => 'smoothbox', 'question_id' => $this->subject()->getIdentity()), 'default', true);?>" <?php if (!$is_smoothbox):?>class="smoothbox"<?php endif;?>><?php echo $this->translate('Delete');?></a>
            </li>
          <?php endif;?>


          <?php if ($this->viewer()->getIdentity() && $this->subject()->authorization()->isAllowed($this->viewer(), 'edit')):?>

            <?php

              $auth = Engine_Api::_()->authorization()->context;

              $availableLabels = array(
                'everyone'            => 'HEQUESTION_Everyone',
                'owner_network'       => 'HEQUESTION_Friends and Networks',
                'owner_member'        => 'HEQUESTION_Friends Only',
                'owner'               => 'HEQUESTION_Just Me'
              );

              $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $this->viewer(), 'auth_view');
              $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

              $privacy_active = '';
              $privacy_active_key = '';

              if (!empty($viewOptions))
              {
                $keys = array_keys($viewOptions);
                $privacy_active_key = $keys[0];
                $privacy_active = $viewOptions[$privacy_active_key];
                foreach( array_reverse( array_keys($viewOptions) ) as $role ) {
                  if( 1 === $auth->isAllowed($this->subject(), $role, 'view') ) {
                    $privacy_active_key = $role;
                    $privacy_active = $viewOptions[$privacy_active_key];
                  }
                }
              }
            ?>
            <?php if (!empty($viewOptions)):?>
              <li>
                <div class="wall-privacy-container">
                  <a href="javascript:void(0);" class="wall-privacy-action-link wall_blurlink wall_tips <?php if ($privacy_active == 'everyone'):?>wall_is_public<?php endif;?>" title="<?php echo $this->translate($privacy_active);?>">&nbsp;</a>
                  <ul class="wall-privacy">
                    <?php foreach ($viewOptions as $key => $item):?>
                    <li>
                      <a href="javascript:void(0);" class="item wall_blurlink <?php if ($key == $privacy_active_key):?>is_active<?php endif;?>" rev="<?php echo $key?>">
                        <span class="wall_icon_active">&nbsp;</span>
                        <span class="wall_text"><?php echo $this->translate($item);?></span>
                      </a>
                    </li>
                    <?php endforeach ;?>
                  </ul>
                  <input type="hidden" name="auth_view" value="<?php echo $privacy_active_key;?>" class="wall_privacy_input" />
                </div>
              </li>
              <?php endif;?>
          <?php endif;?>
        </ul>

      </div>

    </div>

  </div>


  <div class="hqAskedRight">

    <ul class="hqLineOptions item_options">
      <?php if ($this->viewer()->getIdentity()):?>
        <li>
          <span>&middot;</span>
          <a href="javascript:void(0);" class="hqQuestionAskFriends wall-button"><?php echo $this->translate('HEQUESTION_ASK_FRIENDS');?></a>
        </li>
      <?php endif;?>
      <?php if ($this->viewer()->getIdentity() && !$this->subject()->isOwner($this->viewer())):?>
        <li>
          <span class="hqQuestionFollow <?php if ($this->subject()->isFollower($this->viewer())):?>hqQuestionIsFollower<?php endif;?>">
            <a href="javascript:void(0);" class="hqQuestionAddFollow wall-button"><?php echo $this->translate('HEQUESTION_FOLLOW');?></a>
            <a href="javascript:void(0);" class="hqQuestionRemoveFollow wall-button"><?php echo $this->translate('HEQUESTION_UNFOLLOW');?></a>
          </span>
        </li>
      <?php endif;?>
    </ul>

  </div>

  <input type="hidden" name="question_id" value="<?php echo $this->subject()->getIdentity();?>" class="hq_question_id" />


</div>




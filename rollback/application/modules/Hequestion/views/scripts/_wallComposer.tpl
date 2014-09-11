<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _wallComposer.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>




<div class="wallFormComposer wallFormQuestionComposer tli l">

  <form method="post" action="<?php echo $this->url()?>">
    <div class="wallComposerContainer">
      <div class="wallTextareaContainer">
        <div class="inputBox">
          <div class="labelBox is_active">
            <span><?php echo $this->translate('HEQUESTION_ASK');?></span>
          </div>
          <div class="textareaBox">
            <div class="close"></div>
            <textarea rows="1" cols="1" name="title"></textarea>
            <input type="hidden" name="return_url" value="" />
          </div>
        </div>
        <div class="toolsBox"></div>
      </div>
    </div>
    <div class="wall-compose-tray hequestion-compose-tray">

      <div class="hqAllowAddOptions">
        <input type="checkbox" name="can_add" id="hqAllowAddOptionsInput" checked="checked"/>
        <label for="hqAllowAddOptionsInput">
          <?php echo $this->translate('HEQUESTION_ALLOW_ADD_OPTIONS');?>
        </label>
      </div>

    </div>
    <div class="submitMenu">
      <button type="submit"><?php echo $this->translate('HEQUESTION_SUBMIT');?></button>

      <?php

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


      if (!empty($viewOptions)){
        $keys = array_keys($viewOptions);
        $privacy_active_key = $keys[0];
        $privacy_active = $viewOptions[$privacy_active_key];
      }


      ?>

      <?php if (!empty($viewOptions)):?>

      <div class="wall-privacy-container">
        <a href="javascript:void(0);" class="wall-privacy-link wall_tips wall_blurlink" title="<?php echo $this->translate($privacy_active);?>">
          <span class="wall_privacy">&nbsp;</span>
          <span class="wall_expand">&nbsp;</span>
        </a>
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

      <?php endif;?>

      <ul class="shareMenu"></ul>
    </div>


  </form>

</div>

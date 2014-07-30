<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editOptions.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<ul class="hqQuestions">

  <?php
  $paginator = $this->question->getOptions();
  ?>

  <?php foreach ($paginator as $option):?>

  <?php
  $is_active = ($option->getVote($this->viewer())) ? 1 : 0;
  if ($is_active){
    $voted = true;
  }
  ?>

  <li class="<?php if ($is_active):?>hqOptionActive<?php endif;?>">
    <div class="hqUserChoose">
      <div class="hqUserChooseControl">

        <?php if ($this->question->canVote($this->viewer())):?>
        <?php if ($this->question->isMulti()):?>
          <input type="checkbox" class="hqchecked" name="hqchecked_<?php echo $option->option_id?>" rev="<?php echo $option->option_id?>" <?php if ($is_active):?>checked="checked"<?php endif;?> disabled="disabled"/>
          <?php else :?>
          <input type="radio" class="hqselected" name="hqselected" value="<?php echo $option->option_id?>" <?php if ($is_active):?>checked="checked"<?php endif;?> disabled="disabled"/>
          <?php endif;?>
        <?php endif;?>

      </div>
      <div class="hqContent">
        <div class="hqContentTitle"><?php echo $option->title;?></div>
        <div class="hqContentVotes">
          <?php echo $this->translate(array('%s vote', '%s votes', $option->vote_count), $option->vote_count);?>
        </div>
      </div>
    </div>

    <div class="hqEditQuestionOptions">
      <a href="javascript:void(0)" class="hqEditQuestionDelete" rev="<?php echo $option->getIdentity();?>">&nbsp;</a>
    </div>

  </li>
  <?php endforeach;?>


</ul>


<a href="javascript:void(0);" class="hqEditDone wall-button"><?php echo $this->translate('HEQUESTION_Done Editing');?></a>
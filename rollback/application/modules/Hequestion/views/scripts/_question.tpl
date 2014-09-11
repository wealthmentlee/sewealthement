<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _question.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>
<?php
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hequestion/externals/scripts/core.js')
      //->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js')
  ;

$this->headTranslate(array(
  'HEQUESTION_OPTION_DELETE',
  'HEQUESTION_LIST_TITLE_VOTERS',
  'HEQUESTION_FRIENDS'
));

?>

<?php
  if (!$this->question){
    return ;
  }
  if (!$this->question->authorization()->isAllowed(null, 'view')){
    return ;
  }


?>

<?php
$voted = false;
?>


<?php if (!$this->only_content):?>

<?php
  $uid = uniqid('hq');
?>




<script type="text/javascript">
  en4.core.runonce.add(function() {
    if( !$type(Hequestion)) {
      Asset.javascript('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Hequestion/externals/scripts/core.js' ?>');
    }
    if( !$type(Wall)) {
      Asset.javascript('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js' ?>');
    }
    Wall.globalBind();

  });
</script>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    Hequestion.bindQuestion("<?php echo $uid;?>");
  });
</script>

<div id="<?php echo $uid;?>" class="hqQuestion <?php if ($this->show_all):?>hqShowAllOptions<?php endif;?>">


<?php endif;?>

  <ul class="hqQuestions">

    <?php

      $paginator = $this->question->getOptionPaginator();

      if ($this->show_all){
        $paginator->setItemCountPerPage(100);
      }

      $max = 0;

      foreach ($paginator as $option){
        if ($option->vote_count > $max){
          $max = $option->vote_count;
        }
      }

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
            <?php if ($this->question->canVote($this->viewer())): ?>
              <?php if ($this->question->isMulti()):?>
                <input type="checkbox" class="hqchecked" name="hqchecked_<?php echo $option->option_id?>" rev="<?php echo $option->option_id?>" <?php if ($is_active):?>checked="checked"<?php endif;?>/>
              <?php else :?>
                <input type="radio" class="hqselected" name="hqselected" value="<?php echo $option->option_id?>" <?php if ($is_active):?>checked="checked"<?php endif;?>/>
              <?php endif;?>
            <?php else :?>
              &nbsp;
            <?php endif; ?>
          </div>
          <div class="hqContent">

            <?php
              $option_percent = @floor($option->vote_count/$max*100);
            ?>

            <div class="hqContentCountLine" style="width: <?php echo $option_percent;?>%;"></div>
            <div class="hqContentTitle"><?php echo $option->title;?></div>
            <div class="hqContentVotes">
              <?php echo $this->translate(array('%s vote', '%s votes', $option->vote_count), $option->vote_count);?>
            </div>
          </div>
          <?php if ($option->vote_count):?>
            <div class="hqVoteUsers">
              <ul>

                <?php
                  $members = $option->getVoteMembers($this->viewer());

                ?>

                <?php foreach ($members as $voter):?>
                  <?php
                    $member = Engine_Api::_()->getItem('user', $voter->user_id);
                    if (!$member){
                      continue ;
                    }
                  ?>
                  <li>
                    <a href="<?php echo $member->getHref();?>" title="<?php echo addslashes($member->getTitle())?>">
                      <?php echo $this->itemPhoto($member, 'thumb.icon');?>
                    </a>
                  </li>
                <?php endforeach;?>

                <?php if (!empty($members->getPages()->next)):
                  $more_count = $members->getTotalItemCount()-$members->getCurrentItemCount();
                ?>
                  <li>
                    <a href="javascript:void(0);" class="hqLinkVoters wall_tips" rev="<?php echo $option->option_id;?>" title=<?php echo $this->jsonInline($this->translate(array('%s other people', '%s other peoples', $more_count), $more_count));?>>...</a>
                  </li>
                <?php endif;?>

              </ul>
            </div>
          <?php endif;?>
        </div>
      </li>
    <?php endforeach;?>


    <?php if (!empty($paginator->getPages()->next)):?>

      <li>
        <div class="hqUserChooseControl">&nbsp;</div>
        <a class="hqContentMore" href="<?php echo $this->question->getHref();?>" >
          <?php echo $this->translate('HEQUESTION_%s More', $paginator->getTotalItemCount()-$paginator->getCurrentItemCount());?>
        </a>

      </li>

    <?php endif;?>

    <?php if ($this->question->can_add && $this->viewer()->getIdentity()):?>

      <li>
        <div class="hqUserChoose">
          <div class="hqUserChooseControl">&nbsp;</div>
          <div class="hqAddAnswerC">
            <div class="hqAddAnswer">
              <form action="" class="hqAddAnswerForm">
                <input type="text" name="title" value="<?php echo $this->translate('HEQUESTION_ADD_ANSWER');?>" class="hqTextDisactive" rev="<?php echo $this->translate('HEQUESTION_ADD_ANSWER');?>" />
                <a href="javascript:void(0);" class="hqAddAsnwerSubmit wall-button wall_blurlink"><?php echo $this->translate('HEQUESTION_ADD_ANWER_SUBMIT');?></a>
              </form>
            </div>
          </div>
        </div>
      </li>

    <?php endif;?>

  </ul>


  <input type="hidden" name="question_id" value="<?php echo $this->question->getIdentity();?>" class="hq_question_id" />


  <ul class="hqLineOptions item_options">
    <?php if ($this->viewer()->getIdentity()):?>
    <li>
      <a href="javascript:void(0);" class="hqQuestionAskFriends"><?php echo $this->translate('HEQUESTION_ASK_FRIENDS');?></a>
    </li>
    <?php endif;?>
    <?php if ($this->viewer()->getIdentity() && !$this->question->isOwner($this->viewer())):?>
    <li>
      <span>&middot;</span>
        <span class="hqQuestionFollow <?php if ($this->question->isFollower($this->viewer())):?>hqQuestionIsFollower<?php endif;?>">
          <a href="javascript:void(0);" class="hqQuestionAddFollow"><?php echo $this->translate('HEQUESTION_FOLLOW');?></a>
          <a href="javascript:void(0);" class="hqQuestionRemoveFollow"><?php echo $this->translate('HEQUESTION_UNFOLLOW');?></a>
        </span>
    </li>
    <?php endif;?>
  </ul>


<?php if (!$this->only_content):?>

</div>

<?php endif;?>
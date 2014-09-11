<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-16 10:11:40 michael $
 * @author     Michael
 */

?>

<?php if ($this->paginator->getTotalItemCount() > 0):?>

  <h4><?php echo $this->translate('Questions:')?></h4>

  <ul class="items">

    <?php foreach ($this->paginator as $question): ?>

      <li>

        <div class="item_body">
          <div class="item_title">
            <?php echo $this->htmlLink($question->getHref(), $question->getQuestion(100)); ?>
          </div>
          <div class="item_date">
            <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($question->creation_date) ?>
            <?php
              $tmp_categories = $this->categories->getRowMatching('category_id', $question->category_id);
              if (is_object($tmp_categories)) echo $this->translate('in %s -', $tmp_categories->category_name);
            ?>
            <?php echo $this->translate('%s views -', $question->question_views) ?>
            <?php echo $this->translate('%d total answers', $question->count_answers)?>
          </div>
        </div>

      </li>

    <?php endforeach; ?>


  </ul>

  <?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
    <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'user_questions'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
  <?php endif;?>

<?php else: ?>

  <?php echo $this->translate('Question entries not found.'); ?>

<?php endif;?>


<?php if ($this->paginator_answer->getTotalItemCount() > 0):?>

  <h4><?php echo $this->translate('Answers:')?></h4>

  <ul class="items">

  <?php foreach( $this->paginator_answer as $answer ): ?>

    <li>
      <div class="item_body">

        <div class="item_title">
          <?php echo $this->htmlLink($answer->getQuestionHref(), $answer->getShortAnswer(100)); ?>
        </div>

        <div class="item_date">
          <?php echo $this->translate('Posted %s -', $this->timestamp($answer->creation_date)) ?>
          <?php echo $this->translate('Votes:')?> +<span><?php echo $answer->getVotes('+') ?></span>, -<span><?php echo $answer->getVotes('-') ?></span>
        </div>

      </div>

    </li>

  <?php endforeach; ?>

  </ul>

  <?php if($this->paginator_answer->getTotalItemCount() > $this->items_per_page):?>
    <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'answers'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
  <?php endif;?>

<?php else: ?>

  <?php echo $this->translate('Answer entries not found.'); ?>

<?php endif;?>

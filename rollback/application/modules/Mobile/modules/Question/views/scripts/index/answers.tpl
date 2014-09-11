<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: answers.tpl 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

?>
<h4>
  &raquo; <?php echo $this->question_title ?>
  <?php if (isset($this->owner)): ?>
    &raquo; <?php echo $this->translate('%1$s\'s Answers', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  <?php endif; ?>
</h4>

<?php if( count($this->paginator) > 0 ): ?>

<ul class='items'>

  <?php foreach( $this->paginator as $answer ): ?>

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

  <?php endforeach ;?>

</ul>

<?php else: ?>
  <div class="tip">
    <?php echo $this->translate('Answer entries not found..'); ?>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array('query' => array('search' => $this->search, 'user' => $this->user))); ?>


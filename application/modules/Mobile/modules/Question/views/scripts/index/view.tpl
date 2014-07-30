<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

?>


<h4>
  &raquo; <a href="<?php echo $this->url(array('module' => 'question'), 'default', true)?>"><?php echo $this->question_title ?></a>
  &raquo; <?php echo $this->translate('Question')?>
</h4>

<div class="layout_content">
<ul class="items subcontent">
	<li>
		<div class="item_photo">
			<?php echo $this->htmlLink($this->question->getOwner()->getHref(), $this->itemPhoto($this->question->getOwner(), 'thumb.icon')) ?>
		</div>
		<div class="item_body">
			<?php echo $this->question->getQuestion() ?>
			<h4>
				<div class="item_date" style="font-weight:normal; font-size: 0.9em;">
          <?php echo $this->translate('Posted by %s about %s', $this->question->getOwner()->toString(), $this->timestamp($this->question->creation_date)) ?>
          <?php
            $tmp_categories = $this->categories->getRowMatching('category_id', $this->question->category_id);
            if (is_object($tmp_categories)) echo $this->translate('in %s -', $tmp_categories->category_name );
          ?>
          <?php echo $this->translate('%s views -', $this->question->question_views) ?>

          <?php $count_answers = (isset ($this->best_answer)) ? $this->paginator->getTotalItemCount() + 1 : $this->paginator->getTotalItemCount();
          echo $this->translate('%d total answers', $count_answers) . '. ' . $this->translate('Status: %s. ', $this->question->status)
          ?>
				</div>
			</h4>

		</div>
	</li>

	<li style="border-top: 0px;background:none;">
			<div class="item_body">

      <?php if( $this->message ): ?>
        <div class='result_message'><?php echo $this->message ?></div>
      <?php endif; ?>

      <?php if ($count_answers > 0):?>

        <?php
          $count_answers = (isset ($this->best_answer))
              ? $this->paginator->getTotalItemCount() + 1
              : $this->paginator->getTotalItemCount();
        ?>


         <h4><?php echo $this->translate('Answers')?> <span>(<?php echo $this->translate('%d', $count_answers)?>)</span></h4>

         <ul class="items">

           <?php if (isset($this->best_answer)):?>

           <li>
             <div class="item_photo">
              <?php echo $this->htmlLink($this->best_answer->getOwner()->getHref(), $this->itemPhoto($this->best_answer->getOwner(), 'thumb.icon')) ?>
             </div>

             <div class="item_body">

               <div class="qa_best_answer"><?php echo $this->translate('Best answer')?></div>
               <?php echo nl2br($this->best_answer->answer); ?>

               <div class="item_date">
                 <?php echo $this->translate('Posted by %s about %s.', $this->best_answer->getOwner()->toString(), $this->timestamp($this->best_answer->creation_date)) ?>
               </div>

             </div>

           </li>

          <?php endif;?>

           <?php foreach($this->paginator as $answer):?>

              <li>

                <div class="item_photo">
                  <?php echo $this->htmlLink($answer->getOwner()->getHref(), $this->itemPhoto($answer->getOwner(), 'thumb.icon')) ?>
                </div>

                <div class="item_body">

                  <?php if ($this->can_choose_answer): ?>
                    <?php echo $this->htmlLink(array('route' => 'choose', 'best_id' => $answer->answer_id), $this->translate('choose as a best answer')) ?><br />
                  <?php endif; ?>

                  <?php echo nl2br($answer->answer); ?>

                  <div class="item_date">

                    <?php echo $this->translate('Posted by %s about %s -', $answer->getOwner()->toString(), $this->timestamp($answer->creation_date)) ?>
                    <?php echo $this->translate('Votes:')?> +<span><?php echo $answer->getVotes('+') ?></span>, -<span><?php echo $answer->getVotes('-') ?></span>

                    <?php if (Engine_Api::_()->question()->can_delete_answer($answer) === true): ?>
                      -
                      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'question', 'controller' => 'index', 'action' => 'delete', 'id' => $answer->answer_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'Delete') ?>
                    <?php endif; ?>

                  </div>

                </div>

              </li>


           <?php endforeach;?>

         </ul>

        <?php if ($this->paginator->count() >= 1): ?>
          <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
        <?php endif;?>

      <?php else:?>

        <div class="result_message">
          <?php echo $this->translate('No answers yet. Be the first!'); ?>
        </div>

      <?php endif;?>

        <div>
          <?php if ($this->can_answer === 0):?>
            <?php echo $this->createanswer->render($this)?>
          <?php else:?>
            <div class="result_message">
              <?php echo Engine_Api::_()->question()->getstatus_message($this->can_answer)?>
            </div>
          <?php endif;?>
        </div>

			</div>
	</li>
</ul>


<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

?>
<h4>
  &raquo; <?php echo $this->question_title ?>
  <?php if ($this->userObj && $this->userObj->getIdentity()):?>&raquo; <?php echo $this->userObj->__toString()?><?php endif;?>
</h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->active):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">
					<div	class="search">
						<?php echo $this->form->render($this) ?>
					</div>

						<?php if( count($this->paginator) > 0 ): ?>

						<ul class='items'>

              <?php foreach( $this->paginator as $question ): ?>

                <li>

                  <div class="item_photo">
                    <?php echo $this->htmlLink($question->getOwner()->getHref(), $this->itemPhoto($question->getOwner(), 'thumb.icon')) ?>
                  </div>

                  <div class="item_body">

                    <div class="item_options">
                        <?php if ($this->can_delete_question && false): ?>
                          <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'question', 'controller' => 'index', 'action' => 'deleteq', 'id' => $question->question_id), 'Delete') ?>
                        <?php endif; ?>
                    </div>

                    <div class="item_title">
                      <?php echo $this->htmlLink($question->getHref(), $question->getQuestion(100)); ?>
                    </div>

                    <div class="item_date">

                   <?php echo $this->translate('Posted by %s about %s', $question->getOwner()->toString(), $this->timestamp($question->creation_date)) ?>
                        <?php
                            $tmp_categories = $this->categories->getRowMatching('category_id', $question->category_id);
                            if (is_object($tmp_categories))
                                    echo $this->translate('in %s -',  $tmp_categories->category_name );
                        ?>
                  <?php echo $this->translate('%s views -', $question->question_views) ?>

                        <?php echo $this->translate('%d total answers', $question->count_answers) . '. '?>
                        <?php echo $this->translate('Status: %s. ', $question->status)?>


                    </div>

                  </div>

                </li>

              <?php endforeach ;?>

						</ul>

						<?php else: ?>
							<div class="tip">
                <?php echo $this->translate('Nobody has written a question entry yet.'); ?>
                <?php if ($this->can_create): // @todo check if user is allowed to create a poll ?>
                  <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create')).'">', '</a>'); ?>
                <?php endif; ?>
							</div>
						<?php endif; ?>

						<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array('query' => array('search' => $this->search, 'user_id' => $this->user_id))); ?>

				</li>

				<?php else: ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<?php endif; ?>

			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
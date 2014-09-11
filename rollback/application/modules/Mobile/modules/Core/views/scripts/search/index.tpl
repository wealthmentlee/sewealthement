<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>&raquo;  <?php echo $this->translate('Search'); ?> </h4>

<div id="searchform" class="global_form_box">
  <?php echo $this->form->setAttrib('class', '')->render($this) ?>
</div>
<div class="clr"></div>

<div class="layout_content">
<?php if( empty($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please enter a search query.') ?>
    </span>
  </div>
<?php elseif( $this->paginator->getTotalItemCount() <= 0 ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results were found.') ?>
    </span>
  </div>
<?php else: ?>
  <?php echo $this->translate(array('%s result found', '%s results found', $this->paginator->getTotalItemCount()),
                              $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>

	<ul class="search_result items">
		<?php foreach( $this->paginator as $item ):
    $item = $this->item($item->type, $item->id);
    if( !$item ) continue; ?>
		<li>
      <div class="item_photo" style="display:block; width:50px ; height:50px; ">
				<div class="search_photo">
        	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
				</div>
      </div>
      <div class="item_body">
        <?php if( '' != $this->query ): ?>
          <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
        <?php else: ?>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
        <?php endif; ?>
        <p>
          <?php if( '' != $this->query ): ?>
            <?php echo $this->highlightText($this->mobileSubstr($item->getDescription()), $this->query); ?>
          <?php else: ?>
            <?php echo $this->mobileSubstr($item->getDescription()); ?>
          <?php endif; ?>
        </p>
      </div>
  	</li>
  	<?php endforeach; ?>

	</ul>
  <br />


  <div>
    <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array(
      //'params' => array(
      //  'query' => $this->query,
      //),
      'query' => array(
        'query' => $this->query,
        'type' => $this->type,
      ),
    )); ?>
  </div>
<?php endif; ?>

</div>
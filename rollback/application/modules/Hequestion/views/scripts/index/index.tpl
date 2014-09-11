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

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="hequestions_browse">
  <?php foreach( $this->paginator as $item ): ?>
  <li>
    <div class='hequestions_browse_photo'>
      <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
    </div>
    <div class='hequestions_browse_info'>
          <span class='hequestions_browse_info_title'>
            <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
          </span>
      <div class="item_date">
        <?php echo $this->translate(array('%s vote', '%s votes', $item->vote_count), $item->vote_count);?>
        <span>&middot;</span>
        <?php echo $this->translate(array('%s follower', '%s followers', $item->follower_count), $item->follower_count);?>
        <span>&middot;</span>
        <?php echo $this->translate('Posted');?>
        <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
        <?php echo $this->translate('by');?>
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>


<?php else:?>
<div class="tip">
    <span>
      <?php echo $this->translate('HEQUESTION_Nobody has created a question yet.'); ?>
    </span>
</div>
<?php endif; ?>


<br />

<?php if ($this->paginator->count() > 1): ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hequestion"), array(
    'ajax_url' => $this->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'index', 'content' => 1), 'default', true),
    'ajax_class' => 'layout_core_content'
  ))?>
<br />
<?php endif?>
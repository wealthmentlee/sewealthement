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


<ul class="hqBrowseQuestions">
  <?php foreach ($this->paginator as $item):?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
      </div>
      <div class="item_body">
        <div class="item_title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
        <div class="item_date">
          <?php echo $this->translate(array('%s vote', '%s votes', $item->vote_count), $item->vote_count);?>
          <span>&middot;</span>
          <?php echo $this->translate(array('%s follower', '%s followers', $item->follower_count), $item->follower_count);?>
          <span>&middot;</span>
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
          <?php echo $this->translate('by');?>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      </div>
      <?php if ($item->canRemoveLink($this->viewer()) && ($this->subject() && $this->subject()->getType() != 'user')):?>
        <div class="item_options">
          <a href="javascript:void(0);" class="buttonlink hequestion_link_remove" onclick="(new Request.JSON({url: en4.core.baseUrl+'hequestion/index/remove-link/format/json/question_id/<?php echo $item->getIdentity();?>', onComplete: function (){window.location.reload();}})).send();"><?php echo $this->translate('HEQUESTION_REMOVE_LINK');?></a>
        </div>
      <?php endif;?>
    </li>
  <?php endforeach;?>
</ul>



<br />

<?php if ($this->paginator->count() > 1): ?>
<?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hequestion"), array(
    'ajax_url' => $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'hequestion.profile-questions', 'subject' => $this->subject()->getGuid()), 'default', true),
    'ajax_class' => 'layout_hequestion_profile_questions',
    'mini' => true
  ))?>
<br />
<?php endif?>
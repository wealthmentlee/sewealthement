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

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_discussion', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_DISCUSSION')) ?>
</h4>



<?php if (count($this->paginator)):?>

  <?php if ($this->canCreate): ?>
    <div class="mobile_box" style="padding-bottom:0;">
      <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>"><?php echo $this->translate('PAGEDISCUSSION_CREATE')?></a>
    </div>
  <?php endif;?>

  <ul class="items mobile_box">

    <?php foreach ($this->paginator as $topic):?>

      <li>

        <div class="item_body">

          <a href="<?php echo $this->url(array('action' => 'topic', 'topic_id' => $topic->getIdentity()), 'page_discussion', true)?>">

            <?php if ($topic->sticky):?>
              <?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pagediscussion/externals/images/stick.png')?>
            <?php endif;?>
            <?php if ($topic->closed):?>
              <?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Pagediscussion/externals/images/close.png')?>
            <?php endif;?>

            <?php echo $topic->getTitle()?>
          </a>


          <div class="item_date">

            <?php echo $this->locale()->toNumber($topic->getCountPost())?>
            <?php echo $this->translate(array('reply', 'replies', $topic->getCountPost())) ?>
            /
            <?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
          </div>

          <?php echo $this->mobileSubstr($topic->getDescription())?>

        </div>

      </li>

    <?php endforeach;?>

  </ul>

  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
  <?php endif; ?>

<?php else:?>

  <div class="result_message">

    <?php echo $this->translate('PAGEDISCUSSION_NOTOPIC');?>
    <?php if ($this->canCreate):?>
      <?php echo $this->translate('MOBILE_PAGEDISCUSSION_NOTOPIC_CREATE',  array($this->url(array('action' => 'create', 'page_id' => $this->subject->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI']), true), 'page_discussion'))); ?>
    <?php endif; ?>

  </div>

<?php endif?>
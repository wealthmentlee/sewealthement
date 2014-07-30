<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contacts.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if ($this->error): ?>
<div class="contacts_error"><?php echo $this->message; ?></div>
<?php else: ?>

<div class="he_contacts">
  <?php if ($this->title): ?>
  <h4 class="contacts_header"><?php echo $this->title; ?></h4>
  <?php endif; ?>

  <?php /* if (isset($this->items) && $this->items->getCurrentItemCount() > 0): ?>
  <form action="<?php echo $this->url(array('module' => 'hecore', 'controller' => 'index', 'action' => 'contacts'), 'default'); ?>" method="post" name="filter_form">
    
  <?php foreach($this->params as $key => $value): ?>
    <input type="hidden" name="params[<?php echo $key; ?>]" value="<?php echo $value; ?>" />
  <?php endforeach; ?>
    <input type="hidden" name="action_url" value="<?php echo $this->action_url; ?>" />
    <input type="hidden" name="page" value="<?php echo $this->p; ?>" />
    <input type="hidden" name="l" value="<?php echo $this->list; ?>" />
    <input type="hidden" name="m" value="<?php echo $this->module; ?>" />
    
    <div class="options">
      
      <div class="contacts_filter">
        <div class="list_filter_cont">
          <input type="text" class="list_filter" title="Search" id="contacts_filter" name="q" />
          <button class="list_filter_btn" type="submit" id="contacts_filter_submit" title="Search"><?php echo $this->translate("Search"); ?></button>
        </div>
      </div>
      
      <div class="clr"></div>
    </div>
    
    <div class="clr"></div>
    
  </form>
  <?php endif; */ ?>

  <form action="<?php echo $this->action_url; ?>" method="post" name="submit_form">
    <div class="contacts">
      <ul id="he_contacts_list" class="items">
        <?php if ($this->items && $this->items->getCurrentItemCount() > 0): ?>
        <?php foreach ($this->items as $item): ?>
          <?php $itemDisabled = in_array($item->getIdentity(), $this->disabledItems); ?>
          <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
          <li>
            <div class='check'>
              <?php if (!$itemDisabled): ?>
              <input type="checkbox" value="<?php echo $item->getIdentity(); ?>" name="contacts[]"/>
              <?php else: ?>
              
              <?php endif; ?>
            </div>

            <div class='item_photo'>
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')); ?>
            </div>

            <div class="item_body">
              <div class="item_title">
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
              </div>
            </div>

            <div class="clr"></div>
          </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>

      <?php if ($this->items->count() > 0): ?>
      <?php echo $this->paginationControl($this->items, null, array('pagination/search.tpl', 'mobile'), array('query' => $this->query, 'pageAsQuery' => true)); ?>
      <?php endif; ?>

      <div class="clr"></div>
    </div>
    <div class="clr"></div>

    <div class="btn" style="width: 450px; line-height: 28px;">
      <button type="submit" style="float:left;">
        <?php echo $this->translate((isset($this->params['button_label'])) ? $this->params['button_label'] : "Send"); ?>
      </button>
      <div class="clr"></div>
    </div>

    <?php foreach ($this->params as $key => $value): ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
    <?php endforeach; ?>

  </form>

</div>

<?php endif; ?>
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
  
<?php
  if (is_array($this->items)) {
    $this->potentialItems = !empty($this->items['potential']) ? $this->items['potential'] : array();
    $this->items = !empty($this->items['all']) ? $this->items['all'] : $this->items;
  }

  if ($this->items instanceof Zend_Paginator):
?>

<div class="he_contacts">
  <?php if ($this->title): ?>
    <h4 class="contacts_header"><?php echo $this->title; ?></h4>
  <?php endif; ?>

  <div class="clr"></div> 

  <form action="<?php echo $this->action_url; ?>" method="post" name="submit_form">
  <div class="contacts">

    <?php echo $this->render('_contacts_items.tpl'); ?>

    <?php if ($this->items->count()): ?>
      <?php echo $this->paginationControl($this->items, null, array('pagination/search.tpl', 'mobile'), array('query' => $this->query, 'pageAsQuery' => true)); ?>
    <?php endif; ?>
    <div class="clr"></div>
    
  </div>

  <div class="clr"></div>

  <div class="btn" style="width:450px">
    <button id="submit_contacts" style="float:left;"><?php echo $this->translate((isset($this->params['button_label']))?$this->params['button_label']:"Send"); ?></button>
    &nbsp;<?php echo $this->translate('or'); ?>&nbsp;
    <a href="<?php echo $this->return_url; ?>"><?php echo $this->translate('cancel'); ?></a>
  </div>

  <?php foreach($this->params as $key => $value): ?>
  <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>" />
  <?php endforeach; ?>
  <input type="hidden" name="return_url" value="<?php echo $this->return_url; ?>" />
  </form>
  
</div>

<?php endif; ?>
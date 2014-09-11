<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: pagination.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php echo $this->partial('items/_items.tpl', null, array('items' => $this->items))?>

<?php if (!count($this->items)):?>
  <li class="message">
    <div>
      <?php if (empty($this->search)):?>
        <span><?php echo $this->translate('WALL_ITEMS_EMPTY')?></span>
      <?php else: ?>
        <span><?php echo $this->translate('WALL_ITEMS_EMPTY_SEARCH')?></span>
      <?php endif;?>
    </div>
  </li>
<?php endif?>
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

<?php echo $this->partial('list/_items.tpl', null, array('items' => $this->browse))?>

<li class="message" style="<?php if (!empty($this->browse)):?>display:none<?php endif?>">
  <div>
    <?php if (empty($this->search)):?>
      <span><?php echo $this->translate('WALL_LIST_EMPTY')?></span>
    <?php else: ?>
      <span><?php echo $this->translate('WALL_LIST_EMPTY_SEARCH')?></span>
    <?php endif;?>
  </div>
</li>
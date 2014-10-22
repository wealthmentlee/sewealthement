<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-10 16:03 ratbek $
 * @author     Ratbek
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
    <li>
        <a href="<?php echo $this->url(array('page'=>$this->previous), 'page_browse_sort'); ?>"
           onclick="ReviewManager.setPage(<?php echo $this->previous;?>); return false;"><?php echo $this->translate('&#171; Previous');?></a>
      </li>
    <?php endif; ?>
		
    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a href="<?php echo $this->url(array('page'=>$page), 'page_browse_sort'); ?>"
           onclick="ReviewManager.setPage(<?php echo $page;?>); return false;" href="javascript:void(0)"><?php echo $page; ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
    	<li>
        <a href="<?php echo $this->url(array('page'=>$this->next), 'page_browse_sort'); ?>"
           onclick="ReviewManager.setPage(<?php echo $this->next;?>); return false;"><?php echo $this->translate('Next &#187;');?></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>
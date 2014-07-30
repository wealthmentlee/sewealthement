<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2011-02-14 12:38:39 michael $
 * @author     Michael
 */

?>

<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
      <h3><?php echo $this->translate('Delete Article?');?></h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the article with the title "<a href="%3$s">%1$s</a>" last modified %2$s? It will not be recoverable after being deleted.', $this->article->title,$this->timestamp($this->article->modified_date),$this->article->getHref()); ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="true"/>
        <button type='submit'><?php echo $this->translate('Delete');?></button>
        <?php echo $this->translate('or');?> <a href='<?php echo $this->url(array(), 'article_manage', true) ?>'><?php echo $this->translate('cancel');?></a>
      </p>
    </div>
    </div>
  </form>
</div>
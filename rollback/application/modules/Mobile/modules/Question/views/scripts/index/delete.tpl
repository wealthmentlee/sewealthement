<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

?>

<?php if( @$this->messages ): ?>
  <p><?php echo $this->messages ?></p>
 <?php else:?>
<form method="post" class="global_form">
    <div>
      <h3><?php echo $this->delete_title ?></h3>
      <p>
        <?php echo $this->delete_description ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->category_id?>"/>
        <button type='submit'><?php if (isset($this->button)) echo $this->button; else echo 'Delete'; ?></button>
        or <a href='<?php echo $this->return_url?>'>cancel</a>
      </p>
    </div>
  </form>
<?php endif; ?>


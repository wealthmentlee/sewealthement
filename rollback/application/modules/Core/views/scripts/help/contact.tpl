<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: contact.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<!-- @modified by Gitesh Dang -->
<style>
    #contact_form .form-label {
        display: none;
    }
	#category{
        width: 490px;
		max-width: 490px;
    }
	#body {
        width: 486px;
		max-width: 486px;
    }
</style>
<?php if( $this->status ): ?>
  <?php echo $this->message; ?>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
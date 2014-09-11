<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: upload.tpl 9747 2012-07-26 02:08:08Z john $
 * @author	   John
 */
?>

<h2>
    <?php echo $this->group->__toString() ?>
    <?php echo $this->translate('&#187; Photos');?>
</h2>

<?php echo $this->form->render($this) ?>
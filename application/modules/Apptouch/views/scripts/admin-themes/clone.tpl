<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: clone.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */
?>
<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_themes')) ?>
<h3 class="sep"><?php echo $this->translate("APPTOUCH_Theme_Editor") ?></h3>

<div class="settings">
<?php echo $this->form->render($this) ?>
</div>
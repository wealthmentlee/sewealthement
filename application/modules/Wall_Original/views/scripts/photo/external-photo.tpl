<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: external-photo.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<div style="padding: 10px;">
  <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

  <?php echo $this->itemPhoto($this->photo) ?>
</div>
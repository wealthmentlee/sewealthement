<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Changes saved!") ?></div>

  <script type="text/javascript">
    setTimeout(function() {
      parent.Smoothbox.close();
      parent.window.location.replace( parent.window.location.href )
    }, 500);
  </script>

<?php endif; ?>
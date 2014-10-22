<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2012-10-01 17:53 taalay $
 * @author     TJ
 */

$this->headLink()
    ->appendStylesheet($this->baseUrl().'/application/modules/Rate/externals/styles/main.css');

?>

<?php if ($this->redirect):?>

<script type="text/javascript">
  parent.window.location.replace( parent.window.location.href );
  parent.Smoothbox.close();
</script>

<?php endif;?>

<div style="padding:10px;"><?php echo $this->form?></div>
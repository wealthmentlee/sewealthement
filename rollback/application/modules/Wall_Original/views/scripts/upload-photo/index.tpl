<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php if ($this->save):?>

<script type="text/javascript">
  en4.core.runonce.add(function (){
    if (window.parent){
      if (window.parent.wallUploadPhoto){
        window.parent.wallUploadPhoto('<?php echo $this->itemPhoto($this->viewer, 'thumb.profile');?>');
      }
      window.parent.Smoothbox.close();
    }
  });
</script>

<?php endif;?>

<div style="padding:20px;">
  <?php echo $this->form->render();?>
</div>
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: deleted-item.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">

  Wall.runonce.add(function (){
    
    parent.$('activity-item-<?php echo $this->action_id ?>').destroy();
    setTimeout(function()
    {
      parent.Smoothbox.close();
    }, <?php echo ( $this->smoothboxClose === true ? 1000 : $this->smoothboxClose ); ?>);

  });
  
</script>


  <div class="global_form_popup_message">
    <?php echo $this->message ?>
  </div>
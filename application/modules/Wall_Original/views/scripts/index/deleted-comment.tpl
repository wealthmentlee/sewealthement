<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: deleted-comment.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    parent.$('comment-<?php echo $this->comment_id ?>').destroy();
    setTimeout(function()
    {
      parent.Smoothbox.close();
    }, 1000 );

  });
  
</script>

  <div class="global_form_popup_message">
    <?php echo $this->message ?>
  </div>
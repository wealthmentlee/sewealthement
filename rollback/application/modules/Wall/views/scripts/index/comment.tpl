<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: comment.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<p><?php echo $this->message ?></p>
<script type="text/javascript">

  Wall.runonce.add(function (){
    parent.en4.activity.viewComments(<?php echo $this->action_id ?>);
    parent.Smoothbox.close();
  });
  
</script>
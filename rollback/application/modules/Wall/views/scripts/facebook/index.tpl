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
<script type="text/javascript">

  window.addEvent('load', function (){

    <?php if ($this->tokenRow):?>

      window.opener.Wall.services.get('<?php echo $this->tokenRow->provider?>').setServiceOptions(<?php echo $this->jsonInline(array_merge($this->tokenRow->publicArray(), array('enabled' => $this->tokenRow->check(), 'fb_pages' => $this->fb_pages)))?>);
      window.opener.Wall.applyAll(function (item){
        item.fireEvent('<?php echo $this->task?>', ['<?php echo $this->tokenRow->provider?>']);
      });

    <?php endif;?>

    window.close();

  });

</script>
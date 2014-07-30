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
<style type="text/css">
    #photoviewer-label {
        display: none;
    }
</style>
<h2>
  <?php echo $this->translate('WALL_ADMIN_MAIN_HEADER_TITLE')?>
</h2>

<p>
  <?php echo $this->translate('WALL_ADMIN_MAIN_HEADER_DESCRIPTION');?>
</p>

<br />

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div class="settings">

  <div style="overflow: hidden;">
    <?php echo $this->form->render()?>
  </div>

</div>

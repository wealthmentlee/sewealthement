<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: placeholder.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<h2><?php echo $this->translate("HEQUESTION_ADMIN_HEQUESTION_TITLE") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

  <div class='clear'>
    <div class='settings'>

      <?php echo $this->message; ?>

    </div>
  </div>
